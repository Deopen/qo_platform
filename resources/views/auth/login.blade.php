
<!-- 
Written By Omid Yaghoubi (DeOpen)
DeOpenMail@gmail.com
+98935-822-23-55
-->

<!DOCTYPE html>
<title>Login</title>
@extends('layouts.app')
@section('content')
<html>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <head>
    <title>Login</title>
    </head>
    
    
    <body>
    
    <div class="container-fluid">
    <div class="row">
    <div class="col-md-8 col-md-offset-2">
    
    
    <form class="form-horizontal" action="{{ url('/login') }}" method="post" name="login_form">
        
        {!! csrf_field() !!}
        
        <link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}">
        
        <!--<div class="login_container" id="login_container">-->
        <div class="panel panel-default panelStyle">
        <div class="panel-body">
        
        <div class="row">
        <div class="col-md-12">
        <div class="banner">
        <img id="img_banner" src=" {{ asset('img/qo 4.svg') }} " height="100" ></img>  
        </div> <!-- end loginBanner -->
        </div>
        </div>
            
            <div class="user_pass_containter">
            
            <div class="row">
            <div class="col-md-12">
            <input class="form-control" type="text" required name="username" placeholder="Username" value="{{ old('username') }}">
            @if ($errors->has('username'))
                <span class="error">
                <strong>{{ $errors->first('username') }}</strong>
                </span>
            @endif
            </div>
            </div>

            <div class="row">
            <div class="col-md-12">
            <input class="form-control" type="password" required name="password" placeholder="Password">
            @if ($errors->has('password'))
                <span class="error">
                <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
            </div>
            </div>

            
            <div class="row">
            <div class="col-md-12">
            <input id="btn" class="btn btn-default btn-block black" style="
            background-color: hsla(190, 35%, 30%,0.35);
            margin-top: 8%;
            " 
            type="submit" value="Login" name="login">
            </div>
            </div>
            
            
            
            <!--
            <div class="row">
            <div class="col-md-12">
            <div class="myCheckbox">
                    <input type="checkbox" name="remember" id="remember_me">
                    <font color="hsla(290, 85%, 35%,0.6)">
                    <label for="remember_me" >Remember Me</label>
                    </font>
            </div>
            </div>
            </div>
            -->
                
            
            
           <div class="row">
            <div class="col-md-12 col-md-offset-3 col-xs-12 col-xs-offset-0 col-sm-offset-4">
                <div class="checkbox myCheckbox">
                    <label>
                        <input id="remember_me" type="checkbox" name="remember"> Remember Me
                    </label>
                </div>
            </div>
            </div>
                
            
            
            <div class="row" >
            <div class="col-xs-12 col-md-12
            col-xs-offset-0 col-md-offset-2 col-sm-offset-4" >
            <div  
            class="g-recaptcha" id="captcha" 
            data-sitekey="{{env("RE_CAP_SITE_KEY")}}" 
            data-theme= "dark" data-size="compact"
                
             
            ></div>

            
            @if ($errors->has('g-recaptcha-response'))
                <span class="error">
                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                </span>
            @endif
            </div>
            </div>
            


        </div> <!-- end user pass container -->
        
        

        </div> <!-- end panel body -->
        </div> <!-- end login fold -->
        

    </form>
    
    
    </div> <!-- End Col -->
    </div> <!-- End Row -->
    </div> <!-- End Container -->
    

    <script type="text/javascript">
    
        function printLn(element){

            document.write(element+"<br>")
        }//end print line

        function getEl(id){
            return document.getElementById(id)
        }//end get el

        function captchaScaleDown() {
            var loginContainerHeight=getEl("login_container").offsetHeight
            var loginContainerWidth=getEl("login_container").offsetWidth
            var el=getEl("captcha")
            var diffW=loginContainerWidth*0.0025
            var diffH=loginContainerHeight*0.0020
            
            
            //el.style.transform="scale("+diffW+","+diffH+")"
            el.style.transform="scale(0.8,0.8)"
        }//end captcha scale down
        
        window.onresize=captchaScaleDown;
        window.onload=captchaScaleDown;
    </script>

    </body>


</html>

@endsection
