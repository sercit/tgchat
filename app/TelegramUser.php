<?php

namespace TGChat;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    //
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
