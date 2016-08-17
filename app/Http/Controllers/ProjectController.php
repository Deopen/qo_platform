<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Auth;
use App\Project;
use App\project_questioner;
use App\user_project;
use Log;


class ProjectController extends Controller
{
    public function index() {

    	return view('create_project');

    }//end index

    public function edit_project(Request $req,$id) {

    	$curr=Auth::user();
    	$proj=Project::where('id',$id)->first();

    	if ($curr->isAdmin() or $curr->id==$proj->owner_id){
    		$proj->name=trim($req->input('name'));
    		$proj->save();
    		Log::info
    			($curr->username.' edit project with id '.$id);
    		return back();
    	}//end if

    }//end edit project

    public function deleteAllProjects(){
        $projects=Project::all();
        foreach ($projects as $project) {
            $this->delete_project($project->id);
        }//end foreach
        return back();
    }//end delete all project

    public function delete_project($id) {

    	$curr=Auth::user();
    	$proj=Project::where('id',$id)->first();

    	if ($curr->isAdmin() or $curr->id==$proj->owner_id){
    		
            user_project::where('project_id',$id)->delete();
            Log::info
                ($curr->username.' delete user from project with id '.$id);

            project_questioner::where('project_id',$id)->delete();

            Log::info
                ($curr->username.' delete questioner from project with id '.$id);

            $proj->delete();

    		Log::info
    			($curr->username.' delete project with id '.$id);
    		return back();
    	}//end if

    }//end edit project

    public function create_project(Request $req) {

	    if ($req->user()->isProjectOwner()){
            if(!$req->user()->reachProjectLimitation()){
    	    	$proj=new Project();
    	    	$proj->name=$req->input('name');
    	    	$proj->owner_id=$req->user()->id;
    	    	$proj->save();

    	    	Log::info("Project ".$proj->name." created by ".$req->user()->username);
            }else{
                return back()->withErrors(['You reach a limitation, Please contact Admin']);
            }//end if check limit
    	}//if project owner

    	return redirect('home');	

    }//end create project

    public function add_questioner($questioner_id,$project_id){
        if (Auth::user()->isMyProject($project_id)) {
            if(project_questioner::
                where('project_id',$project_id)->
                where('questioner_id',$questioner_id)->count()==0){
                $project_qustioner=new project_questioner();
                $project_qustioner->project_id=$project_id;
                $project_qustioner->questioner_id=$questioner_id;
                $project_qustioner->save();
                Log::info
                (Auth::user()->username.
                    ' add questioner to project with id '.$project_id);

                $project=Project::where('id',$project_id)->first();
                $project->save();

                Log::info
                ("The number of quetioners in project "
                    .$project->name." is ".
                    $project->getQuestionerCount());


            }//end if
            return back();
        }
        //log adding questioner
    }//end add questioner

    public function add_user($user_id,$project_id){
        Project::where("id",$project_id)->first()->add_user($user_id);
        return back();
    }//end add questioner


    public function remove_user($user_id,$project_id){
        Project::where("id",$project_id)->first()->remove_user($user_id);
        return back();
    }//end remove user


    public function remove_questioner($questioner_id,$project_id){
        if (Auth::user()->isMyProject($project_id)) {
            if(project_questioner::
                where('project_id',$project_id)->
                where('questioner_id',$questioner_id)->count()==1){
                
                $project_qustioner=project_questioner::
                    where('project_id',$project_id)->
                    where('questioner_id',$questioner_id)->first();

                $project_qustioner->delete();

                Log::info
                (Auth::user()->username.
                    ' remove questioner with id '.$questioner_id.
                    ' from project with id '.$project_id);
                

                $project=Project::where('id',$project_id)->first();
                $project->save();

                Log::info
                ("The number of questioners in project "
                    .$project->name." is ".
                    $project->getQuestionerCount());

            }//end if
            return back();
        }
        //log adding questioner
    }//end add questioner

}
