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
$gulp production
```

复制`.env.example`为`.env`,`.env`配置，这里面主要包含AppID,数据库配置、SMTP配置、缓存配置、以及调试配置。在实践上`.env`通常包含敏感信息（AppID及数据库配置）以及跟本地环境强相关（每个机器的数据库可能都不一样），因此不会将它包含进仓库，而是提供`.env.example`作为范例。`.env`文件在`Laravel 5.3`下有详细的说法明，这里简单对配置下做下说明。


```
APP_ENV=local
APP_KEY=base64:9sQQMUuhljKvsr8F/oEx18b+PhAekm1R0jNNzM7VSRw= #通过php artisan key:generate生成
APP_DEBUG=true #出错时显示错误栈，实际布署环境应该设置为false
APP_LOG_LEVEL=debug #记录日志的级别
APP_URL=http://localhost

# 以下为数据库配置
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bigbigads
DB_USERNAME=root
DB_PASSWORD=FZ0802ad

# 排名数据库与本地数据库不是同一个，以下为排名数据库配置
RANKDB_CONNECTION=mysql
RANKDB_HOST=121.41.107.126
RANKDB_PORT=3306
RANKDB_DATABASE=ads_analysis
RANKDB_USERNAME=root
RANKDB_PASSWORD=FZ0802ad

BROADCAST_DRIVER=log
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync

# REDIS配置
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# E-MAIL配置
MAIL_DRIVER=smtp
MAIL_HOST=smtp.papamk.com
MAIL_PORT=25
MAIL_USERNAME=pheye@papamk.com
MAIL_PASSWORD=123456abc
MAIL_ENCRYPTION=null

PUSHER_APP_ID=
PUSHER_KEY=
PUSHER_SECRET=

# Braintree配置
BRAINTREE_ENV=sandbox
BRAINTREE_MERCHANT_ID=svjdgf4mf94mfkdv
BRAINTREE_PUBLIC_KEY=ygh9txfjt8cv5kp2
BRAINTREE_PRIVATE_KEY=2616a406dba36832b23db9b0d8e6f4e8
```

然后配置nginx或者apache,将网站根目录定位到`public/`，同时允许`URL rewrite`。配置就完成了。

## 权限配置指南
[参考对应WIKI:权限配置指南](/bigbigads/bigbigads/wikis/权限配置指南)

## subscriptions配置
手动清除下数据库

```
drop table subscriptions
```

> 在产品上线后就不应该用rollback，否则整个列删除后，原来生产的数据将跟着被清除。

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


## 开发配置
教程请参考Wiki

## Design Documents
[Bigbigads原型设计.rp](/uploads/068102ffa932509f98fed02efb76029b/Bigbigads原型设计.rp)
[bigbigads设计.asta](/uploads/1ca44d34c686fecbb6ca07a91faf0fb6/bigbigads设计.asta)
[bigbigads_plan_new.pdf](/uploads/0bd89378aafdb23d60ac644317e08be6/bigbigads_plan_new.pdf)
