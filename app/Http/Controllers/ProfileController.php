<?php

namespace TGChat\Http\Controllers;

use Illuminate\Http\Request;
use TGChat\User;
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
        $id = Auth::user()->id;
        $validator = Validator::make($request->all(),[
            'email'=>'required|unique:users,email,'.$id,
            'firstname'=>'required',
            'lastname'=>'required',
            'patronymic'=>'required',
            'address'=>'required',
            'phone'=>'required',
        ]);

        if($validator->fails()){
            \Session::flash('warning','Please enter the valid details');
            return Redirect::to('/profile')->withInput()->withErrors($validator);
        }
        $user = Auth::user();
        $schedule = '{"Monday":"'.$request['schedule_monday_begin_hours'].':'.$request['schedule_monday_begin_minutes'].'-'.$request['schedule_monday_end_hours'].':'.$request['schedule_monday_end_minutes'].'"'.
            ',"Tuesday":"'.$request['schedule_tuesday_begin_hours'].':'.$request['schedule_tuesday_begin_minutes'].'-'.$request['schedule_tuesday_end_hours'].':'.$request['schedule_tuesday_end_minutes'].'"'.
            ',"Wednesday":"'.$request['schedule_wednesday_begin_hours'].':'.$request['schedule_wednesday_begin_minutes'].'-'.$request['schedule_wednesday_end_hours'].':'.$request['schedule_wednesday_end_minutes'].'"'.
            ',"Thursday":"'.$request['schedule_thursday_begin_hours'].':'.$request['schedule_thursday_begin_minutes'].'-'.$request['schedule_thursday_end_hours'].':'.$request['schedule_thursday_end_minutes'].'"'.
            ',"Friday":"'.$request['schedule_friday_begin_hours'].':'.$request['schedule_friday_begin_minutes'].'-'.$request['schedule_friday_end_hours'].':'.$request['schedule_friday_end_minutes'].'"'.
            ',"Saturday":"'.$request['schedule_saturday_begin_hours'].':'.$request['schedule_saturday_begin_minutes'].'-'.$request['schedule_saturday_end_hours'].':'.$request['schedule_saturday_end_minutes'].'"'.
            ',"Sunday":"'.$request['schedule_sunday_begin_hours'].':'.$request['schedule_sunday_begin_minutes'].'-'.$request['schedule_sunday_end_hours'].':'.$request['schedule_sunday_end_minutes'].'"'.
            '}';
        $user->email = $request['email'];
        $user->firstname = $request['firstname'];
        $user->lastname = $request['lastname'];
        $user->patronymic = $request['patronymic'];
        $user->address = $request['address'];
        $user->phone = $request['phone'];
        $user->welcome_message = $request['welcome_message'];
        $user->schedule = $schedule;
        $user->save();

        \Session::flash('success','Profile changed successfully.');
        return Redirect::to('profile');
    }
}
