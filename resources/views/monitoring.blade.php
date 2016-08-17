@extends('layouts.panel')

@section('inner_panel')

<div class="row">
<div class="col-md-12">
<div class="panel panel-default" style="background-color:rgba(0,0,0,0.8);">
   
   <div align="center" style="background-color:rgba(0,180,0,0.6);">
   <i class='fa fa-eye' ></i>
   </div>
@if ($errors->any())
<div style="background-color:rgba(0,0,200,0.8)">                       
<ol type="1" style="text-align:center;list-style-position: inside;">

<span id="errors" class="help-block">
    <strong><font color="rgba(40,0,0,0.7)">
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
   
    <div id='log_div' class='col-md-offset-1 col-xs-offset-1 col-sm-offset-1' style="height:500px;overflow:Auto;">
    <span class="help-block" style="color:rgba(0,255,0,0.7)">
        <strong id="monitor_lines">
        {!!str_replace('local.INFO:','',$logs)!!}
        
        </strong>
    </span>
    </div>
    
    
</div>
</div>
</div>

<script>
    
var lastRow=""
var lastLineFromServer=""
function getLastLineFromServer(){
    $.ajax({
        url:"/log/last/10",
        type:"GET",
        dataType:'text'
    }).success(function(result){
        result=result.replace(/local.INFO:/gi,'')
        
        var lastLines=result.split(/<br\s*\/?>/i)
        var writeFlag=0
        
        for (i=0;i<lastLines.length;i++){
            
            currLine=lastLines[i].trim()
            
            if (currLine.length==0)
                continue
            if (writeFlag==1){
                $('#monitor_lines').append(currLine+"<br>")
                lastRow=getLastRow()
                goToTheBottom()
            }
            
            if (currLine==getLastRow().trim()){
                writeFlag=1
            }
                    
        }//end for
        
        
        lastLineFromServer=""
        i=1
        while(lastLineFromServer.length<=1 && (lastLines.length-i>=0)){
            lastLineFromServer=lastLines[lastLines.length-i]
            i++
        }
        
        if (lastLineFromServer.trim()!=getLastRow().trim()){
            $.ajax({
                url:"/log/last/500",
                type:"GET",
                dataType:'text'
            }).success(function(result){
                result=result.replace(/local.INFO:/gi,'')
                $('#monitor_lines').html(result)
            })
            goToTheBottom()
        }
        
    }).fail(function(xhr,status,strErr){
        $('#monitor_lines').append(status+": "+strErr+"<br>")
        //$('#monitor_lines').append("getting information from server...<br>")
        //goToTheBottom()
    })
}//end get last from server

function getLastRow(){
    
    var lines=$('#monitor_lines').html().split(/<br\s*\/?>/i)
    var i=1
    while(lines[lines.length-i].trim().length==0)
        i++
    return lines[lines.length-i].trim()
}//end get last row

function goToTheBottom(){
    var objDiv = document.getElementById("log_div");
    objDiv.scrollTop = objDiv.scrollHeight;
}//end goToTheBottom
    
$(function(){
    goToTheBottom()
    lastRow=getLastRow()
    
})

setInterval(getLastLineFromServer,3000)
</script>


@endsection