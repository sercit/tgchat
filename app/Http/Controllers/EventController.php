<?php

namespace App\Http\Controllers;

use App\Service;
use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use App\Event;

use MaddHatter\LaravelFullcalendar\Facades\Calendar;


class EventController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(){
        $events = Event::get();
        $event_list = [];
        foreach ($events as $key => $event){
            $event_list[] = Calendar::event(
                $event->service->service_name.'</span><br/></span>'.$event->client->phone,
                false,
                new \DateTime($event->start_date),
                new \DateTime($event->start_date.' +'.$event->service->duration.' minutes')
            );
        }
        $calendar_details = Calendar::addEvents($event_list)
                                    ->setOptions([
                                        'firstDay' => 1,
                                        'axisFormat' => 'H:mm',
                                        'timeFormat' => 'H:mm',
                                        'allDaySlot'=> false,
                                        'navLinks' => true,
                                        'locales'=> 'ru',

                                    ])->setCallbacks([
                                        'eventClick' => 'function(info){console.log(info)}',
                                    ]);
        $services = Service::where('user_id',Auth::user()->id)->pluck('service_name','id');
        $clients = Client::where('user_id',Auth::user()->id)->pluck('client_name','id');
        return view('events', compact('calendar_details', 'services','clients'));
    }
    //

    public function addEvent(Request $request){
        $validator = Validator::make($request->all(),[
            'start_date'=>'required',
            'service_id'=>'required',
            'client_id'=>'required',
        ]);

        if($validator->fails()){
            \Session::flash('warning','Please enter the valid details');
            return Redirect::to('/events')->withInput()->withErrors($validator);
        }

        $event = new Event();
        $event->start_date = $request['start_date'];
        $event->service_id = $request['service_id'];
        $event->client_id = $request['client_id'];
        $event->save();

        \Session::flash('success','Event added successfully.');
        return Redirect::to('events');
    }
}
