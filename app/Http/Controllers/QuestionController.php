<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\User;
use App\Question;
use App\Questioner;
use App\Option;
use App\Answer;
use App\question_class;
use Auth;
use Validator;

class QuestionController extends Controller
{
    public function view_create_question_form(Request $req,$questioner_id){

    	return view('insert_question',['questioner_id'=>$questioner_id]);
    }//end view create question newt_form()


    private function validator(array $data)
    {
        
        return Validator::make($data, [
            'content' => 'required',
            
        ]);

    }//end validator

    public function insertQuestion(Request $req){



        $questioner_id=trim($req->input('questioner_id'));
    	if (Auth::user()->isProjectOwner() and Auth::user()->isQuestionerOwner($questioner_id) ) {
            //=============================================

            $validator = $this->validator($req->all());
        
            if ($validator->fails()) {
                $this->throwValidationException(
                    $req, $validator
                );
            }

            //=============================================
    		
    		$question=new Question();
    		$question->questioner_id=$questioner_id;
    		$question->content=trim($req->input('content'));
            $question->save();

            $classes=collect(explode(',',trim($req->input('question_classes'))));


            
            foreach ($classes as $class) {
                if (strlen($class)==0)
                    continue;
                $question_classes=new question_class();
                $question_classes->question_id=$question->id;
                $class=strtoupper(substr($class,0,1)).strtolower(substr($class,1,strlen($class)));
                //die($class);
                $question_classes->class=$class;
                $question_classes->save();
            }//end foreach
            

            //remove last default options

            if($req->input('default_options')=="default_option_has_been_set"){
                    
                    $default_options=Option::where('default_questioner_id',$questioner_id)->get();

                    foreach ($default_options as $option) {
                        $option->default_questioner_id=null;
                        $option->save();

                        $answered=Answer::where('selected_option',$option->id)->get();

                        foreach ($answered as $answer) {
                            if($answer->getQuestion()->isOptionless()){
                                $answer->delete();
                            }//end if if is orphaned
                        }

                    }//end foreach default options

            }//end if default option set


            $loopCounter=1;
            
            while(null!==$req->input('option_'.$loopCounter)) {
                $option=new Option();
                $option->number=$loopCounter;
                $option->value=trim($req->input('option_'.$loopCounter));
                $option->score=trim($req->input('option_'.$loopCounter."_score"));
                $option->question_id=$question->id;


                if($req->input('default_options')=="default_option_has_been_set"){

                    $option->default_questioner_id=$questioner_id;

                }//end if default option set

                $option->save();
                $loopCounter++;
            }//end while


            /*change it for comments:
            $options=array();
            $loopCounter=1;
            while(null!==$req->input('option_'.$loopCounter)) {
                array_push($options,trim( $req->input('option_'.$loopCounter)));
                $loopCounter++;
            }//end while

            */
            //edit this for comments
            //$question->comments=json_encode($comments);



    		
    		$questioner=Questioner::where('id',$questioner_id)->first();
    		$questioner->save();

            return redirect("/questions_page/".$questioner_id);
    	}//end if is proj owner and owner of questioner

    }//end insert question

    public function deleteQuestion(Request $req,$questioner_id,$question_id){

        if (Auth::user()->isProjectOwner() and Auth::user()->isQuestionerOwner($questioner_id) ) {
            $question=Question::where('id',$question_id)->first();
            $question->delete();
            Option::where('question_id',$question_id)->delete();
            $questioner=Questioner::where('id',$questioner_id)->first();
            
            $questioner->save();
            return back();
        }//end if is admin

    }//end delete question


}
