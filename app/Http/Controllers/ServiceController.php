<?php

namespace App\Http\Controllers;


use Auth;
use Validator;
use App\Service;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;

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
}
