<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Controllers\Auth\AuthController;

class login extends Controller
{
    //

    function showLoginPage(){

        
    	return view("login");


    }//end show login page

    function validateLogin(Request $req){

    	var_dump($req->input("username"));
    	var_dump($req->input("pass"));
        view()->share('access_level',Auth::user()->access_level);
    }//end validate login


    function logout() {

    	Auth::logout();

    }//end logout

}
