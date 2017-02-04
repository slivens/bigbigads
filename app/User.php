<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use TCG\Voyager\Traits\VoyagerUser;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Notifiable, Billable;

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if User has a Role(s) associated.
     *
     * @param string|array $name The role to check.
     *
     * @return bool
     */
    public function hasRole($name)
    {
        return in_array($this->role->name, (is_array($name) ? $name : [$name]));
    }

    public function setRole($name)
    {
        $role = Role::where('name', '=', $name)->first();

        if ($role) {
            $this->role()->associate($role);
            $this->save();
        }

        return $this;
    }

    public function hasPermission($name)
    {
        return in_array($name, $this->role->permissions->pluck('key')->toArray());
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getUsageAttribute($value)
    {
        if (is_null($value)) {
            return $this->role->groupedPolicies();
        }
        return json_decode($value, true);
    }

    public function setUsageAttribute($value)
    {
        $this->attributes['usage'] = json_encode($value);
    }

    public function getUsage($key)
    {
        return $this->usage[$key];
    }

    public function updateUsage($key, $used)
    {
        $usage = $this->usage;
        if (!isset($usage[$key])) 
            return false;
        $usage[$key][2] = $used;
        $this->usage = $usage;
        $this->save();
        return true;
    }

    public function incUsage($key)
    {
        $usage = $this->getUsage($key);
        if (!isset($usage))
            return false;
        $this->updateUsage($key, count($usage) > 2 ? $usage[2] + 1 : 1);
        return true;
    }
}
