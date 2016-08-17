@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Score Mapping</div>
                <div class="panel-body">
                     <style type="text/css">
                        
                        input.addOption{
                            margin-top: 2%;
                        }

                    </style>

                    <script>

                    function getScores(questioner_id){
                        
                        $.getJSON({
                            url:"/get_score_map/"+questioner_id,
                            type:"GET",
                            dataType:"text",
                        }).success(function(data){
                                var arr=$.parseJSON(data)
                                for (i=0;i<arr.length;i++){
                                    $("#map_value").append(arr[i].begin+" to "+arr[i].end+" ===> "+arr[i].value+"<br>")
                                }
                        })
                    }//end function get scores
                    getScores({{$questioner_id}})
                    </script>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Score Range to Value</label>
                            
                            <div class="col-md-6">
                                <div class="row">
                                
                                    <div class="form-group col-md-3 col-xs-3" >
                                    <input dir="rtl" type="text" placeholder="Begin" class="form-control" id="new_begin">
                                    </div>
                                    
                                    <div class="form-group col-md-3 col-xs-3" >
                                    <input dir="rtl" type="text" placeholder="End" class="form-control" id="new_end">
                                    </div>
                                    
                                    <div class="form-group col-xs-5" style="margin-left: 1pt" >
                                    
                                    <input type="text" placeholder="Val" class="form-control" id="new_value">
                                    
                                    
                                    </div>

                                </div>
                                

                                
                            </div>
                            
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Map Value</label>
                            
                            <div class="col-md-6">
                            
                            <div id="map_value">
                                    
                            </div>

                            </div>
                            
                    </div>

                        

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button onclick="send_new_range({{$questioner_id}})" type="button" class="btn btn-primary">
                                    <i class="fa fa-btn fa-plus"></i>Insert Map
                                </button>
                                
                            </div>
                        </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    
    function send_new_range(questioner_id){
        var begin=parseArabic($('#new_begin').val())
        var end=parseArabic($('#new_end').val())
        var value=$('#new_value').val()
        
        $.ajax({
            url:"/set_score_map/"+begin+"/"+end+"/"+value+"/"+questioner_id,
            type:"GET",
            dataType:"text"
        }).success(function(result){
            if (result=="success"){
                location.reload()
                //$("#map_value").append(begin+" to "+end+" ===> "+value+"<br>")
            }//end if res success
        })
    }//end send new range
    
    function getEl(id){
        return document.getElementById(id)
    }//end get el

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

    
</script>

@endsection
