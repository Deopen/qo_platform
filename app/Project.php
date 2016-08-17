<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\User;
use Auth;
use App\Project;
use App\project_questioner;
use App\user_project;
use Log;

class Project extends Model
{
    public function getOwnerUsername() {

    	return User::where('id',$this->owner_id)->first()->username;
    }//end getProjectOwnerName

    public function add_user($user_id){

    	if (Auth::user()->isMyProject($this->id) and 
            Auth::user()->isMyUser($user_id) ) {
            if(user_project::
                where('project_id',$this->id)->
                where('subject_id',$user_id)->count()==0){
                $user_project=new user_project();
                $user_project->project_id=$this->id;
                $user_project->subject_id=$user_id;
                $user_project->save();

                Log::info
                (Auth::user()->username.
                    ' add user with id '.$user_id.
                    ' to project with id '.$this->id);
                
                
                $this->save();

                Log::info
                ("The number of users in project "
                    .$this->name." is ".
                    $this->user_count);
                

            }//end if
        }
        //log adding questioner
    }//end add_user


    public function remove_user($user_id){

        if (Auth::user()->isMyProject($this->id) and 
            Auth::user()->isMyUser($user_id) ) {
            if(user_project::
                where('project_id',$this->id)->
                where('subject_id',$user_id)->count()==1){
                
                $user_project=user_project::
                    where('project_id',$this->id)->
                    where('subject_id',$user_id)->first();

                $user_project->delete();

                Log::info
                (Auth::user()->username.
                    ' remove user with id '.$user_id.
                    ' from project with id '.$this->id);

                
                $this->save();

                Log::info
                ("The number of users in project "
                    .$this->name." is ".
                    $this->user_count);

            }//end if
        }
        //log remove user
    }//end remove user

    public function getQuestionerCount(){
        return project_questioner::where('project_id',$this->id)->count();
    }//end get questioner


    public function getUserCount(){
        return user_project::where('project_id',$this->id)->count();
    }//end get questioner

}
