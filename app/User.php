<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use TCG\Voyager\Traits\VoyagerUser;
use Laravel\Cashier\Billable;
use Illuminate\Support\Facades\Cache;

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
            $value = $this->role->groupedPolicies();
            foreach($value as $key=>$item) {
                $value[$key][2] = 0;
            }
            $this->usage = $value;
            return $value;
        }
        return json_decode($value, true);
    }

    public function setUsageAttribute($value)
    {
        $this->attributes['usage'] = json_encode($value);
    }

    public function initUsageByRole($role)
    {
        $items = $role->groupedPolicies();
        foreach($items as $key=>$item) {
            $items[$key][2] = 0;
        }
        $this->usage = $items;
        return true;
    }

    public function getUsage($key)
    {
        $item = $this->usage[$key];
        return $item;
    }

    public function updateUsage($key, $used, $extra)
    {
        $usage = $this->usage;
        if (!isset($usage[$key])) 
            return false;
        $usage[$key][2] = $used;
        if (!is_null($extra))
            $usage[$key][3] = $extra;
        $this->usage = $usage;
        $this->save();
        return true;
    }

    public function incUsage($key, $extra)
    {
        $usage = $this->usage;
        if (!isset($usage))
            return false;
        $this->updateUsage($key, count($usage[$key]) > 2 ? $usage[$key][2] + 1 : 1, $extra);
        return true;
    }

    public function getCache($key)
    {
        $newkey = "{$this->id}:" . $key;
        return Cache::get($newkey);
    }

    public function setCache($key, $val)
    {
        $newkey = "{$this->id}:" . $key;
        Cache::put($newkey, $val, 1440);
    }
}
