<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Option;
use App\Question;
use App\question_class;
class Answer extends Model
{
    public function getScore(){
    	return Option::where('id',$this->selected_option)->first()->score;
    }//end get score

    public function getQuestion(){
    	return Question::where('id',$this->question_id)->first();
    }//end get question

    public function getClasses(){
    	return $this->getQuestion()->getClasses();
    }//end get class
}
