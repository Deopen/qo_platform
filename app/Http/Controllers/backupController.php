<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;

use App\Http\Requests;
use DB;

use App\backup;
use App\User;
use App\Project;
use App\user_project;
use App\Questioner;
use App\Question;
use App\Option;
use App\score_map;
use App\Finalize;
use App\project_questioner;
use App\questioner_category;
use App\Answer;
use App\question_class;

use Log;

class backupController extends Controller
{
	public $errorFlag=false;
   	public $oldUserIdToNew=array();
   	public $oldProjectIdToNew=array();
   	public $oldQuestionerIdToNew=array();

   	public function backup(){
   		
   		Log::info("================== Backup Begin ======================");
   		if (Auth::user()->isSuperAdmin()) {
   			Log::info("Backing Up Users ...");
	   		$this->backupUsers();
	   		Log::info("Backing Up Projects ...");
	   		$this->backupProjects();
	   		Log::info("Backing Up Questioners ...");
	   		$this->backupQuestioners();
	   		Log::info("Backing Up Answers ...");
	   		$this->backupAnswers();
	   		Log::info("Backing Up Questions ...");
	   		$this->backupQuestions();
	   		Log::info("Backing Up Finalizes ...");
	   		$this->backupFinalizes();
	   		Log::info("Backing Up Options ...");
	   		$this->backupOptions();

	   	}//end if is super admin
	   	if (!$this->errorFlag)
	   		Log::info("================== Backup Complete ==================");
	   	else
	   		Log::info("WARNING!! BACKING UP HAS ERROR");
	   	
	   		
   	}//end backup

   	public function deleteBackup(){
   		$backups=backup::all();
   		foreach ($backups as $backup) {
   			$backup->delete();
   		}//end foreach delete

   		Log::info('All backup has been removed.');
   	}//end delete backup

   	public function restore() {

   		Log::info("================== Restore Begin ==================");
   		if (Auth::user()->isSuperAdmin()) {
   			$this->restoreUsers();
   			$this->restoreProjects();
   			//$this->restoreQuestioners();
   		}//end if super admin
   		if (!$this->errorFlag){
   			Log::info("================== Restore Compelete ==============");
   			return "success";
   		}
   		else
 			Log::info("WARNING!! RESTORING HAS ERROR");  			
   		//return back();
   	}//end restore

   	public function addRecordToObject($record,$obj){
   		foreach ($record as $field) {
   				//echo $field->get("name")."  ".$field->get("value")."<br>";
   				$field_name=$field->get("name");
   				$field_value=$field->get("value");
   				$obj->$field_name=$field_value;
   			}//end for each field
   	}//end add record  to function

   	public function oldToNew_user($old){
   		
   		if ($old and $old!=1)
   			return $this->oldUserIdToNew[$old];
   		else
   			return $old;//deopen and null

   	}//end oldToNew_user

   	public function oldToNew_project($old){
   		return $this->oldProjectIdToNew[$old];
   	}//end oldToNew_user

   	public function restoreQuestioners(){
   		$records=$this->restoreRecords("Questioner");

   		foreach ($records as $id=>$record) {
   			$questioner = new Questioner();	
   			$this->addRecordToObject($record,$questioner);
   			
   			$newOwnerId=$this->oldToNew_user($questioner->created_by);
   			$questioner->created_by=$newOwnerId;
   			$questioner->save();

   			$this->oldQuestionerIdToNew[$id]=$questioner->id;
   			
   		}//end records foreach


   	}//end restore questioner

	public function restoreProjects(){
   		$records=$this->restoreRecords("Project");
   		
   		foreach ($records as $id=>$record) {
   			$project = new Project();	
   			$this->addRecordToObject($record,$project);
   			//echo $project."<br>";
   			//echo $id."<br>";
   			$newOwnerId=$this->oldToNew_user($project->owner_id);
   			$project->owner_id=$newOwnerId;
   			$project->save();

   			$this->oldProjectIdToNew[$id]=$project->id;
   			
   		}//end records foreach

   		$records=$this->restoreRecords("user_project");
   		foreach ($records as $record) {
   			$user_project = new user_project();	
   			$this->addRecordToObject($record,$user_project);
   			//echo $project."<br>";
   			$newProjectId=$this->oldToNew_project($user_project->project_id);
   			$user_project->project_id=$newProjectId;
   			$newUserId=$this->oldToNew_user($user_project->subject_id);
   			$user_project->subject_id=$newUserId;
   			//var_dump($projectId);
   			$user_project->save();
   		}//end records foreach
   		/*
		$records=$this->restoreRecords("project_questioner");
   		foreach ($records as $record) {
   			$project_questioner = new project_questioner();	
   			$this->addRecordToObject($record,$project_questioner);
   			//echo $project."<br>";
   			$project_questioner->save();
   		}//end records foreach
		*/

   	}//end restore projects


   	public function restoreUsers(){
   		$records=$this->restoreRecords("User");
   		$users=collect();
   		$i=0;
   		//echo $records->count();

   		/*
   		//Debug:
   		foreach ($records as $record) {
   			echo "<br>".$record;
   		}//end foreach debug

   		die();
   		//end debug
   		*/
   		foreach ($records as $id=>$record) {
   			$user = new User();
   			//echo $id."<br>";
   			//echo $record."<br>";
   			
   			$i++;
   			
   			$this->addRecordToObject($record,$user);

   			if ($user->isSuperAdmin())
   				continue;

   			//check uniq fields here:
 			if (User::where("username",$user->username)->count()==0){
   				//$user->save();
   				$users->put($id,$user);
   				
   				Log::info(
   					"User ".$user->username." with id ".
   					$user->id." added from backup."
   					);
 			}
   			else{
   				$this->errorFlag=true;
   				$errorMsg="Restore-Error (record ".$i.")"
   					."==> username \""
   					.$user->username."\" Exists";
   				Log::info($errorMsg);
   				return back()->withErrors(
   					[$errorMsg]);
   			}//end if count ==0
   			//echo $user."<br>";	

   		}//end records foreach
   		//echo "<br>useres count:".$users->count()."<br>";
   		foreach ($users as $id=>$user) {
   			$user->created_by=$this->oldToNew_user($user->created_by);
   			$user->save();
   			$this->oldUserIdToNew[$id]=$user->id;
   			//echo "<br>oldUserIdToNew[$id]: ".$this->oldUserIdToNew[$id]."<br>";
   		}//end for each user

   		var_dump($this->oldUserIdToNew);
   	}//end restore users

   	public function restoreRecords($model_name){

   		$backups=backup::where("model_name",$model_name)->get();
   		$records=collect();

   		foreach ($backups as $backup) {
   			
   			$key=$backup->record_id;

   			$field=collect([
   					"name"=>$backup->field_name,
   					"value"=>$backup->field_val]) ;
   			//echo $field."<br>";
   			
   			//echo $key."<br>";
   			if ($records->has($key)){
   				$records->get($key)->push($field);
   				//echo $records->get($key)."<br>";
   			}else{
   				$records->put($key,collect()->push($field));
   			}//end if else
   			
   			//echo $backup."<br>";
   		}//end foreach backup

   		return $records;
   	}//end restore table

   	public function backupTable($records,$model_name){

   		if ($records->isEmpty()){
   			return null;
   		}//end if empty

   		$field_names=DB::getSchemaBuilder()->getColumnListing($records->first()->getTable());
   		//echo $record->getTable();
   		//echo $model_name."<br>";
   		/*echo $model_name."<br>";
		var_dump($field_names);echo "<br><br>";
		*/
   		foreach ($records as $record ) {
   			
			foreach ($field_names as $field_name) {
				//echo $field_name." --> ".$record->$field_name."<br>";
				if ($field_name=="id")
					continue;
				backup::where("model_name",$model_name)->
						where("field_name",$field_name)->
						where("field_val",$record->field_val)->delete();
				$backup=new backup();
				
   				$backup->model_name=$model_name;
				$backup->field_name=$field_name;
				$field_val=$record->$field_name;
				$backup->field_val=$field_val;
				$backup->record_id=$record->id;
				if ($backup->field_val){
					$backup->save();
					//echo $model_name."=>".$field_name." v:".$field_val." saved <br>";
				}//end if fieald val exist
				//else
					//echo $model_name."=>".$field_name."<br>";
				
			}//end foreach field names
   		}//end foreach user

   	}//end backup users

   	public function backupUsers(){
   		$users=User::all();
   		$this->backupTable($users,"User");
   	}//end backup users

   	public function backupProjects(){
   		$projects=Project::all();
   		$this->backupTable($projects,"Project");

   		$user_projects=user_project::all();
		$this->backupTable($user_projects,"user_project");   		

		$project_questioners=project_questioner::all();
		$this->backupTable($project_questioners,"project_questioner");

   	}//end backup projects (include user projects and etc..)
   	
   	public function backupQuestioners(){

   		$questioners=Questioner::all();
   		$this->backupTable($questioners,"Questioner");

   		$questioner_categories=questioner_category::all();
		$this->backupTable($questioner_categories,"questioner_category");

		$score_maps=score_map::all();
		$this->backupTable($score_maps,"score_map");

   	}//end backup questioner include score map 
   	
   	public function backupAnswers(){

   		$answers=Answer::all();
   		$this->backupTable($answers,"Answer");

   	}//end backup answers

   	public function backupQuestions(){
   		$questions=Question::all();
   		$this->backupTable($questions,"Question");

   		$question_classes=question_class::all();
   		$this->backupTable($question_classes,"question_class");

   	}//end backup questions

   	public function backupFinalizes(){
   		$finalizes=Finalize::all();
   		$this->backupTable($finalizes,"Finalize");
   	}//end backup finalizes

   	public function backupOptions(){
   		$options=Option::all();
   		$this->backupTable($options,"Option");
   	}//end backup options


}
