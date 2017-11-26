<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    const DEFAULT = 'default';
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

    protected $fillable = [
        'uid',
        'name',
        'default'
    ];

    // default 值为1的收藏夹不允许修改
    public function canModify()
    {
        return $this->default != 1;
    }
}
