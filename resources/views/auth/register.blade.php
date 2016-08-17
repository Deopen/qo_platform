@extends('layouts.app')
<script src='https://www.google.com/recaptcha/api.js'></script>
<title>Register</title>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
            
            	@if(isset($edit))
            	<div class="panel-heading">Edit</div>
            	@else
                <div class="panel-heading">Register</div>
                @endif
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" 
                    @if(isset($edit))
                action="/edit_user/{{Auth::user()->id}}"
                    @else
                    action="{{ url('/register') }}"
                    @endif
                    >
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" 
                                @if(isset($edit))
                                value="{{$name}}"
                                @else
                                value="{{ old('name') }}"
                                @endif
                                >

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- I ADD THESE -->


                        <div class="form-group{{ $errors->has('family') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Family</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="family" 
                                @if(isset($edit))
                                value="{{$family}}"
                                @else
                                value="{{ old('family') }}"
                                @endif
                                ">

                                @if ($errors->has('family'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('family') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Username</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="username" 
                                
                                @if (isset($edit))
                                value="{{$username}}"
                                @else
                                value="{{ old('username') }}"
                                @endif
                                ">

                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group{{ $errors->has('age') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Age</label>

                            <div class="col-md-6">
                                <input type="number" max=100 class="form-control" name="age" 
                                @if (isset($edit))
                                value="{{$age}}"
                                @else
                                value="{{ old('age') }}"
                                @endif
                                ">

                                @if ($errors->has('age'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('age') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Gender</label>

                            <div class="col-md-6">
                                
                                <input type="radio" value="male" name="gender"
                                @if(isset($edit))
	                                @if(strtolower($gender)=="male")
	                                	checked
	                                @endif
                                @else
                                    @if(old('gender')=="male")
                                        checked 
                                    @endif
	                            @endif
                                >&nbspMale<br>
                                
                                <input type="radio" value="female" name="gender"
                                @if(isset($edit))
	                                @if(strtolower($gender)=="female")
	                                	checked
	                                @endif
                                @else
                                    @if(old('gender')=="female")
                                        checked 
                                    @endif
	                            @endif
                                >&nbspFemale<br>
                                
                                <input type="radio" value="other" name="gender"
                                @if(isset($edit))
	                                @if(strtolower($gender)=="other")
	                                	checked
	                                @endif
                                @else
                                    @if(old('gender')=="other")
                                        checked 
                                    @endif
	                            @endif
                                >&nbspOther<br>

                                @if ($errors->has('gender'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- I ADD THIS -->

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">E-Mail Address</label>
                            
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" 
                                @if(isset($edit))
                                value="{{$email}}"
                                @else
                                value="{{ old('email') }}"
                                @endif
                                >

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        
                        @if(!isset($edit))
                        <div class="row">
                        <div class="col-xs-12 col-md-12
                        col-xs-offset-0 col-md-offset-4 col-sm-offset-0">
                        
                        <div  
                        class="g-recaptcha" id="captcha" 
                        data-sitekey="{{env("RE_CAP_SITE_KEY")}}" 
                        
                        ></div>


                        @if ( $errors->has('g-recaptcha-response'))
                            <span style="color:red">
                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                            </span>
                        @endif
                        </div>
                        </div>
                        @endif
                        
                        
                        <div class="form-group" style="margin-top:10px;">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    
                                	@if(isset($edit))
                                	<i class="fa fa-btn fa-edit"></i>
                                	Edit
                                	@else    
                                    <i class="fa fa-btn fa-user"></i>
                                    Register
                                	@endif
                                </button>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
