<?php

namespace App\Http\Controllers;

use App\Service;
use Illuminate\Http\Request;
use App\Order;
use Auth;
use Validator;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        //ВОТ ЗДЕСЬ МНЕ НАДО ПОЛУЧИТЬ ВСЕ order, у которых service_id тех Service, что принадлежат Auth::user()->id
        $orders = Order::leftJoin('services', 'orders.service_id','=','services.id')->where('user_id', Auth::user()->id)->get();
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
        $order->save();

        \Session::flash('success','Order added successfully.');
        return Redirect::to('orders');
    }
    public function confirm($id2,$id){
        var_dump($id);
        return Redirect::to('orders');
    }
    public function cancel($id2,$id){
        var_dump($id);
        return Redirect::to('orders');
    }

}
