<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/','Auth\LoginController@showLoginForm');
Route::get('/under-maintenance','MiscController@maintenance');
Route::get('/terms-and-conditions','MiscController@tnc');
Auth::routes();

Route::group(['middleware' => ['guest']], function () {
	Route::get('/resend-activation','Auth\ActivateController@resendActivation');
	Route::post('/resend-activation',array('as' => 'user.resend-activation','uses' => 'Auth\ActivateController@postResendActivation'));
	Route::get('/activate-account/{token}','Auth\ActivateController@activateAccount');

	Route::get('/auth/{provider}', 'SocialLoginController@providerRedirect');
    Route::get('/auth/{provider}/callback', 'SocialLoginController@providerRedirectCallback');

	Route::get('/verify-purchase', 'AccountController@verifyPurchase');
	Route::post('/verify-purchase', 'AccountController@postVerifyPurchase');
	Route::resource('/install', 'AccountController',['only' => ['index', 'store']]);
	Route::get('/update','AccountController@updateApp');
	Route::post('/update',array('as' => 'update-app','uses' => 'AccountController@postUpdateApp'));
});

Route::group(['middleware' => ['auth','web','account']],function(){
	Route::get('/verify-security','Auth\TwoFactorController@verifySecurity');
	Route::post('/verify-security',array('as' => 'verify-security','uses' => 'Auth\TwoFactorController@postVerifySecurity'));
});

Route::group(['middleware' => ['auth','web','account','lock_screen']], function () {
	
	Route::get('/home', 'HomeController@index');
	Route::post('/sidebar', 'HomeController@sidebar');
	Route::post('/setup-guide',array('as' => 'setup-guide','uses' => 'ConfigurationController@setupGuide'));

	Route::get('/release-license','AccountController@releaseLicense');
	Route::get('/check-update','AccountController@checkUpdate');
	Route::post('/filter','HomeController@filter');

	Route::group(['middleware' => ['permission:manage-configuration']], function() {
		Route::get('/configuration', 'ConfigurationController@index');
		Route::post('/configuration',array('as' => 'configuration.store','uses' => 'ConfigurationController@store'));
		Route::post('/configuration-logo',array('as' => 'configuration.logo','uses' => 'ConfigurationController@logo'));
		Route::post('/configuration-mail',array('as' => 'configuration.mail','uses' => 'ConfigurationController@mail'));
		Route::post('/configuration-sms',array('as' => 'configuration.sms','uses' => 'ConfigurationController@sms'));
		Route::post('/configuration-menu', array('as' => 'configuration.menu','uses' => 'ConfigurationController@menu')); 

		Route::model('task_category','\App\TaskCategory');
		Route::post('/task-category/lists','TaskCategoryController@lists');
		Route::resource('/task-category', 'TaskCategoryController'); 

		Route::model('task_priority','\App\TaskPriority');
		Route::post('/task-priority/lists','TaskPriorityController@lists');
		Route::resource('/task-priority', 'TaskPriorityController');
	});
	
	Route::group(['middleware' => ['permission:manage-localization']], function () {
		Route::post('/localization/lists','LocalizationController@lists');
		Route::resource('/localization', 'LocalizationController'); 
		Route::post('/localization/addWords',array('as'=>'localization.add-words','uses'=>'LocalizationController@addWords'));
		Route::patch('/localization/plugin/{locale}',array('as'=>'localization.plugin','uses'=>'LocalizationController@plugin'));
		Route::patch('/localization/updateTranslation/{id}', ['as' => 'localization.update-translation','uses' => 'LocalizationController@updateTranslation']);
	});

	Route::group(['middleware' => ['permission:manage-backup']], function() {
		Route::model('backup','\App\Backup');
		Route::post('/backup/lists','BackupController@lists');
		Route::resource('/backup', 'BackupController',['only' => ['index','show','store','destroy']]); 
	});

	Route::group(['middleware' => ['permission:manage-ip-filter']], function() {
		Route::model('ip_filter','\App\IpFilter');
		Route::post('/ip-filter/lists','IpFilterController@lists');
		Route::resource('/ip-filter', 'IpFilterController'); 
	});

	Route::group(['middleware' => ['permission:manage-todo']], function() {
		Route::model('todo','\App\Todo');
		Route::resource('/todo', 'TodoController'); 
	});

	Route::group(['middleware' => ['permission:manage-template']], function() {
		Route::model('template','\App\Template');
		Route::post('/template/lists','TemplateController@lists');
		Route::resource('/template', 'TemplateController'); 
	});
	Route::post('/template/content','TemplateController@content',['middleware' => ['permission:enable_email_template']]);
	
	Route::group(['middleware' => ['permission:manage-email-log']], function () {
		Route::model('email','\App\Email');
		Route::post('/email/lists','EmailController@lists');
		Route::resource('/email', 'EmailController',['only' => ['index','show']]); 
	});
	
	Route::group(['middleware' => ['permission:manage-custom-field']], function() {
		Route::model('custom_field','\App\CustomField');
		Route::post('/custom-field/lists','CustomFieldController@lists');
		Route::resource('/custom-field', 'CustomFieldController'); 
	});
	
	Route::group(['middleware' => ['permission:manage-message']], function() {
		Route::get('/message', 'MessageController@index'); 
		Route::post('/load-message','MessageController@load');
		Route::post('/message/{type}/lists','MessageController@lists');
		Route::get('/message/forward/{token}','MessageController@forward');
		Route::post('/message', ['as' => 'message.store', 'uses' => 'MessageController@store']);
		Route::post('/message-reply/{id}', ['as' => 'message.reply', 'uses' => 'MessageController@reply']);
		Route::post('/message-forward/{token}', ['as' => 'message.post-forward', 'uses' => 'MessageController@postForward']);
		Route::get('/message/{token}/download','MessageController@download');
		Route::post('/message/starred','MessageController@starred');
		Route::get('/message/{token}', array('as' => 'message.view', 'uses' => 'MessageController@view'));
		Route::delete('/message/{id}/trash', array('as' => 'message.trash', 'uses' => 'MessageController@trash'));
		Route::post('/message/restore', array('as' => 'message.restore', 'uses' => 'MessageController@restore'));
		Route::delete('/message/{id}/delete', array('as' => 'message.destroy', 'uses' => 'MessageController@destroy'));
	});

	Route::group(['middleware' => ['permission:manage-role']], function() {
		Route::model('role','\App\Role');
		Route::post('/role/lists','RoleController@lists');
		Route::resource('/role', 'RoleController'); 
	});
		
	Route::group(['middleware' => ['permission:manage-permission']], function() {
		Route::model('permission','\App\Permission');
		Route::post('/permission/lists','PermissionController@lists');
		Route::resource('/permission', 'PermissionController'); 
		Route::get('/save-permission','PermissionController@permission');
		Route::post('/save-permission',array('as' => 'permission.save-permission','uses' => 'PermissionController@savePermission'));
	});
	
	Route::model('chat','\App\Chat');
	Route::resource('/chat', 'ChatController',['only' => 'store']); 
	Route::post('/fetch-chat','ChatController@index');

	Route::get('/lock','HomeController@lock');
	Route::post('/lock',array('as' => 'unlock','uses' => 'HomeController@unlock'));

	Route::get('/change-localization/{locale}','LocalizationController@changeLocalization',['middleware' => ['permission:change-localization']]);

	Route::model('user','\App\User');
	Route::post('/user/lists','UserController@lists');
	Route::resource('/user', 'UserController',['except' => ['store','edit']]); 
	Route::post('/user/profile-update/{id}',array('as' => 'user.profile-update','uses' => 'UserController@profileUpdate'));
	Route::post('/user/social-update/{id}',array('as' => 'user.social-update','uses' => 'UserController@socialUpdate'));
	Route::post('/user/custom-field-update/{id}',array('as' => 'user.custom-field-update','uses' => 'UserController@customFieldUpdate'));
	Route::post('/user/avatar/{id}',array('as' => 'user.avatar','uses' => 'UserController@avatar'));
	Route::post('/change-user-status','UserController@changeStatus');
	Route::post('/force-change-user-password/{user_id}',array('as' => 'user.force-change-password','uses' => 'UserController@forceChangePassword'));
	Route::get('/change-password', 'UserController@changePassword');
	Route::post('/change-password',array('as'=>'change-password','uses' =>'UserController@doChangePassword'));
	Route::post('/user/email/{id}',array('as' => 'user.email', 'uses' => 'UserController@email'));
	
	Route::post('/user-location/lists','UserLocationController@lists');
	Route::model('user_location','\App\UserLocation');
	Route::post('/user-location/{id}',array('uses' => 'UserLocationController@store','as' => 'user-location.store'));
	Route::resource('/user-location', 'UserLocationController',['except' => ['store']]); 

	Route::model('department','\App\Department');
	Route::post('/department/lists','DepartmentController@lists');
	Route::resource('/department', 'DepartmentController'); 
	
	Route::model('designation','\App\Designation');
	Route::post('/designation/lists','DesignationController@lists');
	Route::resource('/designation', 'DesignationController'); 
	Route::post('/designation/hierarchy','DesignationController@hierarchy');
	
	Route::model('location','\App\Location');
	Route::post('/location/lists','LocationController@lists');
	Route::resource('/location', 'LocationController'); 
	Route::post('/location/hierarchy','LocationController@hierarchy');
	
	Route::model('announcement','\App\Announcement');
	Route::post('/announcement/lists','AnnouncementController@lists');
	Route::resource('/announcement', 'AnnouncementController'); 

	Route::model('task','\App\Task');
	Route::post('/task/lists','TaskController@lists');
	Route::post('/task/fetch','TaskController@fetch');
	Route::post('/task/top-chart','TaskController@topChart');
	Route::resource('/task', 'TaskController'); 
	Route::post('/task-detail','TaskController@detail');
	Route::post('/task-description','TaskController@description');
	Route::post('/task-starred','TaskController@starred');
	Route::post('/task-activity','TaskController@activity');
	Route::post('/task-email-content','TaskController@emailContent');
	Route::get('/task-user-email/{task_id}/{user_id}','TaskController@userEmail');
	Route::post('/task-user-email/{task_id}/{user_id}',array('as' => 'task.user-email','uses' => 'TaskController@postUserEmail'));
	Route::post('/task-comment','TaskController@comment');
	Route::post('/task/{id}/progress',array('as' => 'task.progress','uses' => 'TaskController@progress'));
	Route::post('/task-rating-type','TaskController@ratingType');

	Route::post('/task/{id}/sub-task',array('as' => 'task.add-sub-task','uses' => 'SubTaskController@store'));
	Route::post('/sub-task/lists','SubTaskController@lists');
	Route::get('/sub-task/{id}/edit','SubTaskController@edit');
	Route::patch('/sub-task/{id}',array('as' => 'sub-task.update','uses' => 'SubTaskController@update'));
	Route::delete('/sub-task/{id}',array('as' => 'sub-task.destroy','uses' => 'SubTaskController@destroy'));

	Route::get('/task-rating/{task_id}/{user_id}','TaskController@rating');
	Route::post('/task-rating/lists','TaskController@listRating');
	Route::post('/task-rating/{task_id}/{user_id}',['as' => 'task.store-rating', 'uses' => 'TaskController@storeRating']);
	Route::post('/task-rating-destroy','TaskController@destroyTaskRating');

	Route::post('/sub-task-rating/lists','TaskController@listSubTaskRating');
	Route::get('/sub-task-rating/{task_id}/{user_id}/show','TaskController@showSubTaskRating');
	Route::get('/sub-task-rating/{task_id}/{user_id}','TaskController@subTaskRating');
	Route::post('/sub-task-rating-destroy','TaskController@destroySubTaskRating');
	Route::get('/user-task-rating','TaskController@userTaskRating');
	Route::post('/user-task-rating/lists','TaskController@userTaskRatingLists');
	Route::get('/user-task-summary','TaskController@userTaskSummary');
	Route::post('/user-task-summary/lists','TaskController@userTaskSummaryLists');

	Route::post('/task-note/{id}',array('uses' => 'TaskNoteController@store','as' => 'task-note.store'));

	Route::post('/task-comment/{id}',array('uses' => 'TaskCommentController@store','as' => 'task-comment.store'));
	Route::delete('/task-comment/{id}',array('uses' => 'TaskCommentController@destroy','as' => 'task-comment.destroy'));

	Route::post('/task-attachment/lists','TaskAttachmentController@lists');
	Route::post('/task-attachment/{id}',array('uses' => 'TaskAttachmentController@store','as' => 'task-attachment.store'));
	Route::delete('/task-attachment/{id}',array('uses' => 'TaskAttachmentController@destroy','as' => 'task-attachment.destroy'));
	Route::get('/task-attachment/download/{id}','TaskAttachmentController@download');
});
