# Bigbigads

Bigbigads WEB APP。

## 首次配置开发环境
进入Bigbigads工程根目录，先将工程的依赖库补全及生成目标文件，这些由于体积很大（依赖库）或者不适合仓库管理（自动生成的目标文件，每次变更都产生大量change，会导致merge冲突和增加审查成本)。

```
$composer update
$cp .env.example .env
$php artisan key:generate
$cd public
$npm install
$bower install
$npm run product
```

复制`.env.example`为`.env`。

`.env`：这里面主要包含AppID,数据库配置、SMTP配置、缓存配置、调试配置以及本项目需要的其他配置。在实践上`.env`通常包含敏感信息（AppID及数据库配置）以及跟本地环境强相关（每个机器的数据库可能都不一样），因此不会将它包含进仓库，而是提供`.env.example`作为范例。`.env`文件在`Laravel 5.3`下有详细的说法明，请自行了解。同时，本项目的配置说明直接查看**`.env.example`**，本项目独有的配置项均有说明。


然后配置`nginx`或者`apache`,将网站根目录定位到`public/`，同时允许`URL rewrite`。配置就完成了。
## 生产环境与开发环境
两个环境有很大的差异，开发时**务必**使用开发环境，以方便调试；实际上线则必须使用生产环境。
两者的区别：

1. 开发环境下生成的目标文件都带`sourcemap`，同时不压缩，命名固定;
2. 生产环境下生成的目标文件(`HTML`,`CSS`,`JS`)全部都是压缩处理的，同时每次命名都会改变，因此在生产环境下，只在有提交时才再次生成，不要定时生成；在没做修改的情况下，用户访问缓存文件会更快。

### 如何配置生产环境
在`public`目录下，执行

```
$npm run product
```

然后在工程根目录下，修改`.env`文件，核对以下字段是否设置同样的值

```
APP_ENV=production
APP_DEBUG=false
```

### 如何配置开发环境
在`public`目录下，执行

```
$npm run develop
``` 

然后在工程根目录下，修改`.env`文件，核对以下字段是否设置同样的值

```
APP_ENV=local
APP_DEBUG=true
```

如果要修改`src`目录下的文件，除了`src/index.html`之外，其他`js`,`html`以及`scss`都可以实时监听。**修改前**单独开一个窗口，执行如下命令：

```
$webpack -w
```

会方便调试。如果改的是`src/index.html`或者其他像`json`之类的文件，仍然需要执行`npm run devleop`重新生成。

如果出现`eslint`提示不符合排版要求的错误，可单独开个窗口先做下自动修复，执行：

```
npm run fixlint
```

在开发时，请**一定**配合`Chrome Devtool`做调试。


## 权限配置指南
[参考对应WIKI:权限配置指南](http://git.papamk.com:81/bigbigads/bigbigads/wikis/%E6%9D%83%E9%99%90%E9%85%8D%E7%BD%AE%E6%8C%87%E5%8D%97)

## Paypal的支持
项目已经集成了`Paypal`，目前只实现了`Paypal`帐号的循环扣款。基于信用卡的支付、一次性扣款、`Webhook`消息处理均未实现。

项目使用[paypal/Paypal-PHP-SDK](https://github.com/paypal/PayPal-PHP-SDK)提供的包做Paypal开发，开发包用法、用例和Api说明请直接上该项目查看。

要使用`Paypal`，需要先修改`.env`修改（没有则在末尾增加）相关配置，主要配置项如下：

```
PAYPAL_APPID=bigbigads   # App ID，实际没用上                      
PAYPAL_CLIENT_ID=XXXXXXX # Paypal App ClientID
PAYPAL_CLIENT_SECRET=XXXXXXX # Paypal App Secret
PAYPAL_WEBHOOK=https://phenye.tunnel.2bdata.com/onPayWebhook # Webhook用于接收支付消息，比如支付成功，过期等。这个需要在Paypal开发者平台的webhook先做配置，然后再填到这里来，两者名称必须一致，同时要求必须是https协议。请将域名换成自己的，后面的onPayWebhook应保留不变                  
PAYPAL_RETURNURL=https://phenye.tunnel.2bdata.com/onPay # 回调接口，在支付的时候需要先跳到Paypal的网站上完成支付，然后Paypal将跳回该回调地址完成最后操作。请将域名换成自己的，onPay保持不变。
PAYPAL_MODE=sandbox
```

> 开发注意：Paypal的订阅一旦生效，不会自动取消。因此当用户切换升级计划，也就是切换不同的升级计划时，开发上必须主动将前一个订阅`挂起`或者`取消`，否则就会出现多个订阅同时在扣用户款的情况。

## subscriptions配置

`subscriptions`原来在数据库中有残留，因此当执行数据库迁移时不一定能成功，这时手动检查下数据表：

-  `subscriptions`表格是否存在，有就删除：

```
drop table subscriptions
```

-  `users`表格的字段：
`stripe_id`,`braintree_id`,`subscription_id`, `paypal_email`, `card_brand`, `card_last_four`, `trial_ends_at`，手动删除。

然后看下面一节。
## plans（升级计划）配置指南

分三步：

1. 迁移plans数据库
2. 修改数据库填充文件`PlansSeeder.php`，该文件包含三部分：填充`plans`表格，将角色与plans绑定以及将plans同步到`Paypal`.
3. 执行填充

### 迁移

迁移文件位于`2017_03_18_230500_create_plans_table.php`，执行下面这条命令即可完成迁移，同时查看下本地数据库是否多了张`plans`的表。

```
php artisan migrate
```

> 该命令只需要执行一次，作用是往数据库创建表，如果你需要修改迁移内容，请先阅读下`Laravel`的迁移教程：
> [http://laravelacademy.org/post/6171.html](http://laravelacademy.org/post/6171.html)

如果`plans`做了修改，需要重新迁移，那么需要先执行回滚将`plans`删除先做迁移。

```
php artisan migrate:rollback
```

### 修改`PlansSeeder.php`

数据库填充文件位于`database/seeds/PlansSeeder.php`：

该文件包含三块内容：

1. 填充`plans`，比如Free, Start,Standard,Advanced,VIP等升级计划的信息，主要有价格和收费周期等。

2. 将角色与`plans`绑定。从业务角度上看，不同的plan应该对应到不同的权限上；但是在设计上，我们的权限模型是`RBAC`，所有的权限都分配到指定的角色上。因此不同的`plan`要对应不同的权限，就应该将不同的`plan`绑定到不同的`role`上。当`user`的`plan`改变时，`user`的`role`也应该跟着改变。基于以上原因：`plans`表格都有一个`role_id`的字段，以便用户完成支付后获取该计划所属的角色，并做变更。但是`roles`表格也有一个`plan`的字段，这实际上是由于早期设计上通过第三方`Braintree`保存`plans`（也就是本地没有`plans`表格），所以需要通过`roles`去获取对应的`plans`导致的，在将来**重新设计支付系统时这块将被完全抛弃**。
3. 	将升级计划信息同步到`Paypal`，以便在支付的时候Paypal能知道我们想各个升级计划的具体情况（价格，税收，有效期，循环周期等），以便实现循环支付。

### 执行填充
执行如下命令将初始化跟升级计划相关的数据，因此每次修改了该文件都应该重新执行这条命令。

```
php artisan db:seed --class=PlansSeeder
```

> 可对计划内容作任意修改，不会影响到已经支付的用户。
> 
> 如果是在生产环境下，要重新填充初始化是不可逆的，出于安全性考虑，**请务必备份好数据库**。

## 后台管理与博客
同步后，先执行`composer update`更新下包，然后执行下面命令重新填充权限：

```
php artisan db:seed --class=BigbigadsSeeder
```
后台配置便完成了。


后台地址：`http://<你的域名>/admin`

### 如何登陆？

请自己注册个帐号，然后执行下面命令该帐户提升为后台管理员

```
php artisan voyager:admin  <你的email帐号>
```

> 前后台的帐号不可混用，如果你在前台已经登陆了，进入后台会出错；所以必须先退出前台的帐户，然后再进后台。
> 反之，如果你登陆了后台，然后再去查看前台，就会得到各种错误，这块暂时没有好方法解决。

### 如何修改用户角色与激活用户？
后台可以修改用户的信息，包括是否激活用户，但是**修改角色并不会重置使用资源**，因此，不建议在后台修改用户角色与激活用户。为方便管理，使用以下方法修改角色和激活帐号。

进入项目根目录，

- 修改用户角色

```
$php artisan bigbigads:change <email帐户> <角色key,如Free,Standard...>
```
- 激活/反激活用户

```
$php artisan bigbigads:activate <email帐户> <激活为1，反激活为0>
```

`php artisan`是`Laravel`的命令行机制，通过`php artisan list`可查看所有命令的描述，该项目专用的命令分类在`bigbigads`下面，要查看具体命令的参数说明(eg. `bigbigads:change`)，可使用`php artisan help bigbigads:change`。

### 如何发博客？
请找到`Posts`菜单，标准的发文章流程，看下就懂如何操作。然后进前台看下即可。



## 如何添加搜索项
[参考对应WIKI:如何添加搜索项](/bigbigads/bigbigads/wikis/%E5%B9%BF%E5%91%8A%E6%90%9C%E7%B4%A2%E5%A6%82%E4%BD%95%E6%B7%BB%E5%8A%A0%E6%90%9C%E7%B4%A2%E9%A1%B9)



## 配置QA
Q:升级源码后，权限配置没生效？
A:

1. 重新生成下权限`php artisan db:seed --class=BigbigadsSeeder`。
2. 登陆后，进入`http://<服务器域名>/tester`，重新将自己的权限提示到`Pro`级别，界面上有打出内容且不是错误提示就表示成功，这时重新登陆下权限即生效。
3. 如果没有登陆，使用的是匿名用户的权限，没生效是因为匿名用户有缓存，要么等一天再试，要么修改`app/Services/AnonymousUser.php`，将`user`函数按下面说明修改：

```
    /**
     * 返回匿名帐户
     */
    public static function user($req)
    {
        $ip = $req->ip();
        $user = null;//Cache::get($ip);//将此修改为null即可
        if (!is_null($user) && !is_null($user->date) && $user->date->isToday()) {
            Log::debug("$ip is still valid");
        } else {
        	...省略无关代码
        }
        return $user;
    }

```

## 展示网站(Bigbigads根目录)

```
$npm run develop #开发环境单次编译
$npm run watch #开发环境监听
$npm run product #生产环境
```

## 开发配置
教程请参考Wiki

## Design Documents
[Bigbigads原型设计.rp](/uploads/068102ffa932509f98fed02efb76029b/Bigbigads原型设计.rp)
[bigbigads设计.asta](/uploads/9c4876c4cff4d2fa4059bf744bd652b7/bigbigads设计.asta)
[bigbigads_plan_new.pdf](/uploads/0bd89378aafdb23d60ac644317e08be6/bigbigads_plan_new.pdf)

## Admin Tutorial
[Voyager Document](https://the-control-group.github.io/voyager/docs/0.11/#core-concepts-bread)
