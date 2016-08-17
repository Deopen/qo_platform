<?php

namespace App\Http\Controllers\Auth;

use Crypt;
use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Log;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';
    /* I ADD THIS */
    protected $username = 'username';
    /* I ADD THIS */
    
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
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
            'name' => 'required|max:255|alpha',
            'family' => 'required|max:255|alpha',
            'gender' => 'required|max:7|in:"male","female","other"',
            'age' => 'required|digits:2',
            'username' => 'unique:users|required|max:255|alpha_dash',
            'email' => 'email|max:255|unique:users',
            'password' => 'required|min:5|confirmed',
            'g-recaptcha-response' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

        $owner_id=null;
        if (Auth::user()) {
            $owner_id=Auth::user()->id;
            Log::info(Auth::user()->getAccessLevel()." ".Auth::user()->username." creating a user ..." );
        }//end if
        
        
        //$encrypted_default_password=Crypt::encrypt("omid123");
		
        $res=User::create([
            'username' => strtolower($data['username']),
            'name' => $data['name'],
            'family' => $data['family'],
            'age' => $data['age'],
            'gender' => $data['gender'],
            'email' => $data['email'],
            'created_by' => $owner_id,
            'password' => bcrypt($data['password']),
        ]);

        Log::info("User ".strtolower($data['username'])." created successfully.");
        
        return $res;
    }
}
