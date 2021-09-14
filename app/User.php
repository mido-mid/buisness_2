<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','remember_token','birthDate','phone','gender','city_id','country_id',
        'category_id','type','stateId','jobTitle','verification_code',
        'state','age','official','user_name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function followers()
    {
        return $this->hasMany('App\Models\Following', 'followingId');
    }

    public function friends()
    {
        return $this->hasMany('App\Models\Friendship', 'senderId');
    }

    public function myfriends()
    {
        return $this->hasMany('App\Models\Friendship', 'receiverId');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\State', 'stateId' );
    }

    public function groupmember()
    {
        return $this->hasOne('App\Models\GroupMember', 'user_id' );
    }

    public function pagemember()
    {
        return $this->hasOne('App\Models\PageMember', 'user_id' );
    }
}
