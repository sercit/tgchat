<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'service_name', 'duration', 'amount'
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
