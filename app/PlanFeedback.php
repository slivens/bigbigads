<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanFeedback extends Model
{
    protected $fillable = ['first_name', 'last_name', 'company', 'website', 'page', 'email', 'phone', 'price', 'skype', 'location', 'feedback'];
}
