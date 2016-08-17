<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Panel</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <style>
        body {
            font-family: 'Lato';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <!--<li><a href="{{ url('/home') }}">Home</a></li> -->
                    <li><img id="img_banner" src=" {{ asset('img/qo 4.svg') }} " height="50" style="margin:5px;" ></img></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    <li><a href="{{ url('/about') }}">About</a></li>
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                <strong>{{ Auth::user()->name }} </strong><span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                
                                <li><a href="{{ url('/home') }}"><i class="fa fa-btn fa-home"></i>Home</a></li>

                                @if (Auth::user()->isProjectOwner())
                                <li><a href="{{ url('/register') }}"><i class="fa fa-btn fa-user-plus"></i>Add user</a></li>
                                <li><a href="{{ url('/show_users') }}"><i class="fa fa-btn fa-users"></i>Show users</a></li>
                                @endif
                                
                                @if (Auth::user()->isAdmin())
                                <li><a href="/show_all_projects"><i class="fa fa-btn fa-list-alt"></i>Show Projects</a></li>
                                <li><a href="/show_project_owners"><i class="fa fa-btn fa-user-secret"></i><span style="color: rgba(90,0,70,0.70);font-weight: 700; ">Show Project Owners</span></a></li>
                                
                                <li><a href="/log/all"><i class="fa fa-btn fa-eye"></i><span style="color: rgba(0,90,0,0.65);font-weight: 700; ">Monitoring</span></a></li>
                                
                                @if(Auth::user()->isSuperAdmin())
                                <li><a onclick="backupRequest()"><i class="fa fa-btn fa-save"></i><span style="color: rgba(50,0,0,0.85);font-weight: 700; ">Backup</span></a></li>
                                
                                <li><a onclick="restoreRequest()"><i class="fa fa-btn fa-repeat"></i><span style="color: rgba(0,0,70,0.85);font-weight: 700; ">Restore</span></a></li>
                                
                                <li><a onclick="deleteBackupRequest()"><i class="fa fa-btn fa-remove"></i><span style="color: rgba(70,0,0,0.85);font-weight: 700; ">Delete Backup</span></a></li>
                                @endif
                                
                                
                                @else

                                @if (Auth::user()->isProjectOwner())
                                <li><a href="/my_projects"><i class="fa fa-btn fa-files-o"></i>My Projects</a></li> 
                                <li><a href="/create_project"><i class="fa fa-btn fa-plus-square"></i>New Project</a></li>
                                @endif

                                @endif



                                <li><a href="{{ url('/show_questioners') }}"><i class="fa fa-btn fa-file-text-o"></i>Show Questioners</a></li>
                                
                                <li><a href="{{ url('/edit') }}"><i class="fa fa-btn fa-edit"></i>Edit</a></li>

                                <li><a href="{{ url('/') }}"><i class="fa fa-btn fa-tasks"></i>Tasks</a></li>                                
                                
                                
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>

                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    
<script>
    function backupRequest(){
        $.ajax({
            url:"/backup",
            type:"GET",
            dataType:"text"
        });
    }//end back up request
    
    function deleteBackupRequest(){
        $.ajax({
            url:"/delete_backup",
            type:"GET",
            dataType:"text"
        });
    }//end back up request
    
    function restoreRequest(){
        $.ajax({
            url:"/restore",
            type:"GET",
            dataType:"text"
        }).success(function(result){
            if (result=="success")
                window.location.reload()
        });
    }//end back up request
</script>
</body>
</html>
