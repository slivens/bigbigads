<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\BookmarkItem;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //收藏夹不允许重复记录，会影响到权限统计，因此在创建的时候就要检查
        BookmarkItem::creating(function($newItem) {
            $count = BookmarkItem::where('bid', $newItem->bid)->where('type', $newItem->type)->where('ident', $newItem->ident)->count();
            if ($count > 0) {
                Log::debug("$count:" . $newItem);
                return false;
            }
            return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
