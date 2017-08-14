<?php

namespace App\Providers;

use App\User;
use TCG\Voyager\Models\User as VoyagerUser;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\BookmarkItem;
use Payum\LaravelPackage\Storage\EloquentStorage;
use App\Payment;
use Payum\LaravelPackage\Model\Token;
use Payum\Core\Storage\FilesystemStorage;

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

		//Braintree支付注册
		\Braintree_Configuration::environment(config('services.braintree.environment'));
		\Braintree_Configuration::merchantId(config('services.braintree.merchant_id'));
		\Braintree_Configuration::publicKey(config('services.braintree.public_key'));
		\Braintree_Configuration::privateKey(config('services.braintree.private_key'));

        //收藏夹不允许重复记录，会影响到权限统计，因此在创建的时候就要检查
        BookmarkItem::creating(function($newItem) {
            $count = BookmarkItem::where('bid', $newItem->bid)->where('type', $newItem->type)->where('ident', $newItem->ident)->count();
            if ($count > 0) {
                Log::debug("$count:" . $newItem);
                return false;
            }
            return true;
        });
        $this->app['view']->addNamespace('cashier', base_path() . '/vendor/laravel/cashier-braintree/resources/views');
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

        $this->app->resolving('payum.builder', function(\Payum\Core\PayumBuilder $payumBuilder) {
            $payumBuilder
				// this method registers filesystem storages, consider to change them to something more
                // sophisticated, like eloquent storage
                /* ->setTokenStorage(new FilesystemStorage(sys_get_temp_dir(), Token::class, 'hash')) */
                /* ->addStorage(Payment::class, new EloquentStorage(Payment::class)) */
                /* ->setTokenStorage(new EloquentStorage(Token::class)) */
                /* ->addStorage(\ArrayObject::class, new FilesystemStorage(sys_get_temp_dir(), ArrayObject::class)) */
                /* ->addStorage(Payout::class, new FilesystemStorage(sys_get_temp_dir(), Payout::class)) */
                ->addDefaultStorages()
                ->addGateway('paypal_ec', [
                    'factory' => 'paypal_express_checkout',
                    'username' => env('PAYPAL_EC_USERNAME'),
					'password' => env('PAYPAL_EC_PASSWORD'),
					'signature' => env('PAYPAL_EC_SIGNATURE'),
					'sandbox' => env('PAYPAL_EC_ENV') === 'sandbox'
				]);
            $payumBuilder->addGateway('stripe',[
					'factory' => 'stripe_js',
					'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
					'secret_key' => env('STRIPE_SECRET_KEY')
				]);
		});
	}
}
