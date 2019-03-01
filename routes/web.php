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

Route::resource('camps', 'CampController');

Route::prefix('browse-camps')->group(function () {
    Route::get('/', 'CampController@browser')->name('camps.browser');
    Route::get('/organization/{record}', 'CampController@by_organization')->name('camps.by_organization');
    Route::get('/category/{record}', 'CampController@by_category')->name('camps.by_category');
});

Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => ['role:admin']], function () {
        Route::resource('users', 'UserController');
        Route::resource('roles', 'RoleController');
    });
    Route::prefix('camps')->group(function () {
        Route::get('/approve/{camp}', 'CampController@approve')->name('camps.approve');
        Route::get('/registration/{camp}', 'CampController@registration')->name('camps.registration');
    });
    Route::prefix('questions')->group(function () {
        Route::post('/save/{camp}', 'QuestionSetController@store')->name('questions.store');
        Route::get('/{camp}', 'QuestionSetController@show')->name('questions.show');
        Route::post('/finalize/{camp}', 'QuestionSetController@finalize')->name('questions.finalize');
    });
    Route::prefix('application')->group(function () {
        Route::group(['middleware' => ['role:camper']], function () {
            Route::get('/apply/{camp}', 'CampApplicationController@landing')->name('camp_application.landing');
            Route::get('/questions/{camp}', 'CampApplicationController@prepare_questions_answers')->name('camp_application.prepare_questions_answers');
            Route::post('/save', 'CampApplicationController@store')->name('camp_application.store');
            Route::get('/view-answers/{question_set}', 'CampApplicationController@answer_view')->name('camp_application.answer_view');
            Route::get('/submit/{camp}', 'CampApplicationController@submit_application_form')->name('camp_application.submit_application_form');
            Route::get('/file-delete/{answer}', 'CampApplicationController@answer_file_delete')->name('camp_application.answer_file_delete');
            Route::get('/status/{registration}', 'CampApplicationController@status')->name('camp_application.status');
            Route::get('/confirm/{registration}', 'CampApplicationController@confirm')->name('camp_application.confirm');
            Route::post('/withdraw/{registration}', 'CampApplicationController@withdraw')->name('camp_application.withdraw');
        });
        Route::get('/file-download/{answer}', 'CampApplicationController@answer_file_download')->name('camp_application.answer_file_download');
    });
    Route::resource('qualification', 'QualificationController');
    Route::prefix('qualification')->group(function () {
        Route::get('/grade-answers/{registration}/{question_set}', 'QualificationController@answer_grade')->name('qualification.answer_grade');
        Route::post('/manual-grade/{registration}/{question_set}', 'QualificationController@save_manual_grade')->name('qualification.save_manual_grade');
        Route::get('/finalize-form/{form_score}', 'QualificationController@form_finalize')->name('qualification.form_finalize');
        Route::get('/rank/{question_set}', 'CandidateController@rank')->name('qualification.candidate_rank');
        Route::post('/announce/{question_set}', 'CandidateController@announce')->name('qualification.candidate_announce');
        Route::get('/result/{question_set}', 'CandidateController@result')->name('qualification.candidate_result');
    });
    Route::prefix('profile')->group(function () {
        Route::get('/notifications', 'ProfileController@notifications')->name('profiles.notifications');
    });
});

Route::group(['middleware' => ['auth']], function () {
    Route::prefix('profile')->group(function () {
        Route::get('/document-download/{user}/{type}', 'ProfileController@document_download')->name('camp_application.document_download');
        Route::get('/document-delete/{user}/{type}', 'ProfileController@document_delete')->name('camp_application.document_delete');
    });
});

Route::prefix('profile')->group(function () {
    Route::get('/', 'ProfileController@index')->name('profiles.index');
    Route::get('/{user}', 'ProfileController@show')->name('profiles.show');
    Route::get('/edit/{user}', 'ProfileController@edit')->name('profiles.edit');
    Route::get('/my-camps/{user}', 'ProfileController@my_camps')->name('profiles.my_camps');
    Route::put('/update/{user}', 'ProfileController@update')->name('profiles.update');
});

Route::get('/register-landing', 'Auth\RegisterController@landing')->name('register-landing');
Route::get('/register-camper', 'Auth\RegisterController@camper')->name('register-camper');
Route::get('/register-campmaker', 'Auth\RegisterController@campmaker')->name('register-campmaker');

Route::get('/verify-user/{code}', 'Auth\RegisterController@activateUser')->name('activate.user');

Route::get('/', 'HomeController@index')->name('home');

Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, \Config::get('app.locales'))) {
        App::setLocale($locale);
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('locale');