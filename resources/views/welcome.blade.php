@extends('layouts.panel')

@section('inner_panel')
<div class="user_tbl">
<table class="home_tbl">
                   
@if(Auth::user()->isAdmin())
                    <tr id="monitor_tr">
                        <td style="background-color:rgba(0,0,0,0.8);" colspan="100%">
                        <ol type="1" style="text-align:center;list-style-position: inside;">
                        <span class="help-block" style="color:rgba(0,255,0,0.7)">
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
                    

                    <tr>
                        <th>Tasks</th>
                        <th>Progress</th>
                        <th>Finalize</th>
                        <th>Score</th>
                    </tr>
                    


                    @foreach(Auth::user()->getMyQuestioners() as $questioner)
                    <tr>
                    
                    <td>
                    <strong>
                    <a style="font-family:Arial;" href='/questions_page/{{$questioner->id}}'>{{$questioner->name}}</a>
                    </strong>
                    </td>

                    <td>
                    <progress value="{{Auth::user()->getAnsweredQuestionCount($questioner->id)}}" max="{{$questioner->getQuestionCount()}}">
                    </progress>
                    <br>
                    @if($questioner->getQuestionCount()!=0)
                    {{ceil((Auth::user()->getAnsweredQuestionCount($questioner->id)/$questioner->getQuestionCount())*100)}}%
                    @else
                    0%
                    @endif
                    
                    <td id="finalized_td_{{$questioner->id}}">
                    @if (Auth::user()->isFinalizedQuestioner($questioner->id))
                        Finalized
                    @else
                        <a id="a_{{$questioner->id}}" data-name="{{$questioner->name}}" onclick="finalize({{$questioner->id}})"><i class="fa fa-btn fa-check-square"></i>Finalize</a>
                    @endif
                    </td>

                    @if($questioner->isMultiClass())
                    <td>
                    <a href="/full_result/{{$questioner->id}}">
                    <i class="fa fa-btn fa-bar-chart"></i>
                    Results
                    </a>

                    </td>
                    @else
                    <td><i class="fa fa-btn fa-mortar-board"></i>
                       <br>
                        <span id="myScore_{{$questioner->id}}">{!!Auth::user()->getStyledScore($questioner->id)!!}</span>
                    </td>
                    @endif

                    </tr>
                    @endforeach
                    </table>

                
            
        
    
</div>

<script>
function finalize(questioner_id){
    var questioner_name=$("#a_"+questioner_id).data('name')
    r=confirm("Are you sure for finilizing questioner "+questioner_name+" ?")
    if (r==true){
        $.ajax({
            url:"/finalize/"+questioner_id,
            type:"GET",
            dataType:"text"
        }).success(function(result){
            if (result=="success"){
                alert("Questioner "+questioner_name+" finalized successfully.")
                $('#finalized_td_'+questioner_id).html("Finalized")
                $.ajax({
                    url:"/get_score/"+questioner_id,
                    type:"GET",
                    dataType:"text"
                }).success(function (result){
                    //alert(result)
                    $('#myScore_'+questioner_id).html(result)
                })
            }
            else if (result=="duplicate")
                alert("You finalize it before!")
        })
    }//end
}//end finalize
</script>
@endsection
