<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Validator;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function show(){
        $user = Auth::user();
        return view('profile', compact('user'));
    }
    public function editProfile(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|unique:users',
            'firstname'=>'required',
            'lastname'=>'required',
            'patronymic'=>'required',
            'address'=>'required',
        ]);

        if($validator->fails()){
            \Session::flash('warning','Please enter the valid details');
            return Redirect::to('/profile')->withInput()->withErrors($validator);
        }

        $user = Auth::user();
        $user->email = $request['email'];
        $user->save();

        \Session::flash('success','Profile changed successfully.');
        return Redirect::to('profile');
    }
}
