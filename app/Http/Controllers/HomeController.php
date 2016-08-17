<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Project;
use App\Questioner;
use App\Question;
use App\Option;
use App\Finalize;
use App\project_questioner;
use App\user_project;
use Log;
use Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {

        //{"id":2,"name":"omid","username":"deopen","email":"","access_level":"subject","created_at":"2016-05-11 12:13:41","updated_at":"2016-05-17 19:02:41"} 
        
        $this->middleware('auth');
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$this->bulkRegisteration();
        return $this->myProjects();
    }//end index

    public function showFullResult($questioner_id){
        if (Auth::user()->amIInThisQuestioner($questioner_id)){
            return view("full_result",["results"=>Auth::user()->getFullResults($questioner_id)]);
        }//end if am i in this questioner
    }//end show full result

    public function finalize($questioner_id){
        if (Finalize::where('subject_id',Auth::user()->id)->where('questioner_id',$questioner_id)->count()==0){
            $finalize=new Finalize();
            $finalize->subject_id=Auth::user()->id;
            $finalize->questioner_id=$questioner_id;
            if (Auth::user()->amIInThisQuestioner($questioner_id)){
                $finalize->save();
                if (strpos(back(),"questions_page"))
                    return redirect("/");
                else
                    return "success";         
            }//end if questioner of me
        }//end if finalize count 0
        else{
            return "duplicate";
        }//end else finalize count 0
    }//end finalize

    public function getLastLinesOfLog($num){
        if (Auth::user()->isAdmin()){
            $logPath=base_path()."/storage/logs/laravel.log";
            $logFile = file($logPath);
            $lines="";
            
            for ($i = 1,$fileToken=1; $i <= $num;$fileToken++){
                
                $currLine=nl2br($logFile[count($logFile)-$fileToken]);

                if ( strpos($currLine,"local.INFO") ){
                    $lines=$currLine.$lines;
                    $i++;
                }//end if
            }//end for
            
            return $lines;
        }//end if admin
    }//end getLastLineOfLog

    public function getAllLogs(){
        if (Auth::user()->isAdmin()){
            return $this->getLastLinesOfLog(500);
        }//end if admin
    }//end getLastLineOfLog

    
    public function getAllLogsView(){
        return view('monitoring',['logs'=>$this->getAllLogs()]);
    }//end get all logs

    public function showUsers() {
        if (Auth::user()->isAdmin() or Auth::user()->isProjectOwner() ) {
            /*
            Log::info(
                    Auth::user()->getAccessLevel()." ".
                    Auth::user()->username." get users info.");
            */
            return view('home',[
                "users"=>Auth::user()->getAllUsers()
                    ]);
        }else{
            return back();
        }//end if is admin
    }//end show users

    public function showProjectOwners() {
        if (Auth::user()->isAdmin()) {
            return view('home',[
                "users"=>User::where("access_level","project_owner")->get(),
                "project_owner_view"=>true
                    ]);
        }else{
            return back();
        }//end if is admin
    }//end show users

    public function myProjects() {
        if (Auth::user()->isProjectOwner()){
            return view('home',[
                "projects"=>Auth::user()->getMyProjects()
                    ]);
        }else{
            return back();
        }//end if is project owner

    }//end show users

    public function showAllProjects() {
        if (Auth::user()->isAdmin()){
            return view('home',[
                "projects"=>Project::all()
                    ]);
        }else{
            return back();
        }//end if is project owner

    }//end show users


    public function showQuestioners(){

        return view('home',[
            "questioners"=>Auth::user()->getMyQuestioners()
                ]);
        
    }//end show questioners

    public function addQuestioners($project_id){


        if (Auth::user()->isProjectOwner()){
            $uncleanedAdded_questioners=project_questioner::
                        where('project_id',$project_id)->get(['questioner_id']);
            $added_questioners=array();
            //debug purpose
            foreach ($uncleanedAdded_questioners as $added_questioner){
                array_push($added_questioners,$added_questioner->questioner_id);
            }//end foreach
            
            if (Auth::user()->isAdmin()){
                $questioners=Questioner::all();
            }//end if admin
            else{
                $questioners=Questioner::where('created_by',Auth::user()->id)->
                                                orWhere('accessibility','public')->get();
            }//end else admin

            return view('home',[
                "questioners"=>$questioners,
                "project_id"=>$project_id,
                "added_questioners"=>$added_questioners
                    ]);
        }//end if project owner

        return back();
    }//end show questioners

    public function addUsers($project_id){

        if (Auth::user()->isProjectOwner()){
            $uncleanedAdded_users=user_project::
                        where('project_id',$project_id)->get(['subject_id']);
            $added_users=array();
            //debug purpose
            foreach ($uncleanedAdded_users as $added_user){
                array_push($added_users,$added_user->subject_id);
            }//end foreach

            if (Auth::user()->isAdmin()){
                $users=User::all();
            }//end if admin
            else{
                $users=User::where('created_by',Auth::user()->id)->get();
            }//end else admin

            return view('home',[
                "users"=>$users,
                "project_id"=>$project_id,
                "added_users"=>$added_users
                    ]);
        }//end if project owner
    }//end show questioners


    public function showQuestions($questioner_id){

        if (Auth::user()->isQuestionerOfMe($questioner_id) or
            Auth::user()->isAdmin()
            )
        {
            $questions=
            Question::where('questioner_id',$questioner_id)->get();
            
            
            return view('questions_page',
            [
            'questions'=>$questions,
            'questioner_id'=>$questioner_id]);
        }//end if
        
        return back();

    }//end show questions



}
