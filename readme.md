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

## 后台如何配置权限
根据设计文档，权限与策略的基础描述信息分别都放在`permissions`与`policies`表中，而角色的权限则在`permission_role`,角色的策略在`policy_role`。直接通过修改数据库去修改权限是非常困难的。为了简化操作，这里使用了`Laravel`的种子填充功能实现权限、策略的生成以及角色的权限与策略配置。具体请查看`database/seeds/BigbigadsSeeder.php`源码，在完成修改后，执行以下命令完成配置。

```
php artisan db:seed --class=BigbigadsSeeder
```
> 完成配置后，不代表前端能立即生效。这里有两个原因：
> 
> 1. 用户信息是缓存的（缓存时间为1天），所以需要手动禁用缓存才行 
> 2. 每个用户要记录它的策略Usage，所以只有在初始化的时候才从角色那里初始化策略Usage。如果角色的Usage变了，用户Usage也跟着变会有问题，所以当重新填充角色的策略时，需要重新初始化用户的策略Usage才能生效。

## 前端如何判断权限
两种方式：`policy-lock`指令,`User.can``User.getPolicy`接口。

### policy-lock用法

```
<!-- 下面这行表示检查adser_search(广告主搜索）权限，没有权限就在按钮后面加锁并禁止 -->
<button class="btn btn-primary" policy-lock key="adser_search" trigger="lockButton">Search</button>
```

#### policy-lock参数说明

- `key`  要检查的权限，可以使用|同时检查多个权限（不允许有策略)。目前没有相关文档指明数据库中`permissions`对应到需求文档上的计划列表，所以目前只能从`BigbigadsSeeder.php`查看（后续研究下是否有建立关联的更好手段）。
- `trigger` 目前支持`lockButton`,`disabled`以及没有属性值这三种情况，分别实现以下特性"禁用并加锁","禁用","加锁"。

User.can以及User.getPolicy待补充。
## 如何添加搜索项
[参考对应WIKI:如何添加搜索项](http://git.papamk.com:81/bigbigads/bigbigads/wikis/%E5%B9%BF%E5%91%8A%E6%90%9C%E7%B4%A2%E5%A6%82%E4%BD%95%E6%B7%BB%E5%8A%A0%E6%90%9C%E7%B4%A2%E9%A1%B9)
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
