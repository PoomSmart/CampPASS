<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::group(['middleware' => ['role:admin']], function() {
    Route::resource('users', 'UserController');
});

Route::group(['middleware' => ['permission:role-list']], function() {
    Route::resource('roles', 'RoleController');
});

Route::group(['middleware' => ['permission:camp-list']], function() {
    Route::resource('camps', 'CampController');
    Route::get('/approve/{camp}', 'CampController@approve')->name('camps.approve');
});

Route::group(['middleware' => ['permission:question-edit']], function() {
    Route::resource('questions', 'QuestionController');
});

Route::group(['middleware' => ['permission:campmaker-edit']], function() {
    Route::resource('campers', 'CamperController');
});

Route::group(['middleware' => ['auth']], function() {
    Route::get('/application/file-download/{json_id}', 'CampApplicationController@file_download')->name('camp_application.file_download');
});

Route::group(['middleware' => ['role:camper']], function() {
    Route::get('/application/apply/{camp}', 'CampApplicationController@landing')->name('camp_application.landing');
    Route::post('/application/save', 'CampApplicationController@store')->name('camp_application.store');
    Route::get('/application/view-answers/{question_set}', 'CampApplicationController@answer_view')->name('camp_application.answer_view');
    Route::get('/application/confirm/{camp}', 'CampApplicationController@submit_application_form')->name('camp_application.submit_application_form');
    Route::get('/application/file-delete/{json_id}', 'CampApplicationController@file_delete')->name('camp_application.file_delete');
});

// TODO: refine this
Route::group(['middleware' => ['permission:answer-list', 'permission:camper-list']], function() {
    Route::resource('qualification', 'QualificationController');
    Route::get('/ranking/view-answers/{camper}/{question_set}', 'QualificationController@answer_view')->name('qualification.answer_view');
    Route::get('/ranking/rank/{question_set}', 'CandidateRankController@rank')->name('qualification.candidate_rank');
});

Route::resource('camp_browser', 'CampBrowserController');

Route::get('/register-landing', 'Auth\RegisterController@landing')->name('register-landing');
Route::get('/register-camper', 'Auth\RegisterController@camper')->name('register-camper');
Route::get('/register-campmaker', 'Auth\RegisterController@campmaker')->name('register-campmaker');

Route::get('/verify-user/{code}', 'Auth\RegisterController@activateUser')->name('activate.user');

Route::get('/', 'HomeController@index')->name('home');
