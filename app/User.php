<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable,EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function profile()
    {
        return $this->hasOne('App\Profile');
    }

    public function userLocation()
    {
        return $this->hasMany('App\UserLocation'); 
    }

    public function task()
    {
        return $this->belongsToMany('App\Task','task_user','user_id','task_id')->withPivot('rating', 'comment','updated_at');
    }

    public function routeNotificationForNexmo()
    {
        return $this->Profile->mobile;
    }

    public function getFullNameAttribute(){
        return $this->Profile->first_name.' '.$this->Profile->last_name;
    }

    public function getFullNameWithDesignationAttribute(){
        return ucfirst($this->Profile->first_name).' '.ucfirst($this->Profile->last_name).' ('.$this->Profile->Designation->name.' in '.ucfirst($this->Profile->Designation->Department->name).')';
    }

    public function getFullDesignationAttribute(){
        return $this->Profile->Designation->name.' in '.ucfirst($this->Profile->Designation->Department->name);
    }
}
