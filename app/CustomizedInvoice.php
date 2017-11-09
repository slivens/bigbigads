<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomizedInvoice extends Model
{
    protected $fillable = ['user_id', 'company_name', 'address', 'contact_info', 'website', 'tax_no'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 存储限制，一个月只能存储1次
     * 若更新时间与今天差30天或者以上，则更新
     * 旧判断会导致不同年的同一个月不能更新
     *
     * @return boolean
     */
    public function canSave()
    {
        return Carbon::parse($this->updated_at)->diffInDays(Carbon::now()) >= 30;// month != Carbon::now()->month;
    }
}
