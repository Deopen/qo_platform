@extends('layouts.app')


@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('css/home.css') }}">
<div class="container">
        <div class="row">
        <div class="col-md-10 col-md-offset-1">

            <div class="panel panel-default">
                <div class="panel-heading" style="
                text-align:center;
                background-color:hsla(170,40%,70%,0.7);
                ">
                <strong>
                {{Auth::user()->getAccessLevel()}} panel
                </strong>
                </div>  
                
                <div class="panel-body" style="
                background-color:hsla(130,60%,10%,0.25);
                ">
                @if(Auth::user()->isProjectOwner())
                <aside>
                <ul class="nav nav-pills nav-justified">
                     
                    <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" 
                    href="#">Project
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="/create_project"><i class="fa fa-btn fa-plus-square"></i>New Project</a></li>
                      @if (Auth::user()->isAdmin())
                      <li><a href="/show_all_projects"><i class="fa fa-btn fa-list-alt"></i>All Projects</a></li>
                      @endif
                      <li><a href="/my_projects"><i class="fa fa-btn fa-files-o"></i>My Projects</a></li> 
                      
                      @if (Auth::user()->isSuperAdmin())
                      <li><a href="/delete_all_projects"><i class="fa fa-btn fa-remove"></i><span style="color: rgba(70,0,0,0.85);font-weight: 700; ">Delete All Projects</span></a></li>
                      @endif
                      
                      
                    </ul>
                    </li>

                    <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" 
                    href="#">Questioner
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="/create_questioner"><i class="fa fa-btn fa-plus-square"></i>New Questioner</a></li>
                      
                      <li><a href="/show_questioners"><i class="fa fa-btn fa-pencil-square-o"></i>All Questioners</a></li> 
                      
                      @if (Auth::user()->isSuperAdmin())
                      <li><a href="/delete_all_questioners"><i class="fa fa-btn fa-remove"></i><span style="color: rgba(70,0,0,0.85);font-weight: 700; ">Delete All Questioners</span></a></li>
                      @endif
                      
                      
                    </ul>
                    </li>

                    <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" 
                    href="#">User
                    <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                      <li><a href="/register"><i class="fa fa-btn fa-user-plus"></i>New User</a></li>
                      @if (Auth::user()->isAdmin())
                      <li><a href="/add_user/bulk_user_registeration"><i class="fa fa-btn fa-user-plus"></i>Bulk Registeration</a></li>
                      <li><a href="/show_project_owners"><i class="fa fa-btn fa-user-secret"></i><span style="color: rgba(90,0,70,0.65);font-weight: 700; ">Show Project Owners</span></a></li>
                      
                      @if (Auth::user()->isSuperAdmin())
                      <li><a href="/delete_all_users"><i class="fa fa-btn fa-remove"></i><span style="color: rgba(70,0,0,0.85);font-weight: 700; ">Delete All Users</span></a></li>
                      @endif
                      
                      @endif
                      <li><a href="/show_users"><i class="fa fa-btn fa-users"></i>Show Users</a></li>

                    </ul>
                    </li>

                </ul>
                </aside>
                @endif
                
                @yield('inner_panel')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection