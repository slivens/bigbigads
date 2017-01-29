<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\Permission;

class Role extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }   
    
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    public function policies() 
    {
        return $this->belongsToMany(Policy::class)->withPivot('value');
    }
}
