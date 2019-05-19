<?php

namespace TGChat\Http\Controllers;


use Auth;
use Validator;
use TGChat\Service;
use TGChat\Http\Controllers\BotController;
use Illuminate\Support\Facades\Redirect;
use TGChat\Http\Controllers\Controller;
use TGChat\Bot;
use TelegramBot\Api\Client;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index(){
        $services = Service::where('user_id', Auth::user()->id)->get();
        return view('services', compact('services'));
    }
    public function addService(Request $request){
        $validator = Validator::make($request->all(),[
            'service_name'=>'required',
            'duration'=>'required',
            'amount'=>'required'
        ]);

        if($validator->fails()){
            \Session::flash('warning','Please enter the valid details');
            return Redirect::to('/services')->withInput()->withErrors($validator);
        }

        $service = new Service();
        $service->service_name = $request['service_name'];
        $service->user_id = Auth::user()->id;
        $service->duration = $request['duration'];
        $service->amount = $request['amount'];
        $service->save();
        \Session::flash('success','Service added successfully.');
        return Redirect::to('services');
    }
    public function showSingleService($single){
        $service = Service::where('id',$single)->get()->first();
        return view('services.single', compact('service'));
    }
    public function edit(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'service_name'=>'required',
            'duration'=>'required',
            'amount'=>'required'
        ]);

        if($validator->fails()){
            \Session::flash('warning','Please enter the valid details');
            return Redirect::to('/services/'.$request['id'])->withInput()->withErrors($validator);
        }

        $service = Service::where('id',$id)->get()->first();
        $service->service_name = $request['service_name'];
        $service->duration = $request['duration'];
        $service->amount = $request['amount'];
        $service->save();

        \Session::flash('success','Service changed successfully.');
        return Redirect::to('services');
    }
    public function destroy(Request $request, $id){
        $service = Service::where('id',$id)->get();
        $service->events()->delete();
        $service->delete();
        return Redirect::to('/services');
    }
}
