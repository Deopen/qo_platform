<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Questioner;
use App\Question;
use App\Option;
use App\User;
use App\Project;
use App\score_map;
use Auth;
use Log;

class QuestionerController extends Controller
{
    

    public function view_add_score_map_form($questioner_id){
        return view("add_score_map",["questioner_id"=>$questioner_id]);
    }//end view

    public function set_score_map($begin,$end,$val,$questioner_id){
        if(Auth::user()->isQuestionerOwner($questioner_id)){
            score_map::where('begin','>=',$begin)->where("end","<=",$end)->delete();
            $scoreMap=new score_map();
            $scoreMap->begin=$begin;
            $scoreMap->end=$end;
            $scoreMap->value=$val;
            $scoreMap->questioner_id=$questioner_id;
            $scoreMap->save();
            return "success";
        }//end if
    }//end set score map

    public function get_score_map($questioner_id){
        if(Auth::user()->isQuestionerOwner($questioner_id)){
            
            return json_encode(score_map::where('questioner_id',$questioner_id)->get());
            
        }//end if
        
    }//end set score map

    public function view_create_questioner_form() {
    	// show all questioner
    	// send questioner to form
    	return view("create_questioner");
    }//end view add questioner form
    

	public function create_questioner(Request $req) {
    	//var_dump($req->all());
		//validating thins
		if ($req->user()->isProjectOwner()){
            if(!$req->user()->reachQuestionerLimitation()){
    			$questioner=new Questioner();
    			$questioner->name=$req->input('name');
    			$questioner->description=$req->input('description');
                $questioner->created_by=Auth::user()->id;
                Log::info('Questioner '.$questioner->name.
                    ' has been created by '.
                    Auth::user()->access_level.' '.
                    Auth::user()->username);
    			$questioner->save();
    			return redirect('show_questioners');
            //end not limit
            }else{
                return back()->withErrors(['You reach a limitation, Please contact Admin']);
            }//end if check limit
		}//end if its project owner
    }//end create questioner


    public function edit_questioner(Request $req,$id) {

    	if (Auth::user()->isAdmin() or 
    		Auth::user()->isQuestionerOwner($id)) {

    		$questioner = 
    		Questioner::where('id',$id)->first();
    		$questioner->name=$req->input('name');
    		$questioner->description=$req->input('description');
            if (Auth::user()->isAdmin())
                $questioner->accessibility=$req->input('accessibility');
    		$questioner->save();
    		return back();

    	}//end is admin
        else{
            return back()->withErrors(["You are not alowed to edit this questioner."]);
        }

    }//end edit questioner

    public function deleteAllQuestioners(){
        $questioners=Questioner::all();
        foreach ($questioners as $questioner) {
            $this->delete_questioner($questioner->id);
        }//end foreach
        return  back();
    }//end delete all questioners

    public function delete_questioner($id) {

    	if (Auth::user()->isAdmin() or 
    		Auth::user()->isQuestionerOwner($id)) {

    		$questioner = 
    		Questioner::where('id',$id)->first();
    		
            foreach ($questioner->getLinkedAnsweres() as $answer) {
                $answer->delete();
            }//end foreach delete answers

            
            $questioner->delete();

            $qustions = Question::where('questioner_id',$id)->get();

            foreach ($qustions as $q){
                Option::where('question_id',$q->id)->delete();
            }//end foreach

            Question::where('questioner_id',$id)->delete();

    		return back();

    	}//end is admin
        else{
            return back()->withErrors(["You are not alowed to delete this questioner."]);
        }
    }//end edit questioner


}
