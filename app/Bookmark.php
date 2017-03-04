<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    //
    protected $appends = ['item_count'];
    public function items()
    {
        return $this->hasMany("App\BookmarkItem", "bid");
    }

    public function getItemCountAttribute($value)
    {
        return $this->items()->distinct()->count();
    }

    protected $hidden = [
        'created_at', 'updated_at'
    ];
}

