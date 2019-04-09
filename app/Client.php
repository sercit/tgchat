<?php

namespace App;

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
}
