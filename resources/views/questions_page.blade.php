
@extends('layouts.panel')
@section('inner_panel')

@if ($errors->any())
<div style="background-color:rgba(0,0,0,0.6)">                       
<ol type="1" style="text-align:center;list-style-position: inside;">

<span id="errors" class="help-block">
    <strong><font color="white">
    @foreach($errors->all() as $error)
    <li>
    {{ $error }}
    </li>
    @endforeach
    </font></strong>
</span>
</ol>


    <script>

        function pulseAnimationOnError(){

            $("#errors")
                .fadeTo(160,0.3)
                .fadeTo(230,1)
                .fadeTo(200,0.3)
                .fadeTo(400,1)
                .fadeTo(100,0.6)
                .fadeTo(200,1)

        }//end pulse on error



        pulseAnimationOnError()

</script>
</div>                            
@endif
             
              @if( $questions->count()>4 )
               @if(Auth::user()->isAdmin() or Auth::user()->isQuestionerOwner($questioner_id))

                <div class="form-group">
                    
                        <form action="/add_question/toQuestioner_{{$questioner_id}}" method="get">
                        <button type='submit' id="btn_add_question" class="btn btn-primary">
                            <i class="fa fa-btn fa-plus"></i>Add Question
                        </button>
                        </form>
                        
                </div>

                @endif
                @endif

                
                <div class="persian_question">
                   
                    @foreach($questions as $question)
                    
                    <div class="question_containter">
                    
                    <div id="question_div_{{$question->id}}">
                    {{$question->content}}
                    </div>
                    <br><br>
                    <div class="option_container">
                    
                    
                    @foreach($question->getOptions() as $option)
                        <input id="option_input_{{$question->id}}_{{$option->id}}" type="radio" name='option_{{$question->id}}' onclick='sendAnswer({{$option->id}},{{$question->id}})'
                        @if(Auth::user()->getSelectedOptionOfQuestion($question->id)==$option->id)
                        checked="true"
                        @endif
                        ><span class="persian_option"><i></i>
                        
                        <span id="option_span_{{$option->id}}" class="option_span_{{$question->id}}">
                        {{$option->value}}
                        </span>
                        
                        </span><br>
                    @endforeach
                     
                    <!-- Change it for comments -->
                    <!--
                    {{--
                    @foreach(json_decode($question->options) as $p)
                    <input type="radio" name="option_{{$question->id}}" value="{{$p}}"><span class="persian_option">&nbsp&nbsp
                    {{$p}}
                    </span><br>
                    @endforeach
                    --}}
                    -->


                    </div>
                    
                    
                    
                    @if(Auth::user()->isAdmin() or Auth::user()->isQuestionerOwner($questioner_id))
                    <div style="margin-top:5%;">

                            <button class="btn btn-primary" onclick="location.href='/delete_question/inQuestioner_{{$questioner_id}}/question_{{$question->id}}'"
                            >
                                delete
                            </button>

                            <button onclick="edit_question({{$question->id}})" class="btn btn-primary">
                                edit
                            </button>

                            <button class="btn btn-primary">
                                add option
                            </button>        
                    </div>
                    @endif


                    </div>

                    
                    @endforeach

                @if (!Auth::user()->isFinalizedQuestioner($questioner_id))
                <div align="center">
                <form action="/finalize/{{$questioner_id}}" method="get">
                        <button type='submit' class="btn btn-danger">
                            <i class="fa fa-btn fa-check-square-o"></i>Finalize
                        </button>
                </form>
                </div>
                @endif 
                  
                </div>
                

                @if(Auth::user()->isAdmin() or Auth::user()->isQuestionerOwner($questioner_id))

                <div class="form-group">
                    
                        <form action="/add_question/toQuestioner_{{$questioner_id}}" method="get">
                        <button type='submit' id="btn_add_question" class="btn btn-primary">
                            <i class="fa fa-btn fa-plus"></i>Add Question
                        </button>
                        </form>
                        
                </div>

                @endif
                <!-- put if it's a subject -->
                <!--
                </form>    
                -->
                

<script type="text/javascript">
    
    
    function sendAnswer(optionId,questionId){
        
        $.ajax({
            url:"/answer/q_"+questionId+"/option_"+optionId,
            type:"GET",
            dataType:"text"
        }).fail(function(xhr,status,strErr){
            $("#option_input_"+questionId+"_"+optionId).attr("checked",false)
            alert(status+" "+strErr+" Server is unreachable, please try again.")
        }).success(function (result){
            if (result=="success")
                $("#option_input_"+questionId+"_"+optionId).attr("checked",true)
            else {
                $("#option_input_"+questionId+"_"+optionId).attr("checked",false)
                if (result=="finalized"){
                    alert("Sorry, You can't change your answer, Because you finalized questioner.")
                }//end if finalized
            }
        })
    }//end send answer
    
    var editFlag=[]
    
    function edit_question(question_id){
        
        if (editFlag[question_id]!=1){
            var content=$("#question_div_"+question_id).html()
            $("#question_div_"+question_id).html("<textarea id='textarea_question_"+question_id+"' dir='rtl' class='persian_question' name='content' rows=7>"+content.trim()+"</textarea>")
            
            $(".option_span_"+question_id).each(function(index){
                var option=$(this).html().trim()
                $(this).html("<input id='text_"+$(this).attr('id')+"' type='text' class='form-control persian_option' value='"+option+"' style='background-color: rgba(200,200,200,0.5);'>")
                
            })
            
            editFlag[question_id]=1
        }else{
            var content=$("#textarea_question_"+question_id).val()
            var content=$("#question_div_"+question_id).html(content)
            
            $(".option_span_"+question_id).each(function(index){
                var option=$("#text_"+$(this).attr('id')).val()
                var option=$(this).html(option)
            })
            
            editFlag[question_id]=0
        }//end if edit flag!=1
    }//end edit question
    
</script>
@endsection


