<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Validator;
use Crypt;
use Auth;
use App\User;
use App\Project;
use App\user_project;
use Storage;
use DateTime;
use Log;

class UserController extends Controller
{
    //

    public function getPass($id){
        if (Auth::user()->isMyUser($id)){
                return User::where('id',$id)->first()->getDefaultPass();
            }//end if myUser 
    }//end get pass

    public function getScore($questioner_id){
        return Auth::user()->getScore($questioner_id);
    }//end get score

    public function generatePass($id){
        if (Auth::user()->isMyUser($id)){
                User::where('id',$id)->first()->setPass($this->passGenerator());
                return User::where('id',$id)->first()->getDefaultPass();
            }//end if myUser 
    }//end get pass

	public function isFieldExistWithValue($fieldName,$fieldValue) {

		$res=User::where($fieldName,$fieldValue)->count();
        if ($res){
            //echo $username."->true<br>";
            return true;
        }else{
            //echo $username."->false<br>";
            return false;
        }

	}//end is field exist or empty

	public function isAdminExist() {

		return $this->isFieldExistWithValue('access_level','admin');

	}//end is admin exisrt


    public function isUsernameExistOrEmpty($username) {

        if (!isset($username))
            return true;

        return $this->isFieldExistWithValue('username',$username);
        

    }//end is username exist

    public function deleteAllUsers(){
        if (Auth::user()->isSuperAdmin()){
            $users=User::all();
            foreach ($users as $user) {
                if (!$user->isSuperAdmin())
                    $this->delete_user($user->id);
            }//end deleting users
        }//end if is super admin
        return back();
    }//end remove all useres


    public function insertAdmin() {

    	if (!$this->isAdminExist()){
    		$admin=new User();
    		$admin->username="deopen";
    		$admin->access_level='admin';
    		$admin->password=bcrypt('omid123');
    		$admin->save();
            Log::info('admin '.$admin->username.' has been inserted.');
    		return back();
    	}// end if admin not exist

	}//end insert admin


	public function showEditForm(){
	
		return view('auth/register',[
							'edit'=>1,
							'name'=>Auth::user()->name,
							'family'=>Auth::user()->family,
							'username'=>Auth::user()->username,
							'age'=>Auth::user()->age,
							'gender'=>Auth::user()->gender,
							'email'=>Auth::user()->email
							]);
	
	}//end show edit form

	protected function validator(array $data,$user)
    {
    	
    	
    	foreach ($data as $fieldName=>$fieldValue){

			if ($user->$fieldName==$fieldValue)
				unset($data[$fieldName]);
            else{
                //to avoid runnig strtolower on persian string
                //also you can use mb_strtolwer(str,"utf-8")
                if (mb_detect_encoding($data[$fieldName])=="ASCII")
                    $data[$fieldName]=strtolower($data[$fieldName]);

                //debug porpuse
                //echo mb_detect_encoding($data[$fieldName])."=>".$fieldName."<br>";
                
            }



		}//end cleaning
        
		
		/*
		foreach ($data as $fieldName=>$fieldValue){
			echo $fieldName." = ".$fieldValue."<br>";
		}//end cleaning
		
		die();
		*/
    
        return Validator::make($data, [
            'name' => 'max:255|alpha',
            'family' => 'max:255|alpha',
            'gender' => 'max:7|in:male,female,other',
            'age' => 'digits:2',
            'username' => 'unique:users|max:255|alpha_dash',
            'email' => 'email|max:255|unique:users',
            'password' => 'min:5|confirmed',
            'access_level'=> 'in:subject,project_owner,admin'
        ]);
    }//end validator

    public function edit_user(Request $req,$id){
        //var_dump($req->input("name"));
        if (Auth::user()->isMyUser($id) or 
            Auth::user()->id==$id){

        $validator = $this->validator($req->all(),
                                    User::where('id',$id)->first());
        
        if ($validator->fails()) {
            $this->throwValidationException(
                $req, $validator
            );
        }

        $user=User::find(trim($id));
        	if ($req->input("name"))
            	$user->name=trim($req->input("name"));
        	if ($req->input("family"))
            	$user->family=trim($req->input("family"));
            if ($req->input("username"))
            	$user->username=strtolower(trim($req->input("username")));

            
            $user->gender=strtolower(trim($req->input("gender")));
            
            $user->age=trim($req->input("age"));
            
            $user->email=trim($req->input("email"));

            if ($req->input("password")){
                $user->password=bcrypt($req->input("password"));
                $user->default_password=null;
            }//end if user set password
            
            if ($req->user()->isAdmin()){
            	if ($req->input("access_level"))
	                $user->access_level=trim(strtolower($req->input("access_level")));
    			if ($req->input("project_limit"))
                	$user->project_limit=trim($req->input("project_limit"));
                if ($req->input("questioner_limit"))
                	$user->questioner_limit=trim($req->input("questioner_limit"));
            }//end if is admin
            Log::info('User '.$user->username.' with id '.$id
                .' has been edited by '.Auth::user()->access_level.' '.
                Auth::user()->username);
            
            $user->save();
            if (Auth::user()->isProjectOwner()){
                if(strpos(back(),"_user") or strpos(back(),"project_owners")){
                    return back();
                }elseif(strpos(back(),"edit")){
                    return redirect("/");
                }else{
                    //?
                }//end if str pos
            }
            else
                return redirect("/");
        
        }//end if isAdmin
		return redirect("/");
    }//end edit user


    public function usernameGenerator($name,$family) {
        return substr($name,0,1)."_".$family."_".rand(1,1000);
    }//end username generator

    public function passGenerator(){
        return mt_rand(1000000,9999999);
    }//end pass generator


    public function delete_user($id){
        
        if (Auth::user()->isMyUser($id) ){
            $user=User::find($id);
            $username=$user->username;

            user_project::where("subject_id",$id)->delete();

            $user->delete();

            Log::info('User '.$username.' with id '.$id.
                ' has been deleted by '.Auth::user()->access_level.
                ' '.Auth::user()->username);
            return back();

        }//end if isAdmin
    }//end delete user


    public function showInputFileForm($project_id=null){

        return view("bulk_user_input_file",
                    ["project_id"=>$project_id]);
        

    }//end show input file form

    public function getBulkInputFile(Request $req){
        if (Auth::user()->isProjectOwner()){
            if (Auth::user()->isAdmin()){
                $file_name="subjects.bulk.input.txt";
            }else{
                $file_name="subjects.bulk.input.owner_id_".
                                        Auth::user()->id."_.txt";
            }//end if
            
            $project_id=$req->input("project_id");
            
            if ($project_id!=null and !Auth::user()->isMyProject($project_id)){
                return back();
            }

            $input_file = $req->file('input_file');
            $input_file->move(base_path()."/storage/app/",$file_name);
                    
            $this->bulkRegisterationToProject($project_id);
                
            if ($project_id!=null){
                return redirect("/my_projects");
            }elseif (Auth::user()->isAdmin()){
                return redirect("/show_users");
            }//end if project!=null and Auth::isMy...
            

        }else{
            return back();
        }//end if project owner

    }//end getBulkInputFile

    public function bulkRegisterationToProject($project_id){
        $this->bulkRegisteration($project_id);
        return back(); 
    }//end bulk to project

    public function bulkRegisterationMyFormat() {
        if (Auth::user()->isAdmin()){
            $this->bulkRegisteration(null);
            return back();       
        }

    }//end bulk my format

    public function bulkRegisteration($project_id){

        if (Auth::user()->isProjectOwner()) {
            $file_name="";
            if (Auth::user()->isAdmin()){
                $file_name="subjects.bulk.input.txt";
            }else{
                $file_name="subjects.bulk.input.owner_id_".
                                        Auth::user()->id."_.txt";
            }//end if

            $bulkInput_lineByLine=
                    explode('endinput',
                        Storage::get($file_name)) ;
            Log::info
            ('Bulk registeration has been start by '.
                Auth::user()->access_level.' '.
                Auth::user()->username);

            //var_dump($bulkInput_lineByLine[12]);

            foreach ($bulkInput_lineByLine as $line) {
                if (strlen(trim($line))<=1) {continue;}//skip line
                $newUser = new User();
                
                //echo "<br>";
                $fields=explode(' ',$line);
                foreach ($fields as $field) {
                    if (strlen(trim($field))<=1) {continue;}//skip field
                    $sepratedKeyValue=explode(":",$field);
                    try{
                        $fieldName=trim(strtolower($sepratedKeyValue[0]));
                        $fieldValue=trim($sepratedKeyValue[1]);
                        //echo $fieldName."===>".$fieldValue."<br>";
                        $newUser->$fieldName=$fieldValue;
                    }catch(\Exception $e){
                        $err_msg="<font color='red'> ERROR IN READING BULK:<br>Err==>Field:".
                        $field."<br> line: ".$line." Len:".strlen($field)."</font><br>".
                        $e->getMessage()."<br>";
                        echo $err_msg;
                        Log::error("HTML ERROR MESSAGE: ".$err_msg);
                    }//end try catch
                    
                    //echo $field."<br>";
                }//end foreach field

                
                
                while($this->isUsernameExistOrEmpty($newUser->username)){
                    $newUser->username=$this->usernameGenerator($newUser->name,$newUser->family);
                }//end while if username exist
            
            	$default_password=$this->passGenerator();
            	$encrypted_default_password=
            					Crypt::encrypt($default_password);
                
                $newUser->password=bcrypt($default_password);
                try{
                	$newUser->default_password=$encrypted_default_password;
                	$newUser->access_level="subject";//security
                    $newUser->questioner_limit=0;
                    $newUser->project_limit=0;
                	$newUser->username=strtolower($newUser->username);
                    $newUser->gender=strtolower($newUser->gender);
                    $newUser->created_by=Auth::user()->id;
                    $newUser->save();

                    if ($project_id){
                        Project::where('id',$project_id)->first()->add_user($newUser->id);
                    }//end if project id

                    Log::info
                    ('User '.$newUser->username.' added in Bulk Mode by '.
                        Auth::user()->access_level.' '.
                        Auth::user()->username);

                }catch(\Exception $e){
                    $err_msg="<font color='red'> ERROR >>>STORING<<< BULK:<br>Err==>Field:".
                        $field."<br> line: ".$line." Len:".strlen($field)."</font><br>".
                        $e->getMessage()."<br>";
                    echo $err_msg;
                    Log::error("HTML ERROR MESSAGE: ".$err_msg);
                }//end try catch
                

                //echo $line."<br>";
            }//end foreach bulk
            //rename:
            $now = new DateTime();
            $now = $now->format('Y_m_d_H_i_s');
            
            if ($project_id){
                $newFileName="subjects.imported_toProject_".$project_id."_Date_".$now.".txt";
            }else{
                $newFileName="subjects.imported_DateTime_".$now.".txt";
            }//end if project

            Storage::move($file_name,$newFileName);
            Log::info("Users importing complete.");
            $users_count=User::all()->count();
            Log::info("The number of users: ".$users_count);
            return back();
        }//end if isAdmin
        else {
            return back();
        }
    }//end bulk registeration method


}
