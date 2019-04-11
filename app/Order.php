<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'event_name', 'start_date', 'end_date', 'service_id'
    ];
    //
    public function service(){
        return $this->belongsTo(Service::class);
    }
}
