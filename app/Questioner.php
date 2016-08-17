<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\project_questioner;
use App\Question;
use App\Option;
use App\Answer;
use App\question_class;

class Questioner extends Model
{

    public function isMultiClass(){
        $myQuestions=$this->getLinkedQuestions();

        foreach ($myQuestions as $question) {
            $classCountForQuestion=
                question_class::where("question_id",$question->id)->
                count();
            if ($classCountForQuestion!=0)
                return true; 
        }//end foreach questions

        return false;
    }//end is multi class

    public function getOwnerUsername(){
    	return User::where('id',$this->created_by)->first()->username;
    }//end get owner username

    public function getQuestionCount(){
        return $this->getLinkedQuestionId()->count();
    }//end function get question count

    public function getLinkedProjectsId(){
    	$res=project_questioner::where("questioner_id",$this->id)->get();
    	$idLists=collect();
    	foreach ($res as $project_questioner) {
    		$idLists->push($project_questioner->project_id);
    	}//end foreach
    	return $idLists;
    }//end get linked projects

    public function getLinkedQuestions(){
        return Question::where("questioner_id",$this->id)->get();
    }//end get linked questions

    public function getClasses(){
        $questions=$this->getLinkedQuestions();
        $classes=collect();
        foreach ($questions as $question) {
            $classes=$classes->merge($question->getClasses());
        }//end foreach questions

        return $classes->unique();
    }//end get classes

	public function getLinkedQuestionId(){

    	$res=$this->getLinkedQuestions();
    	
    	$idLists=collect();
    	foreach ($res as $question) {
    		$idLists->push($question->id);
    	}//end foreach
    	return $idLists;

    }//end get linked questions

    public function getLinkedOptions(){
    	$questions=$this->getLinkedQuestionId();
    	$options=collect();

    	foreach ($questions as $question_id) {
    		$options=$options->merge(Option::where("question_id",$question_id)->get());
    	}//end foreach

    	return $options;

    }//end get linked options

    public function getLinkedAnsweres(){
    	$questions=$this->getLinkedQuestionId();
    	$answers=collect();

    	foreach ($questions as $question_id) {
    		$answers=$answers->merge(Answer::where('question_id',$question_id)->get());
    	}//end foreach

    	return $answers;

    }//end get linked answers


    public function getLinkedAnsweresId(){
    	$answers=$this->getLinkedAnsweres();
    	$idLists=collect();
    	foreach ($answers as $answer) {
    		$idLists->push($answer->id);
    	}
    	return $idLists;
    }//end get linked answers id

    public function getMaximumScore(){
        return $this->getMaximumScoreWithClass(null);
    }//end get max score

    public function getMaximumScoreWithClass($class){
    	$questions=$this->getLinkedQuestions();
    	$questionToMaxScore=collect();
    	$totalMaxScore=0;
    	foreach ($questions as $question) {
            if ($class==null or $question->getClasses()->contains($class)){
        		$options=$question->getOptions();    
        		$questionToMaxScore->put($question->id,$options->max('score'));	
        		$totalMaxScore+=$questionToMaxScore->get($question->id);
            }//end if class
    	}//end foreach

    	return $totalMaxScore;
    }//end get maximum score with class
    
    public function getMinimumScore(){
        return $this->getMinimumScoreWithClass(null);
    }//end get minimum score

    public function getMinimumScoreWithClass($class){
        $questions=$this->getLinkedQuestions();
        $questionToMinScore=collect();
        $totalMinScore=0;
        foreach ($questions as $question) {
            if ($class==null or $question->getClasses()->contains($class)){
                $options=$question->getOptions();    
                $questionToMinScore->put($question->id,$options->min('score'));  
                $totalMinScore+=$questionToMinScore->get($question->id);
            }//end if class
        }//end foreach

        return $totalMinScore;
    }//end get Minimum score with class
    
}
