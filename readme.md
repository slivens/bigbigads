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

## 配置QA

## 开发配置
教程请参考Wiki
