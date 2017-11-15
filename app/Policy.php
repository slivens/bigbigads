<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    const PERMANENT = 0;
    const MONTH = 1;
    const DAY = 2;
    const HOUR = 3;
    const VALUE = 4;
    const DURATION = 5;
    const YEAR = 6;

    const TYPE_DESC = [
        self::DAY => '按日累计',
        self::PERMANENT => '永久累计',
        self::YEAR => '按年累计',
        self::MONTH => '按月累计',
        self::HOUR => '按小时累计',
        self::VALUE => '固定数值'
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];
}
