<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActionLog extends Model
{
    //
    //
    public function log($type, $param, $remark, $ip = Request::ip()) 
    {
        $action = new ActionLog();
        $action->type = $type;
        $action->ip = $ip;
        $action->param = $param;
        $action->remark = $remark;
        $action->save();
    }
}
