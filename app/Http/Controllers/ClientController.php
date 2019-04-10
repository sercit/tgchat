<?php

namespace App\Http\Controllers;

use App\Client;
use Auth;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class ClientController extends Controller
{
       public function __construct(){
            $this->middleware('auth');
        }
        public function showAllClients(){
            $clients = Client::where('user_id',Auth::user()->id)->get();
            return view('clients', compact('clients'));
        }
        public function addClient(Request $request){
            $validator = Validator::make($request->all(),[
                'client_name'=>'required',
                'phone'=>'required',
            ]);

            if($validator->fails()){
                \Session::flash('warning','Please enter the valid details');
                return Redirect::to('/clients')->withInput()->withErrors($validator);
            }

            $client = new Client();
            $client->client_name = $request['client_name'];
            $client->user_id = Auth::user()->id;
            $client->phone = $request['phone'];
            $client->save();

            \Session::flash('success','Client was added successfully.');
            return Redirect::to('clients');
        }
        public function showSingleClient($single){
            $client = Client::where('id',$single)->get()->first();
            return view('clients.single', compact('client'));
        }
    public function edit(Request $request, $id){
        $validator = Validator::make($request->all(),[
            'client_name'=>'required',
            'phone'=>'required',
        ]);

        if($validator->fails()){
            \Session::flash('warning','Please enter the valid details');
            return Redirect::to('/clients/'.$request['id'])->withInput()->withErrors($validator);
        }

        $client = Client::where('id',$id)->get()->first();
        $client->client_name = $request['client_name'];
        $client->phone = $request['phone'];
        $client->save();

        \Session::flash('success','Client changed successfully.');
        return Redirect::to('clients');
    }
    public function destroy(Request $request, $id){
        $clients = Client::where('id',$id)->get()->first();
        $clients->delete();
        return Redirect::to('/clients');
    }
}
