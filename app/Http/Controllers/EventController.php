<?php

namespace App\Http\Controllers;

use App\Service;
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
//        $services = $this->service
        $events = Event::with('service')->where('service_id',Auth::user()->id)->get();
        $event_list = [];
        foreach ($events as $key => $event){
            $event_list[] = Calendar::event(
                $event->event_name,
                true,
                new \DateTime($event->start_date),
                new \DateTime($event->end_date)
            );
        }
        $calendar_details = Calendar::addEvents($event_list);
        return view('events', compact('calendar_details'));
    }
    //

    public function addEvent(Request $request){
        $validator = Validator::make($request->all(),[
            'event_name'=>'required',
            'start_date'=>'required',
            'end_date'=>'required'
        ]);

        if($validator->fails()){
            \Session::flash('warning','Please enter the valid details');
            return Redirect::to('/events')->withInput()->withErrors($validator);
        }

        $event = new Event();
        $event->event_name = $request['event_name'];
        $event->start_date = $request['start_date'];
        $event->end_date = $request['end_date'];
        $event->save();

        \Session::flash('success','Event added successfully.');
        return Redirect::to('events');
    }
}
