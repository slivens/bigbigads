<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomizedInvoice extends Model
{
    protected $table = 'customized_invoice';
    protected $fillable = ['user_id', 'company_name', 'address', 'contact_info', 'website', 'tax_no'];
    public function user()
    {
        return $this->belongsTo('\App\User');
    }

    /**
     * 存储限制，一个月只能存储1次
     * 若更新时间不在当前月内，则更新
     *
     * @return boolean
     */
    public function canSave()
    {
        return Carbon::parse($this->updated_at)->month != Carbon::now()->month;
    }
}
