<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Answer;
use App\Question;
use Auth;

class AnswerController extends Controller
{
    //
    public function setAnswer($question_id,$option_id){

    	$questioner_id=Question::where("id",$question_id)->first()->questioner_id;

    	if(Auth::user()->amIInThisQuestioner($questioner_id)){
    		if(!Auth::user()->amIFinalized($questioner_id)){
		    	Answer::where("question_id",$question_id)->delete();
		    	$answer=new Answer();
		    	$answer->selected_option=$option_id;
		    	$answer->question_id=$question_id;
		    	$answer->subject_id=Auth::user()->id;
		    	$answer->save();
		    	return "success";
	    	}else{
	    		return "finalized";
	    	}

	    }//end if am i in this questioner
    }//end setAnswer
}
