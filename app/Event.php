<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'event_name', 'start_date', 'end_date', 'service_id'
    ];
    //
    public function service(){
        return $this->belongsTo(Service::class);
    }
    public function client(){
        return $this->belongsTo(Client::class);
    }
}
