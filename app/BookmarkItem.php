<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookmarkItem extends Model
{
    protected $fillable = ['uid', 'bid', 'ident', 'type'];
    protected $hidden = [
        'created_at', 'updated_at'
    ];
}
