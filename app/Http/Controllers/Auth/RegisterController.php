<?php

namespace TGChat\Http\Controllers\Auth;

use TGChat\User;
use TGChat\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Log;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'patronymic' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \TGChat\User
     */
    protected function create(array $data)
    {
        if($data['phone'][0] == '8'){
           $data['phone'][0] = '7';
        }elseif($data['phone'][0] == '+'){
            $data['phone'] = substr($data['phone'],1);
        }
        Log::debug($data);
        return User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'patronymic' => $data['patronymic'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'welcome_message' => 'Добро пожаловать',
            'schedule' => '{"Monday": "10:00-19:00", "Tuesday":"10:00-19:00","Wednesday":"10:00-19:00","Thursday":"10:00-19:00","Friday":"10:00-19:00","Saturday":"10:00-19:00","Sunday":"10:00-19:00"}',
            'paid_until' => date('Y-m-d H:i:s', strtotime("+30 days")),
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
            'telegram_user_token' => $this->generateRandomString(5),

        ]);
    }

    /**
     * Create the random string
     *
     * @param int $length - length of the return string
     * @return string
     */
    protected function generateRandomString(int $length = 5):string {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
