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

Route::get('/', function () {
    return view('home');
});

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

Route::group(['middleware' => ['permission:question-list']], function() {
    Route::resource('questions', 'QuestionController');
});

Route::group(['middleware' => ['role:campmaker']], function() {
    Route::resource('campers', 'CamperController');
});

Route::get('/register-landing', 'Auth\RegisterController@landing')->name('register-landing');
Route::get('/register-camper', 'Auth\RegisterController@camper')->name('register-camper');
Route::get('/register-campmaker', 'Auth\RegisterController@campmaker')->name('register-campmaker');

Route::get('/verify-user/{code}', 'Auth\RegisterController@activateUser')->name('activate.user');

Route::get('/home', 'HomeController@index')->name('home');
