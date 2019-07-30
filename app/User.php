<?php

namespace TGChat;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname','lastname','patronymic', 'phone', 'address','welcome_message','paid_until','schedule','email', 'password', 'telegram_user_id', 'telegram_user_token',
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
    public function services(){
        return $this->hasMany('TGChat\Service');
    }
    public function events(){
        return $this->hasMany('TGChat\Event');
    }
    public function clients(){
        return $this->hasMany('TGChat\Client');
    }
    public function telegram_user(){
        return $this->hasOne('TGChat\TelegramUser');
    }
}
