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
    Route::prefix('camps')->group(function () {
        Route::get('/approve/{camp}', 'CampController@approve')->name('camps.approve');
    });
});

Route::group(['middleware' => ['permission:question-edit']], function() {
    Route::resource('questions', 'QuestionController');
});

Route::group(['middleware' => ['permission:campmaker-edit']], function() {
    Route::resource('campers', 'CamperController');
});

Route::group(['middleware' => ['auth']], function() {
    Route::prefix('application')->group(function () {
        Route::get('/file-download/{json_id}', 'CampApplicationController@file_download')->name('camp_application.file_download');
    });
});

Route::group(['middleware' => ['role:camper']], function() {
    Route::prefix('application')->group(function () {
        Route::get('/apply/{camp}', 'CampApplicationController@landing')->name('camp_application.landing');
        Route::post('/save', 'CampApplicationController@store')->name('camp_application.store');
        Route::get('/view-answers/{question_set}', 'CampApplicationController@answer_view')->name('camp_application.answer_view');
        Route::get('/confirm/{camp}', 'CampApplicationController@submit_application_form')->name('camp_application.submit_application_form');
        Route::get('/file-delete/{json_id}', 'CampApplicationController@file_delete')->name('camp_application.file_delete');
    });
});

// TODO: refine this
Route::group(['middleware' => ['permission:answer-list', 'permission:camper-list']], function() {
    Route::resource('qualification', 'QualificationController');
    Route::prefix('qualification')->group(function () {
        Route::get('/grade-answers/{registration}/{question_set}', 'QualificationController@answer_grade')->name('qualification.answer_grade');
        Route::post('/manual-grade/{registration}/{question_set}', 'QualificationController@save_manual_grade')->name('qualification.save_manual_grade');
        Route::get('/rank/{question_set}', 'CandidateRankController@rank')->name('qualification.candidate_rank');
    });
});

Route::prefix('profile')->group(function () {
    Route::get('/', 'ProfileController@index')->name('profiles.index');
    Route::get('/{user}', 'ProfileController@show')->name('profiles.show');
    Route::get('/edit/{user}', 'ProfileController@edit')->name('profiles.edit');
});

Route::prefix('browse-camps')->group(function () {
    Route::get('/', 'CampBrowserController@index')->name('camp_browser.index');
    Route::get('/organization/{record}', 'CampBrowserController@by_organization')->name('camp_browser.by_organization');
    Route::get('/category/{record}', 'CampBrowserController@by_category')->name('camp_browser.by_category');
});

Route::get('/register-landing', 'Auth\RegisterController@landing')->name('register-landing');
Route::get('/register-camper', 'Auth\RegisterController@camper')->name('register-camper');
Route::get('/register-campmaker', 'Auth\RegisterController@campmaker')->name('register-campmaker');

Route::get('/verify-user/{code}', 'Auth\RegisterController@activateUser')->name('activate.user');

Route::get('/', 'HomeController@index')->name('home');

Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, \Config::get('app.locales')))
        Session::put('locale', $locale);
    return redirect()->back();
})->name('locale');