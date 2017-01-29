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
    //
}
