<?php

namespace TGChat\Http\Controllers;

use TGChat\Order;
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
    public function checkIntersections($order){
        $master = $order->service->user;
        $day = date('Y-m-d',strtotime($order->date));
        $dayOfWeek = date('l',strtotime($order->date));
        $time = date("H:i",strtotime($order->date));
        $schedule = json_decode($master->schedule, true);
        [$dayStart,$dayEnd] = explode('-',$schedule[$dayOfWeek]);


        //Это позволяет записывать несмотря на расписание
        $dayStart = '00:00:00';
        $dayEnd = '23:59:59';

        $dayStartTimestamp = strtotime($day.' '.$dayStart);
        $dayEndTimestamp = strtotime($day.' '.$dayEnd);
        $availableTimes = [];

        $eventsForChosenDay = Event::where('user_id',Auth::user()->id)->where('start_date','LIKE', '%'.$day.'%')->orderBy('start_date','asc')->get();
        if(count($eventsForChosenDay)){
        }
        $i=0;
        $count = count($eventsForChosenDay);
        foreach ($eventsForChosenDay as $event){
            $i++;
            if($event->service) {
                $duration = $event->service->duration;
            }else{
                $duration = $event->duration;
            }
            $gapEndTimestamp = strtotime($event->start_date);
            if($i == 1){
                $gapStartTimestamp = $dayStartTimestamp;
            }else{
                $prevEventStartTimestamp = strtotime($eventsForChosenDay[$i-2]->start_date);
                if($eventsForChosenDay[$i-1]->service){
                    $prevEventDuration = $eventsForChosenDay[$i-1]->service->duration;
                }else{
                    $prevEventDuration = $eventsForChosenDay[$i-1]->duration;
                }
                $prevEventEndTimestamp = $prevEventStartTimestamp + $prevEventDuration*60;
                $gapStartTimestamp =  $prevEventEndTimestamp;
            }


            $gap = ($gapEndTimestamp - $gapStartTimestamp)/60;
            if($gap >= $duration){
                $availableTimes[] = [$gapStartTimestamp,$gapEndTimestamp];
            }
            if($i == $count){
                if($event->service) {
                    $gapStartTimestamp = strtotime($event->start_date) + $event->service->duration * 60;
                }else{
                    $gapStartTimestamp = strtotime($event->start_date) + $event->duration * 60;
                }
                $gapEndTimestamp = $dayEndTimestamp;
                $gap = ($gapEndTimestamp - $gapStartTimestamp)/60;
                if($gap >= $duration){
                    $availableTimes[] = [$gapStartTimestamp,$gapEndTimestamp];
                }
            }
        }
        $dayTimes = $dayStartTimestamp;
        $availableDayTimes = [];
        do{
            if($count == 0){
                if($dayTimes >= $dayStartTimestamp[0] && $dayTimes <= ($dayEndTimestamp - $order->service->duration*60)){
                    $availableDayTimes[] = $dayTimes;
                    $dayTimes += 15*60;
                    continue;
                }
            }
            foreach ($availableTimes as $availableTime){
                if($dayTimes >= $availableTime[0] && $dayTimes <= ($availableTime[1] - $order->service->duration*60)){
                    $availableDayTimes[] = $dayTimes;
                    break;
                }
            }
            $dayTimes += 15*60;
        }while($dayTimes < $dayEndTimestamp);
        $availableButtons = [];
        foreach ($availableDayTimes as $availableTime) {
            $text = date('H:i', $availableTime);
            $availableButtons[] = $text;
        }
        if(in_array($time, $availableButtons)){
            return [];
        }else{
            return $availableButtons;
        }
    }
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
        $order = new Order();
        $order->service_id = $request['service_id'];
        $client = Client::where('id','=',$request['client_id'])->get()->first();
        $order->client_id = $client->id;
        $order->name = $client->client_name;
        $order->phone = $client->phone;
        if($client->telegram_id != null){
            $order->client_telegram_id = $client->telegram_id;
        }
        $order->service_id = $request['service_id'];
        $order->comment = null;
        $order->date = $request['start_date'];
        $order->created_at = date("Y-m-d H:i:s");
        $order->updated_at = date("Y-m-d H:i:s");
        $order->save();
        $intersections = $this->checkIntersections($order);
        if(count($intersections)){
            $times = implode(', ',$intersections);
            $text = "На это время уже есть запись. Свободные места: ".$times. '!';
            \Session::flash('warning',$text);
            $order->delete();
        }else{
            $event = new Event();
            $event->start_date = $order->date;
            $event->service_id = $order->service_id;
            $event->client_id = $order->client_id;
            $event->client_telegram_id = $order->client_telegram_id;
            $event->telegram_user_id = null;
            $event->user_id = Auth::user()->id;
            $event->amount = $order->service->amount;
            $event->save();

            \Session::flash('success','Запись создана успешно');
        }
        return Redirect::to('events');


    }
}
