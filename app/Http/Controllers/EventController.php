<?php

namespace TGChat\Http\Controllers;

use TGChat\Service;
use TGChat\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use TGChat\Http\Controllers\Controller;
use Auth;
use Validator;
use TGChat\Event;

use MaddHatter\LaravelFullcalendar\Facades\Calendar;


class EventController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(){
        $events = Event::where('user_id', Auth::user()->id)->get();
        $event_list = [];
        foreach ($events as $key => $event){
            if($event->duration == null){
                $serviceName = $event->service->service_name;
                $duration = $event->service->duration;
            }else{
                $duration = $event->duration;
                if(!$event->service) {
                    if (!$event->amount) {
                        $serviceName = 'Перерыв';
                    } else {
                        $serviceName = 'Услуга уже удалена';
                    }
                }else{
                    $serviceName = $event->service->service_name;
                }

            }
            if($event->client){
                $clientName = $event->client->client_name;
                $phone = $event->client->phone;
            }else{
                $clientName = 'Имя клиента недоступно';
                $phone = 'Телефон клиента недоступен';
            }
            //$this->checkIntersections($event_list, $event);
            $event_list[] = Calendar::event(
                $serviceName.PHP_EOL.
                    $clientName.'('.$phone.')',
                false,
                new \DateTime($event->start_date),
                new \DateTime($event->start_date.' +'.$duration.' minutes')
            );
        }
        $calendar_details = Calendar::addEvents($event_list)
                                    ->setOptions([
                                        'firstDay' => 1,
                                        'axisFormat' => 'H:mm',
                                        'timeFormat' => 'H:mm',
                                        'allDaySlot'=> false,
                                        'navLinks' => true,
                                        'locales'=>'ruLocale',
                                        'locale'=>'ru',
                                        'businessHours'=>'
                                        {

                                            start: \'11:00\',
                                                end:   \'12:00\',
                                                dow: [ 1, 2, 3, 4, 5]
                                        }',

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
        $event->client_telegram_id = null;
        $event->telegram_user_id = null;
        $event->user_id = Auth::user()->id;
        $event->amount = Service::find($request['service_id'])->amount;
        $event->save();

        \Session::flash('success','Event added successfully.');
        return Redirect::to('events');
    }
    public function checkIntersections($events, $newEvent){
        foreach ($events as $event){
            $oldEventStartDate   = strtotime($event->start->format('Y-m-d H:i:s'));
            $oldEventEndDate = strtotime($event->end->format('Y-m-d H:i:s'));
            $newEventStartDate = strtotime($newEvent->start_date);
            $newEventEndDate = $newEvent->service->duration+$newEventStartDate;
            if($oldEventStartDate<$newEventEndDate AND $oldEventEndDate>$newEventStartDate){
                $newEvent->name = '(П)'.$newEvent->service->service_name;
                return;
            }else{
                $newEvent->name = $newEvent->service->service_name;
            }
        }
        return true;
    }
}
