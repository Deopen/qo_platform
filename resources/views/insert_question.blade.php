@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Insert Question</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" id="insert_question_form" action="{{ url('/insert_question') }}">
                     <style type="text/css">
                        
                        textarea{
                            text-align: right;
                            direction: rtl;
                            font-family: arial;
                            font-weight:550;      
                        }
                        
                        input.addOption{
                            margin-top: 2%;
                        }
                        
                    </style>

                        {!! csrf_field() !!}
                        <input type="hidden" name="questioner_id" value="{{$questioner_id}}">

                        <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Content</label>
                       
                            <div class="col-md-6">
                                
                                
                                <textarea dir="rtl" id='txt_content' class="persian_question form-control" name='content' rows=5  form='insert_question_form'>{{old('content')}}</textarea>
                                
                                @if ($errors->has('content'))
                                    <span class="help-block" style="color:red">
                                        <strong>{{ $errors->first('content') }}</strong>
                                    </span>
                                @endif
                            </div>

                            
                        </div>

                        <div id="class_div" class="form-group{{ $errors->has('option') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Classes</label>
                            
                            <div class="col-md-6">
                                
                                <input type="text" placeholder="Seperated By Comma" class="form-control" id="txt_class" name="question_classes">
                                
                            </div>
                            
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">New Option</label>
                            
                            <div class="col-md-6">
                                <div class="row">
                                
                                    <div class="col-xs-8" >
                                    <input dir="rtl" type="text" placeholder="Option" class="persian_question form-control" id="new_option">
                                    </div>
                                        
                                    <div class="form-group col-xs-4" style="margin-left: 1pt" >
                                    
                                    <input type="text" placeholder="Score" class="form-control" id="score">
                                    
                                    
                                    </div>

                                </div>
                                

                                <input type="button" 
                                onclick="addOption()" 
                                class="addOption" value="add option">
                                
                            </div>
                            
                        </div>
                        

                        <div class="form-group{{ ($errors->has('option_1') OR $errors->has('option_2')) ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">Options</label>
                            
                            <div class="col-md-6">
                            
                            <!--
                            <ul id="options" style="list-style-type:disc">
                            -->
                            <div id="options">
                                    
                            </div>

                            </div>
                            
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-4 control-label">Default Option</label>

                            <div class="col-md-6">
                                <div class="checkbox">
                                  
                                  <label><input name="default_options" type="checkbox" value="default_option_has_been_set">Set options as default for this questionnaire</label>
                                  
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 col-md-offset-4">
                            @if ( $errors->has('option_1') OR $errors->has('option_2'))
                                <span class="help-block" style="color:red">
                                    @if ($errors->has('option_1'))
                                    <strong>At least two options is required</strong>
                                    <br>
                                    <strong>Please re-enter the options</strong>
                                    @endif
                                    <br>
                                    
                                    
                                </span>
                            @endif
                            </div>
                            </div>

                        




                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-plus"></i>Insert Question
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    $('document').ready(function() {
        $('#txt_content').val($('#txt_content').val().trim())
        //$('#class_div').hide()
    })
    
    var optionNumber=0;

    function getEl(id){
        return document.getElementById(id)
    }//end get el

    function createLiTag(option) {
        return "<li dir='rtl' id='li_"+optionNumber+"' class='list-group-item persian_option' style='direction:rtl;list-style-position: inside;word-break: break-all;overflow:auto'>"+option+"</li>"
    }//end create ul

    function createHiddenInput(name,value){

        return "<input id='hidden_input_"+name+"' type='hidden' name='"+name+"' value='"+value+"' form='insert_question_form' >"

    }//end create hiddenInput

    //=========================================================================
    //this function coppied from
    //src: http://stackoverflow.com/questions/17024985/javascript-cant-convert-hindi-arabic-numbers-to-real-numeric-variables
    function parseArabic(str) {
    return Number( str.replace(/[٠١٢٣٤٥٦٧٨٩]/g, function(d) {
        return d.charCodeAt(0) - 1632;
    }).replace(/[۰۱۲۳۴۵۶۷۸۹]/g, function(d) {
        return d.charCodeAt(0) - 1776;
    }) );
    }
    //=========================================================================

    var editFlag=[]
    function editOption(option_num){
        
        var content=$("#hidden_input_option_"+option_num).val()
        var score=$("#hidden_input_option_"+option_num+"_score").val()

        if (editFlag[option_num]!=1) {
            $("#li_"+option_num).html(
                                "<div class='row'><div class='col-md-8 col-sm-9 '><input class='form-control' dir='rtl'  id='input_edit_option_"+option_num+"' type='text'></div><div class='col-md-4 col-sm-3'> <input class='form-control' id='input_edit_option_"+option_num+"_score' \
                                type='text'></div></div>")

            $("#input_edit_option_"+option_num).val(content)
            $("#input_edit_option_"+option_num+"_score").val(score)
            editFlag[option_num]=1
        }else{
            var newContent=$("#input_edit_option_"+option_num).val()
            var newScore=$("#input_edit_option_"+option_num+"_score").val()
            newScore=parseArabic(newScore)
            $("#hidden_input_option_"+option_num).val(newContent)
            $("#hidden_input_option_"+option_num+"_score").val(newScore)
            
            editFlag[option_num]=0
            $("#li_"+option_num).html(newContent+" , (Score : "+newScore+")")
        }
    }//end edit option

    function deleteOption(option_num){
        $("#option_"+option_num).remove()
        $("#hidden_input_option_"+option_num).remove()
        $("#hidden_input_option_"+option_num+"_score").remove()
        
        for (i=option_num;i<optionNumber;i++){
            $("#option_"+(i+1)).attr("id","option_"+i)
            
            $("#hidden_input_option_"+(i+1))
                                .attr("name","option_"+i)
                                .attr("id","hidden_input_option_"+i)

            $("#hidden_input_option_"+(i+1)+"_score")
                                .attr("name","option_"+i+"_score")
                                .attr("id","hidden_input_option_"+i+"_score")

            $("#a_option_"+(i+1))
                                .attr("onclick","deleteOption("+i+")")
                                .attr("id","a_option_"+i)

        }//end for
        
        optionNumber--

    }//end edit option

    function addOption(){
        
        var optionContent=getEl('new_option').value
        var scoreValue=parseArabic(getEl('score').value)
        
        optionNumber++

        $("#options").append("<div id='option_"+optionNumber+"'>"+createLiTag(optionContent+" , (Score : "+scoreValue+")")+" <a onclick='deleteOption("+optionNumber+")' href='#' id='a_option_"+optionNumber+"'><i class='fa fa-trash-o'></i>Delete</a>"+" <a onclick='editOption("+optionNumber+")' href='#' id='a_edit_option_"+optionNumber+"'><i class='fa fa-edit'></i>Edit</a>")

        
        $('#insert_question_form').append(createHiddenInput('option_'+optionNumber,optionContent)+" "+createHiddenInput('option_'+optionNumber+"_score",scoreValue))

        $('#new_option').val("")
        $('#score').val("")


    }//end add option
</script>

@endsection
