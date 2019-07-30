<?php

namespace TGChat;

use Illuminate\Database\Eloquent\Model;

/**
 * Post
 *
 * @mixin Eloquent
 */
class TelegramUser extends Model
{
    //
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function events(){
        return $this->hasMany(Event::class);
    }
}
