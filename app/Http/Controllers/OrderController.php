<?php

namespace TGChat\Http\Controllers;

use TGChat\Service;
use Illuminate\Http\Request;
use TGChat\Order;
use Auth;
use TGChat\Event;
use TGChat\Client;
use Validator;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $orders = Order::select(['services.*','orders.*'])
                        ->where('user_id', Auth::user()->id)
                        ->join('services', 'orders.service_id','=','services.id')
                        ->where('date','>',date('Y-m-d H:i:s'))
                        ->get();
        $services = Service::where('user_id',Auth::user()->id)->pluck('service_name','id');
        return view('orders', compact('orders','services'));
    }

    public function add(Request $request){
        $validator = Validator::make($request->all(),[
            'service_id'=>'required',
            'name'=>'required',
            'phone'=>'required',
            'date'=>'required'
        ]);

        if($validator->fails()){
            \Session::flash('warning','Please enter the valid details');
            return Redirect::to('/services')->withInput()->withErrors($validator);
        }

        $order = new Order();
        $order->service_id = $request['service_id'];
        $order->name = $request['name'];
        $order->phone = $request['phone'];
        $order->date = $request['date'];
        $order->client_id = null;
        $order->client_telegram_id = null;
        $order->comment = null;
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
            \Session::flash('success','Заказ успешно создан');
        }

        return Redirect::to('orders');
    }

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

    public function confirm($id){
        $order = Order::where('id', $id)->get()->first();

        $intersections = $this->checkIntersections($order);
        if(count($intersections)){
            $times = implode(', ',$intersections);
            $text = "На это время уже есть запись. Свободные места: ".$times. '!';
            \Session::flash('warning',$text);
            $order->delete();
            return Redirect::to('orders');
        }else {
            $event = new Event();
            $event->service_id = $order->service->id;
            $event->user_id = $order->service->user_id;
            $event->duration = $order->service->duration;
            $event->amount = $order->service->amount;

            $event->start_date = $order->date;
            $old_client = Client::where('client_name', $order->name)->where('phone', $order->phone)->where('id', Auth::user()->id)->first();

            if ($old_client === null) {  //if this master didn't have client with this data, it'll add it
                $client = new Client();
                $client->client_name = $order->name;
                $client->phone = $order->phone;
                $client->user_id = Auth::user()->id;
                $client->save();
            } else {
                $client = $old_client;
            }
            $event->client_id = $client->id;
            $event->client_telegram_id = $order->client_telegram_id;
            $event->telegram_user_id = null;
            $event->amount = $order->service->amount;
            $event->save();
            $order->delete();
            \Session::flash('success', 'Запись создана успешно');
            return Redirect::to('orders');
        }
    }
    public function cancel($id){
        $order = Order::where('id',$id)->with('service')->first()->delete();
        return Redirect::to('orders');
    }

}
