@extends('layouts.panel')



@section('inner_panel')


               
                <div class="user_tbl" id="user_tbl">
                <table class="home_tbl">
                    @if(Auth::user()->isAdmin())
                    <tr id="monitor_tr">
                        <td style="background-color:rgba(0,0,0,0.8);" colspan="100%">
                        <ol type="1" style="text-align:center;list-style-position: inside;">
                        <span  class="help-block" style="color:rgba(0,255,0,0.7)">
                            <strong id="monitor_line">
                            Monitoring Panel
                            </strong>
                                <script>
                                    lines=1
                                    var myInt=0
                                    $(function() {
                                        $('#monitor_tr').hover(function () {
                                            $(this).stop(true, false).animate({
                                                height: "100px"
                                                
                                            });
                                            lines=4
                                        }, function () {
                                            $(this).stop(true, false).animate({
                                                height: "30px"
                                                
                                            });
                                            lines=1
                                            $('#monitor_line').html("Monitoring Panel")
                                        });
                                        
                                        $('#monitor_tr').on("click",function() {
                                           
                                            clearInterval(myInt)
                                            myInt=setInterval(getLastLineOfLog,3000)
                                            if(lines==4){
                                                $(this).stop(true, false).animate({
                                                height: "30px"
                                                });
                                                lines=1
                                                $('#monitor_line').html("Monitoring Panel")
                                            }else{
                                                $(this).stop(true, false).animate({
                                                height: "100px"
                                                });
                                                lines=4
                                            }
                                            
                                        })
                                    })//end ready
                                    
                                    function getLastLineOfLog(){
                                        $.ajax({
                                            url:"/log/last/"+lines,
                                            type:"GET",
                                            dataType:"text"
                                        }).success(function(result){
                                            $('#monitor_line').html(result.replace('local.INFO:',''))
                                             
                                           clearInterval(myInt)
                                           myInt=setInterval(getLastLineOfLog,3000)
                                        }).fail(function(xhr,status,strErr){
                                            //$('#monitor_line').html(status+': '+strErr)
                                            //clearInterval(myInt)
                                            clearInterval(myInt)
                                            myInt=setInterval(getLastLineOfLog,3000)
                                            $('#monitor_line').html("getting information from server...")
                                        })
                                    }//end function getLastLineLog
                                    clearInterval(myInt)
                                    myInt=setInterval(getLastLineOfLog,3000)

                                </script>
                            
                        </span>
                        </ol>
                        </td>
                    </tr>
                    @endif

                   
                   @if ($errors->any())
                        <tr id="errors_td">
                        <td colspan="100%">
                        <ol type="1" style="text-align:center;list-style-position: inside;">
                        <span class="help-block">
                            <strong><font color="red">
                            @foreach($errors->all() as $error)
                            <li>
                            {{ $error }}
                            </li>
                            @endforeach
                            </font></strong>
                        </span>
                        </ol>
                        </td>
                        </tr>
                            <script>

                                function pulseAnimationOnError(){

                                    $("#errors_td")
                                        .fadeTo(160,0.3)
                                        .fadeTo(230,1)
                                        .fadeTo(200,0.3)
                                        .fadeTo(400,1)
                                        .fadeTo(100,0.6)
                                        .fadeTo(200,1)

                                }//end pulse on error
                                
                                
                            
                                pulseAnimationOnError()
                            
                            </script>
                        @endif

                    @if(isset($users))
                        
                        
                        <tr>
                        <th>id</th>
                        <th>username</th>
                        <th>name</th>
                        <th>family</th>
                        
                        @if(!isset($project_id) and !isset($project_owner_view))
                        <th>gender</th>
                        <th>age</th>
                        <th id="th_email">email</th>
                        @endif

                        @if(Auth::user()->isAdmin())
                            @if(isset($project_owner_view))
                                <th>project limit</th>
                                <th>projects</th>
                                <th>questioner limit</th>
                                <th>questioners</th>
                            @endif
                        <th>access_level</th>
                        @endif

                        @if(Auth::user()->isProjectOwner())
                        <th>pass</th>
                        @endif
                        
                        @if(isset($project_id))
                        <th>Add/Remove</th>
                        @endif

                        <th>edit</th>
                        <th>delete</th>
                        </tr>    
                        @foreach($users as $user)
                        <form method='POST' id="form_{{ $user->id }}" action='/edit_user/{{$user->id}}'>
                        {!! csrf_field() !!}

                        <tr id="usr_row_{{$user->id}}"> 

                        <td id="id.field.{{$user->id}}">{{$user->id}}</td>
                        <td class="breakableTd" id="username.field.{{$user->id}}">{{$user->username}}</td>
                        <td class="breakableTd" id="name.field.{{$user->id}}">{{$user->name}}</td>
                        <td class="breakableTd" id="family.field.{{$user->id}}">{{$user->family}}</td>

                        @if(!isset($project_id) and !isset($project_owner_view))
                        <td id="gender.field.{{$user->id}}">{{$user->gender}}</td>
                        <td id="age.field.{{$user->id}}">{{$user->age}}</td>
                        <td style="max-width:20pt" id="email.field.{{$user->id}}" data-email='{{$user->email}}'>
                        @if (strlen($user->email)!=0)
                        <a onclick="alert('{{$user->email}}')">Show</a>
                        @endif
                        </td>
                        
                        
                        @endif

                        @if(Auth::user()->isAdmin())
                            @if(isset($project_owner_view))
                                <td id="project_limit.field.{{$user->id}}">{{$user->project_limit}}</td>
                                <td id="projects.field.{{$user->id}}">{{$user->getProjectCount()}}</td>
                                
                                <td id="questioner_limit.field.{{$user->id}}">{{$user->questioner_limit}}</td>

                                <td id="projects.field.{{$user->id}}">{{$user->getQuestionerCount()}}</td>

                            @endif
                        <td id="access_level.field.{{$user->id}}" style="
                        @if($user->access_level=="admin")
                        color: rgba(255,0,0,0.65);
                        font-weight: bold;
                        @endif

                        @if($user->access_level=="project_owner")
                        color: rgba(90,0,70,0.65);
                        font-weight: bold;
                        @endif
                        ">
                        {{$user->access_level}}
                        </td>
                        
                        @endif

                        @if(Auth::user()->isProjectOwner())
                        <td>
                            <a onclick="getPass({{$user->id}})"><i class="fa fa-key"></i></a>
                        </td>
                        @endif

                        @if(isset($project_id))
                        <td>
                        @if(in_array($user->id,$added_users))
                        <a href="/remove_user/user_{{$user->id}}/fromProject_{{$project_id}}"><i class="fa fa-btn fa-remove"></i>Remove from Project</a>
                        @else
                        <a href="/add_user/user_{{$user->id}}/toProject_{{$project_id}}"><i class="fa fa-btn fa-plus-square"></i>Add to Project</a>
                        @endif
                        </td>
                        @endif


                        
                        <td id="edit_td_{{$user->id}}">
                        {{--
                        <!--<input id="edit_btn_{{$user->id}}" type="button" value="edit" onclick="edit_user({{$user->id}})">-->
                        --}}
                        <a id="edit_btn_{{$user->id}}" onclick="
                        @if(isset($project_id) or isset($project_owner_view))
                        edit_user({{$user->id}},false)
                        @else
                        edit_user({{$user->id}},true)
                        @endif
                        " >
                        
                        <i class="fa fa-btn fa-edit"></i>Edit</a></td>

                        <td><!--<input type="button" value="delete" onclick="location.href='delete_user/{{$user->id}}'">-->
                        <a href="/delete_user/{{$user->id}}"><i class="fa fa-btn fa-user-times"></i>Delete</a>
                        </td>
                        
                        
                        </tr>
                        </form>
                        @endforeach
                        

                    @endif

                    @if(isset($projects))
                        

                        <tr>
                        <th>id</th>
                        <th>Project name</th>
                        <th>Owner</th>
                        <th>Questioner</th>
                        <th>User</th>
                        <th>Add/Remove User</th>
                        <th>Bulk User Registeration</th>
                        <th>Add/Remove Questioner</th>
                        <th>Edit</th>
                        <th>Delete</th>
                        </tr>    
                        @foreach($projects as $project)
                        <form method='POST' id="form_{{ $project->id }}" action='edit_project/{{$project->id}}'>
                        {!! csrf_field() !!}

                        <tr id="prj_row_{{$project->id}}"> 
                        <td id="id.field.{{$project->id}}">{{$project->id}}</td>
                        <td class="breakableTd" id="name.field.{{$project->id}}">{{$project->name}}</td>
                        <td class="breakableTd" id="owner.field.{{$project->id}}">{{$project->getOwnerUsername()}}</td>
                        <td id="questioner_count.field.{{$project->id}}">{{$project->getQuestionerCount()}}</td>
                        <td id="user_count.field.{{$project->id}}">{{$project->getUserCount()}}</td>
                        
                        
                        <!--<input id="add_btn_{{$project->id}}" type="button" value="add" onclick="location.href='add_questioner/{{$project->id}}'">-->

                        <td>
                        <a id="add_users_btn_{{$project->id}}" href="add_user/toProject_{{$project->id}}"><i class="fa fa-btn fa-user-plus"></i>Add/Remove User</a>
                        </td>

                        <td>
                        <a id="add_questioner_btn_{{$project->id}}" href="/bulk_user_registeration/project_{{$project->id}}"><i class="fa fa-btn fa-users"></i>Bulk User Registeration</a>
                        </td>

                        <td>
                        <a id="add_questioner_btn_{{$project->id}}" href="add_questioner/toProject_{{$project->id}}"><i class="fa fa-btn fa-plus-circle"></i>Add/Remove Questioner</a>
                        </td>


                        <td id="edit_td_{{$project->id}}"><!--<input id="edit_btn_{{$project->id}}" type="button" value="edit" onclick="edit_project({{$project->id}})">-->
                        <a id="edit_btn_{{$project->id}}" onclick="edit_project({{$project->id}})" ><i class="fa fa-btn fa-edit"></i>Edit</a>
                        </td>

                        <td><!--<input type="button" value="delete" onclick="location.href='delete_project/{{$project->id}}'">-->
                        <a href="/delete_project/{{$project->id}}"><i class="fa fa-btn fa-remove"></i>Delete</a>
                        </td>
                        
                        
                        </tr>
                        </form>
                        @endforeach
                        

                    @endif

                    @if(isset($questioners))
                        

                        <tr>
                        <th>id</th>
                        <th>Name</th>
                        <th>Owner</th>
                        <th>Description</th>
                        <th>Questions count</th>
                        <th>Accessibility</th>
                        <th>Show Questions</th>
                        <th>Add Score Map</th>
                        
                        @if(isset($project_id))
                        <th>Add/Remove To Project</th>
                        @endif

                        <th>Edit</th>
                        <th>Delete</th>
                        </tr>    
                        @foreach($questioners as $questioner)
                        <form method='POST' id="form_{{ $questioner->id }}" action='/edit_questioner/{{$questioner->id}}'>
                        {!! csrf_field() !!}

                        <tr id="qstner_row_{{$questioner->id}}"> 
                        <td id="id.field.{{$questioner->id}}">{{$questioner->id}}</td>
                        <td class="breakableTd" id="name.field.{{$questioner->id}}">{{$questioner->name}}</td>

                        <td id="owner.field.{{$questioner->id}}">{{$questioner->getOwnerUsername()}}</td>

                        <td class="breakableTd" id="description.field.{{$questioner->id}}">
                        {{$questioner->description}}</td>
                        
                        <td id="questions_count.field.{{$questioner->id}}">{{$questioner->getQuestionCount()}}</td>

                        <td id="accessibility.field.{{$questioner->id}}">{{$questioner->accessibility}}</td>
                        
                        <td><!--<input id="add_btn_{{$questioner->id}}" type="button" value="show questions" onclick="location.href='questions_page/{{$questioner->id}}'">-->
                        <a href="/questions_page/{{$questioner->id}}"><i class="fa fa-btn fa-list-ol"></i>Show Questions</a>
                        </td>
                        
                        <td>
                        <a href="/add_score_map/{{$questioner->id}}"><i class="fa fa-btn fa-share"></i>Add Score Map</a>
                        </td>

                        @if(isset($project_id))
                        <td>
                        @if(in_array($questioner->id,$added_questioners))
                        <a href="/remove_questioner/questioner_{{$questioner->id}}/fromProject_{{$project_id}}"><i class="fa fa-btn fa-remove"></i>Remove from Project</a>
                        @else
                        <a href="/add_questioner/questioner_{{$questioner->id}}/toProject_{{$project_id}}"><i class="fa fa-btn fa-plus-square"></i>Add to Project</a>
                        @endif
                        </td>
                        @endif


                        <td id="edit_td_{{$questioner->id}}"><!--<input id="edit_btn_{{$questioner->id}}" type="button" value="edit" onclick="edit_questioner({{$questioner->id}})">-->
                        <a id="edit_btn_{{$questioner->id}}" onclick="edit_questioner({{$questioner->id}})" ><i class="fa fa-btn fa-edit"></i>Edit</a>
                        </td>

                        <td><!--<input type="button" value="delete" onclick="location.href='delete_questioner/{{$questioner->id}}'">-->
                        <a href="/delete_questioner/{{$questioner->id}}"><i class="fa fa-btn fa-remove"></i>Delete</a>
                        </td>
                        
                        
                        </tr>
                        </form>
                        @endforeach
                        

                    @endif

                </div>


<script type="text/javascript">
    
    
    function request_for_generate_pass(id){
        $.ajax({
            url:"/generate_pass/"+id,
            type:"GET",
            dataType:"text"
        }).success(function(result){
            alert(result)
        }).fail(function(xhr,status,strErr){
            alert(status+': '+strErr)
        })
    }//end request for generate pass
    
    function getPass(id){
        $.ajax({
            url:"/get_pass/"+id,
            type:"GET",
            dataType:"text"
        }).success(function(result){
            if (result!="None"){
                alert(result)
            }else{
                r=confirm("This user has not any default pass, Do you want to generate one?")
                if (r==true){
                    request_for_generate_pass(id)
                }//end if r==true
            }//end if else !=None
        }).fail(function(xhr,status,strErr){
            alert(status+': '+strErr)
        })
    }//end get pass
    
    var editing=new Array();

    function getEl(id){
        return document.getElementById(id)
    }//end get el
    
    function doEditableThisCell(cellId){


        var el = getEl(cellId)
        var oldName=(el.innerHTML).trim()
        var sizeOfField=oldName.length
        
        if (oldName==""){
            oldName=""
            sizeOfField=1
        }

        
        //alert(oldName)
        var fieldName=cellId.split(".")[0]
        var id=cellId.split(".")[cellId.split(".").length-1]
        //alert(fieldName)
        var inputType=fieldName==="email"?'email':'text'
        el.innerHTML="<input type='"+inputType+"' name='"+fieldName+"' style='text-align: center'  value='"+oldName+"' size='"+sizeOfField+"' form='form_"+id+"'>"
        
        //alert(el.innerHTML)
        

    }//end doing editable

    function addFormToRow(id) {

        var el=getEl("edit_td_"+id)
        var editBtn=getEl("edit_btn_"+id)
        
        editBtn.addEventListener("click",function () {getEl("form_"+id).submit()})

        window.addEventListener("keydown",
            function (event) { 
                if(event.keyCode==13) 
                    getEl("form_"+id).submit()}
                )
        
    }//end adding form to row

    function edit_user(id,fullFlag){
        //alert("edit function runed")   
        if (editing.indexOf(id)==-1) {
            
            editing.push(id)
            doEditableThisCell("name.field."+id)
            doEditableThisCell("family.field."+id)

            if (fullFlag){
                doEditableThisCell("age.field."+id)
                doEditableThisCell("gender.field."+id)
                
                getEl("email.field."+id).innerHTML=getEl("email.field."+id).dataset.email
                
                $(getEl("email.field."+id)).width("+=80px")
                $(getEl("age.field."+id)).width("+=20px")
                
                doEditableThisCell("email.field."+id)
            }

            @if(isset($project_owner_view))
                @if(Auth::user()->isAdmin())
                    doEditableThisCell("project_limit.field."+id)
                    doEditableThisCell("questioner_limit.field."+id)
                @endif
            @endif

            doEditableThisCell("username.field."+id)
            @if(Auth::user()->isAdmin())  
            doEditableThisCell("access_level.field."+id)
            @endif

            addFormToRow(id)
        }else{
            editing.pop(id)
        }//end if else being in editing mode

    }//end edit user



    function edit_project(id){
        //alert("edit function runed")   
        if (editing.indexOf(id)==-1) {
            editing.push(id)
            doEditableThisCell("name.field."+id);
            addFormToRow(id)
        }else{
            editing.pop(id)
        }//end if else being in editing mode

    }//end edit user

    function edit_questioner(id){
        //alert("edit function runed")   
        if (editing.indexOf(id)==-1) {
            editing.push(id)
            doEditableThisCell("name.field."+id);
            doEditableThisCell("description.field."+id);
            @if(Auth::user()->isAdmin())
                doEditableThisCell("accessibility.field."+id);
            @endif
            addFormToRow(id)
        }else{
            editing.pop(id)
        }//end if else being in editing mode

    }//end edit user


</script>

@endsection
