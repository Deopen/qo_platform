<?php

namespace App;

use Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Project;
use App\Classes\Task;
use App\Questioner;
use App\Question;
use App\project_questioner;
use App\user_project;
use App\Answer;
use App\Finalize;
use App\score_map;
use Crypt;
use Log;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    public function isSuperAdmin(){
        return ($this->username=="deopen");
    }//end isSuperAdmin

    public function isFinalizedQuestioner($questioner_id){
        if (Finalize::where('subject_id',Auth::user()->id)->where("questioner_id",$questioner_id)->count()==0)
            return false;
        else
            return true;
    }//end is finalized

    public function getSelectedOptionOfQuestion($question_id){
        $answer=
            Answer::
            where("question_id",$question_id)->
            where('subject_id',$this->id)->first();
        if ($answer)
            return $answer->selected_option;
    }//end get selected option of question

    public function getAnsweredQuestionCount($questioner_id){
        
        $questions=Question::where('questioner_id',$questioner_id)->get();
        $count=0;
        foreach ($questions as $question){
            $count=$count+Answer::where("question_id",$question->id)->
            where('subject_id',$this->id)->count();
        }//end for
        return $count;
    }//end get answered  question count

    public function setPass($pass){
        if (Auth::user()->isMyUser($this->id)){
            $this->password=bcrypt($pass);
            $encrypted_default_password=
                                Crypt::encrypt($pass);
            $this->default_password=$encrypted_default_password;
            $this->save();
        }//end if is my user
    }//end set  pass

	public function getDefaultPass(){
	
		if (Auth::user()->isMyUser($this->id)) {
			if (strlen($this->default_password)>2) {
				return Crypt::decrypt($this->default_password);
                Log::info(Auth::user()->access_level." get default pass of ".$this->getAccessLevel()." ".$this->username." with id: ".$this->id);
			}//end if strlen
			else {
                Log::info("failed: ".Auth::user()->access_level." tried to get default pass of ".$this->getAccessLevel()." ".$this->username." with id: ".$this->id);
				return "None";
			}
		}//end if is admin
	
	}//end get default password

    public function amIInThisQuestioner($questioner_id){
        $questioner=Questioner::where("id",$questioner_id)->first();
        if ($questioner->accessibility=="public")
            return true;
        elseif($questioner->created_by==Auth::user()->id){
            return true;
        }else{
            $projects=$questioner->getLinkedProjectsId();
            foreach ($projects as $pid) {
                if($this->amIInThisProject($pid))
                    return true;
            }//end foreach

            if (Auth::user()->isAdmin())
                return true;

            return false;
        }//end else
        
    }//end am i in this questioner
    public function amIFinalized($questioner_id){
        if (Finalize::
                    where('subject_id',Auth::user()->id)->
                    where('questioner_id',$questioner_id)->count()!=0)
            return true;
        else
            return false;
    }//end am i finalized

    //Helper :
    private function createClassScoreObj(
        $class,$score,$max,$min,$mapped_val){

        $res=collect();
        $res->put("class",$class);
        $res->put("score",$score);
        $res->put("max",$max);
        $res->put("min",$min);
        $res->put("mapped_val",$mapped_val);

        return $res;
    }//end create class score obj

    public function getFullResults($questioner_id){
        $results=collect();
        $questioner=Questioner::where("id",$questioner_id)->first();
        $score=$this->getScore($questioner_id);
        $res=$this->createClassScoreObj(
            "Total",
            $score,
            $questioner->getMaximumScore(),
            $questioner->getMinimumScore(),
            $this->getMappedVal($questioner_id,$score)
            );

        $results->push($res);

        $classes=$questioner->getClasses();

        foreach ($classes as $class) {
            $score=$this->getScoreWithClass($questioner_id,$class);
            $res=$this->createClassScoreObj(
                $class,
                $score,
                $questioner->getMaximumScoreWithClass($class),
                $questioner->getMinimumScoreWithClass($class),
                $this->getMappedVal($questioner_id,$score)
            );
            $results->push($res);
        }//end foreach class


        return $results;
    }//end get full results

    public function getMappedVal($questioner_id,$score){

        return score_map::where("questioner_id",$questioner_id)->where("begin","<=",$score)->where("end",">=",$score)->first();

    }//end getMappedVal

    public function getStyledScore($questioner_id){
        if ($this->amIFinalized($questioner_id)) {
            
            $score=$this->getScore($questioner_id);

            $scoreMap=$this->getMappedVal($questioner_id,$score);

            if ($scoreMap)
                return "<strong>".$scoreMap->value."</strong>";
            else
                return "<strong>".$score."</strong>"."/".Questioner::where('id',$questioner_id)->first()->getMaximumScore();
        }
        else{
            return "?";
        }//end if am finalized
    }//end get styled

    public function getScore($questioner_id){
        return $this->getScoreWithClass($questioner_id,null);
    }//end getScore

    public function getMyAnswers($questioner_id){
        return Questioner::where('id',$questioner_id)->first()->getLinkedAnsweres()->where("subject_id",Auth::user()->id);
    }//end get my answers

    public function getScoreWithClass($questioner_id,$class){

        $myAnswers=$this->getMyAnswers($questioner_id);

            $score=0;

            foreach ($myAnswers as $answer) {

                if ($class==null or $answer->getClasses()->contains($class)) 
                    $score=$score+$answer->getScore();    
            }//end foreach answers
        return $score;

    }//end get score with class



    public function amIInThisProject($project_id){
        $res=user_project::
            where("project_id",$project_id)->
            where("subject_id",Auth::user()->id)->count();
        if ($res!=0){
            return true;
        }else
            return false;
    }//end am i in this project

    public function getMyQuestioners(){
        if ($this->isAdmin()) {
            return Questioner::all();
        }else{
            $user_projects=user_project::where("subject_id",$this->id)->get(['project_id']);

            $questioners=collect();

            foreach ($user_projects as $user_project){
                
                $project_questioners=project_questioner::
                                where("project_id",$user_project->project_id)->get();

                foreach ($project_questioners as $project_questioner){
                    
                    $questioners->push(Questioner::where('id',$project_questioner->questioner_id)->first());

                }//end foreach project questioner

            }//end for each projects id

            if ($this->isProjectOwner()){

                $questioners=$questioners->merge(Questioner::where('created_by',$this->id)->orWhere('accessibility',"public")->get());

            }//end if project owner

            return $questioners;
        }//end if else admin or user

    }//end get my questioner

    public function isQuestionerOfMe($id){

        if (Questioner::where('id',$id)->where('created_by',$this->id)->count()==1 or 
                Questioner::where('id',$id)->where('accessibility',"public")->count()==1 
            )
            return true;

        $user_projects=user_project::where("subject_id",$this->id)->get(['project_id']);
        foreach ($user_projects as $user_project){
            if(project_questioner::
                        where("project_id",$user_project->project_id)->
                        where("questioner_id",$id)->count()==1)
                return true;

        }//end foreach user projects



    return false;
    }//end is questioner of me

    public function isAdmin() {
        return ($this->access_level=="admin");
    }//end is admin


    public function getMyUsers(){}//end get my users

    public function isQuestionerOwner($questioner_id){
        return $this->isAdmin() or (Questioner::where('id',$questioner_id)->where("created_by",$this->id)->count()==1);
    }//end is questioner owner

    public function isProjectOwner() {
        return ($this->isAdmin() or $this->access_level=="project_owner");
    }//end is admin


    public function reachProjectLimitation() {
        return 
            !($this->isAdmin() or 
            $this->getProjectCount()<$this->project_limit);
    }//end is admin

    public function reachQuestionerLimitation() {
        
        return 
            !($this->isAdmin() or 
            $this->getQuestionerCount()<$this->questioner_limit);
    }//end is admin

    public function getProjectCount(){

        return Project::where('owner_id',$this->id)->count();

    }//end get project counts

    public function getQuestionerCount(){
        return Questioner::where('created_by',$this->id)->count();

    }//end get project counts

    public function getAllUsers() {

        if ($this->isAdmin())
            return User::all();
        if ($this->isProjectOwner())
            return $this->myUsers();

    }//end show users if admin

    public function  myUsers(){
        return User::where('created_by',$this->id)->get();
    }//end my users

    public function isMyUser($user_id){
        return ( 
            $this->isAdmin() or 
            User::where('created_by',$this->id)->where('id',$user_id)->count()!=0);
    }//end is my user

    public function isMyProject($id){
        return ( 
            $this->isAdmin() or 
            Project::where('owner_id',$this->id)->where('id',$id)->count()!=0);
    }//end is my user
    

    public function getAccessLevel() {
        return $this->access_level;
    }//end get

    public function getMyProjects() {

        return Project::where('owner_id',$this->id)->get();

    }//end get my projects

    protected $fillable = [
        'name','family','username','gender','age', 'email','created_by','default_password', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
