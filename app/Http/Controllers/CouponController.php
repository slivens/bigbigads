<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CouponController extends ResourceController
{

    protected function checkBeforeIndex(Request $request)
    {
        if (!$this->where || empty($this->where['code']))
            return false;
        return true;
    }
}
