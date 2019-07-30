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
        $order->save();

        \Session::flash('success','Order added successfully.');
        return Redirect::to('orders');
    }
    public function confirm($id){
        $order = Order::where('id',$id)->with('service')->first();
        $event = new Event();
        $event->service_id = $order->service->id;
        $event->start_date = $order->date;
        $old_client = Client::where('client_name', $order->name)->where('phone', $order->phone)->where('id', Auth::user()->id)->first();

        if($old_client===null) {  //if this master didn't have client with this data, it'll add it
            $client = new Client();
            $client->client_name = $order->name;
            $client->phone = $order->phone;
            $client->user_id = Auth::user()->id;
            $client->save();
        }else{
            $client=$old_client;
        }
        $event->client_id = $client->id;
        $event->save();
        $order->delete();
        return Redirect::to('orders');
    }
    public function cancel($id){
        $order = Order::where('id',$id)->with('service')->first()->delete();
        return Redirect::to('orders');
    }

}
