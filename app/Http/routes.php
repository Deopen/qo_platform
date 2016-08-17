<?php

use App\User;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::get('/about', function () {
    return view('about');  
});

Route::get('/backup',"backupController@backup");
Route::get('/restore',"backupController@restore");
Route::get('/delete_backup',"backupController@deleteBackup");
Route::get('/delete_all_users',"UserController@deleteAllUsers");
Route::get('/delete_all_questioners',"QuestionerController@deleteAllQuestioners");

Route::get("/full_result/{quetioner_id}","HomeController@showFullResult");

Route::get('/delete_all_projects',"ProjectController@deleteAllProjects");



Route::get('/', function () {
    return view('welcome');
})->middleware(['auth']);

Route::get('/edit',"UserController@showEditForm");
Route::get('/finalize/{questioner_id}',"HomeController@finalize");
Route::get('/get_score/{questioner_id}',"UserController@getScore");

Route::get("insert_admin","UserController@insertAdmin");
Route::get("delete_user/{id}",'UserController@delete_user')->middleware(['auth']);
Route::post("edit_user/{id}",'UserController@edit_user')->middleware(['auth']);

Route::get
		("add_user/bulk_user_registeration",
			'UserController@showInputFileForm')
								->middleware(['auth']);
Route::get
		("/bulk_user_registeration/project_{project_id}",
			'UserController@showInputFileForm')
								->middleware(['auth']);
Route::post
		("/bulk_input_upload",
			'UserController@getBulkInputFile')
								->middleware(['auth']);



/*Route::get
		("/bulk_user_registeration/project_{project_id}",
			'UserController@bulkRegisterationToProject')
								->middleware(['auth']);
*/
Route::auth();

Route::get('/home', 'HomeController@index');
Route::get('/show_users', 'HomeController@showUsers')->middleware(['auth']);
Route::get('/get_pass/{user_id}', 'UserController@getPass')->middleware(['auth']);
Route::get('/generate_pass/{user_id}', 'UserController@generatePass')->middleware(['auth']);

Route::get('/show_project_owners', 'HomeController@showProjectOwners')->middleware(['auth']);

Route::get('/log/last/{num}', 'HomeController@getLastLinesOfLog')->middleware(['auth']);
Route::get('/log/all', 'HomeController@getAllLogsView')->middleware(['auth']);


Route::get('/my_projects', 'HomeController@myProjects')->middleware(['auth']);
Route::get('/show_all_projects', 'HomeController@showAllProjects')->middleware(['auth']);

Route::get("create_project","ProjectController@index")->middleware(['auth']);
Route::post("create_project","ProjectController@create_project")->middleware(['auth']);
Route::post("edit_project/{id}",'ProjectController@edit_project')->middleware(['auth']);
Route::get("delete_project/{id}",'ProjectController@delete_project')->middleware(['auth']);
#============Add Questioner && Users================================

Route::get("add_questioner/toProject_{project_id}",'HomeController@addQuestioners')->middleware(['auth']);

Route::get("add_questioner/questioner_{questioner_id}/toProject_{project_id}",'ProjectController@add_questioner')->middleware(['auth']);


Route::get("remove_questioner/questioner_{questioner_id}/fromProject_{project_id}",'ProjectController@remove_questioner')->middleware(['auth']);


Route::get("add_user/toProject_{project_id}",'HomeController@addUsers')->middleware(['auth']);

Route::get("add_user/user_{user_id}/toProject_{project_id}",'ProjectController@add_user')->middleware(['auth']);

Route::get("remove_user/user_{user_id}/fromProject_{project_id}",'ProjectController@remove_user')->middleware(['auth']);

#=====================================================================



Route::get("create_questioner",'QuestionerController@view_create_questioner_form')->middleware(['auth']);
Route::post("create_questioner",'QuestionerController@create_questioner')->middleware(['auth']);
Route::get
	('/show_questioners', 'HomeController@showQuestioners')->middleware(['auth']);
Route::post("edit_questioner/{id}",'QuestionerController@edit_questioner')->middleware(['auth']);
Route::get("delete_questioner/{id}",'QuestionerController@delete_questioner')->middleware(['auth']);

Route::get
	('/questions_page/{questioner_id}', 'HomeController@showQuestions')->middleware(['auth']);

Route::get
	('/add_question/toQuestioner_{questioner_id}', 'QuestionController@view_create_question_form')->middleware(['auth']);

Route::post
	('/insert_question', 'QuestionController@insertQuestion')->middleware(['auth']);

Route::get
	('/add_score_map/{questioner_id}', 'QuestionerController@view_add_score_map_form')->middleware(['auth']);
Route::get
	('/set_score_map/{begin}/{end}/{val}/{questioner_id}', 'QuestionerController@set_score_map')->middleware(['auth']);
Route::get
	('/get_score_map/{questioner_id}', 'QuestionerController@get_score_map')->middleware(['auth']);


Route::any
	('delete_question/inQuestioner_{questioner_id}/question_{question_id}', 'QuestionController@deleteQuestion')->middleware(['auth']);

Route::any
	('answer/q_{question_id}/option_{option_id}', 'AnswerController@setAnswer')->middleware(['auth']);


