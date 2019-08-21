<?php

namespace TGChat;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'client_name', 'phone'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function events(){
        return $this->hasMany(Event::class);
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }
}
