<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Option;
class Question extends Model
{
   public function getOptions(){

   	if ( !$this->isOptionless() ){
   		return Option::where('question_id',$this->id)->get();
   	}else{
   		return Option::where('default_questioner_id',$this->questioner_id)->get();
   	}//end if not have option

   }//end get options

   public function isOptionless(){
   		return Option::where('question_id',$this->id)->count()==0;
   }//end is option less

   public function getClasses(){
      $question_classes=question_class::where('question_id',$this->id)->get();
      $classes=collect();
      foreach ($question_classes as $question_class) {
         $classes->push($question_class->class);
      }//end foreach
      return $classes;
    }//end get class

}
