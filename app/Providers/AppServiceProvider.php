<?php

namespace App\Providers;

use App\User;
use TCG\Voyager\Models\User as VoyagerUser;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Payum\LaravelPackage\Storage\EloquentStorage;
use App\BookmarkItem;
use App\Payment;
use App\GatewayConfig;
use Payum\LaravelPackage\Model\Token;
use Payum\Core\Storage\FilesystemStorage;
use Illuminate\Support\Facades\Session;
use Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 监听 User 模型事件
        User::observe(UserObserver::class);
        VoyagerUser::observe(UserObserver::class);
        \App\Refund::observe(\App\Observers\RefundObserver::class);


        //Braintree支付注册
        \Braintree_Configuration::environment(config('services.braintree.environment'));
        \Braintree_Configuration::merchantId(config('services.braintree.merchant_id'));
        \Braintree_Configuration::publicKey(config('services.braintree.public_key'));
        \Braintree_Configuration::privateKey(config('services.braintree.private_key'));

        // 收藏夹不允许重复记录，会影响到权限统计，因此在创建的时候就要检查
        // TODO: 应该移到别的地方去
        BookmarkItem::creating(function($newItem) {
            $count = BookmarkItem::where('bid', $newItem->bid)->where('type', $newItem->type)->where('ident', $newItem->ident)->count();
            if ($count > 0) {
                Log::debug("$count:" . $newItem);
                return false;
            }
            return true;
        });
        $this->app['view']->addNamespace('cashier', base_path() . '/vendor/laravel/cashier-braintree/resources/views');

        Session::extend('enhanced', function($app) {
            $store = $app['config']->get('session.store') ?: 'redis';          
            $minutes = $app['config']['session.lifetime'];  
            return new \App\Extensions\EnhancedSessionHandler(clone $app['cache']->store($store), $minutes);
        });

        Response::macro('success', function(string $desc, array $extra = []) {
            return Response::json(array_merge(['code' => 0, 'desc' => $desc], $extra));
        });
        Response::macro('fail', function($code, string $desc, array $errors = [], array $extra = [], int $statusCode = 422) {
            if (request()->expectsJson())
                return Response::json(array_merge(["code" => $code, "desc" => $desc, "errors" => $errors], $extra), $statusCode);
            return abort(500, "Code $code: $desc");
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('mailgun.client', function() {
            return \Http\Adapter\Guzzle6\Client::createWithConfig([
                // your Guzzle6 configuration
            ]);
        }); 

        $this->app->singleton(\App\Contracts\PaymentService::class, function() {
            // 配置应该由此处传入，以便达到解耦以及多个PaymentService实例共用的目的
            return new \App\Services\PaymentService(config('payment'));
        });

        $this->app->singleton('app.service.payment', function() {
            return app(\App\Contracts\PaymentService::class);
        });

        $this->app->singleton(\App\Contracts\SessionService::class, function() {
            return new \App\Services\SessionService();
        });

        $this->app->singleton('app.service.session', function() {
            return app(\App\Contracts\SessionService::class);
        });

        $this->app->bind(\App\Contracts\UserService::class, function($app) {
            return new \App\Services\UserService(['useMailgun' => !empty(env('MAILGUN_USERNAME'))]);
        });
        $this->app->singleton('app.service.user', function() {
            return app(\App\Contracts\UserService::class);
        });

        $this->app->resolving('payum.builder', function(\Payum\Core\PayumBuilder $payumBuilder) {
            $payumBuilder
                // this method registers filesystem storages, consider to change them to something more
                // sophisticated, like eloquent storage
                /* ->setTokenStorage(new FilesystemStorage(sys_get_temp_dir(), Token::class, 'hash')) */
                /* ->addStorage(Payment::class, new EloquentStorage(Payment::class)) */
                /* ->setTokenStorage(new EloquentStorage(Token::class)) */
                /* ->addStorage(\ArrayObject::class, new FilesystemStorage(sys_get_temp_dir(), ArrayObject::class)) */
                /* ->addStorage(Payout::class, new FilesystemStorage(sys_get_temp_dir(), Payout::class)) */
                ->addDefaultStorages();
            // Paypal配置全部从数据库中读取
            // Paypal Express Checkout
            $configs = GatewayConfig::where('factory_name', GatewayConfig::FACTORY_PAYPAL_EXPRESS_CHECKOUT)->get();
            foreach ($configs as $config) {
                $payumBuilder->addGateway($config->gateway_name, [
                        'factory' => 'paypal_express_checkout',
                        'username' => $config->config['username'],
                        'password' => $config->config['password'],
                        'signature' => $config->config['signature'],
                        'sandbox' => $config->config['sandbox']
                    ]);
            }

            // Paypal REST API
            $configs = GatewayConfig::where('factory_name', GatewayConfig::FACTORY_PAYPAL_REST)->get();
            foreach ($configs as $config) {
                $payumBuilder->addGateway($config->gateway_name, [
                        'factory' => 'paypal_rest',
                        'client_id' => $config->config['client_id'],
                        'client_secret' => $config->config['client_secret'],
                        'config_path' => $config->config['config_path']
                    ]);
            }
            $payumBuilder->addGateway('stripe',[
                    'factory' => 'stripe_js',
                    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
                    'secret_key' => env('STRIPE_SECRET_KEY')
                ]);
        });

        if ($this->app->environment('local')) {
            $this->app->register(\Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class);
        }
    }
}
