<?php

Auth::routes();

Route::resource('camps', 'CampController');

Route::prefix('browse-camps')->group(function () {
    Route::get('/', 'CampController@browser')->name('camps.browser');
    Route::get('/organization/{record}', 'CampController@by_organization')->name('camps.by_organization');
    Route::get('/category/{record}', 'CampController@by_category')->name('camps.by_category');
});

Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => ['role:admin']], function () {
        Route::resource('users', 'UserController')->except('create');
        Route::resource('roles', 'RoleController');
    });
    Route::prefix('notifications')->group(function () {
        Route::get('/', 'NotificationController@index')->name('notifications.index');
        Route::get('/notifications', 'NotificationController@notifications')->name('profiles.notifications');
        Route::get('/all-notifications', 'NotificationController@all_notifications')->name('notifications.all_notifications');
    });
    Route::prefix('camps')->group(function () {
        Route::get('/approve/{camp}', 'CampController@approve')->name('camps.approve');
        Route::get('/registration/{camp}', 'CampController@registration')->name('camps.registration');
        Route::get('/attribute-download/{camp}/{name}', 'CampController@attribute_download')->name('camps.attribute_download');
        Route::get('/attribute-delete/{camp}/{name}', 'CampController@attribute_delete')->name('camps.attribute_delete');
    });
    Route::prefix('questions')->group(function () {
        Route::post('/save/{camp}', 'QuestionSetController@store')->name('questions.store');
        Route::get('/{camp}', 'QuestionSetController@show')->name('questions.show');
        Route::post('/finalize/{camp}', 'QuestionSetController@finalize')->name('questions.finalize');
    });
    Route::prefix('application')->group(function () {
        Route::group(['middleware' => ['permission:answer-edit']], function () {
            Route::get('/apply/{camp}', 'CampApplicationController@landing')->name('camp_application.landing');
            Route::get('/questions/{camp}', 'CampApplicationController@prepare_questions_answers')->name('camp_application.prepare_questions_answers');
            Route::post('/save/{camp}', 'CampApplicationController@store')->name('camp_application.store');
            Route::get('/answers-view/{question_set}', 'CampApplicationController@answer_view')->name('camp_application.answer_view');
            Route::get('/submit/{camp}', 'CampApplicationController@submit_application_form')->name('camp_application.submit_application_form');
            Route::get('/answer-file-delete/{answer}', 'CampApplicationController@answer_file_delete')->name('camp_application.answer_file_delete');
            Route::get('/status/{registration}', 'CampApplicationController@status')->name('camp_application.status');
            Route::post('/payment-upload/{registration}', 'CampApplicationController@payment_upload')->name('camp_application.payment_upload');
            Route::get('/payment-delete/{registration}', 'CampApplicationController@payment_delete')->name('camp_application.payment_delete');
            Route::post('/consent-upload/{registration}', 'CampApplicationController@consent_upload')->name('camp_application.consent_upload');
            Route::get('/consent-delete/{registration}', 'CampApplicationController@consent_delete')->name('camp_application.consent_delete');
            Route::get('/confirm/{registration}', 'CampApplicationController@confirm')->name('camp_application.confirm');
            Route::get('/unreturn/{registration}', 'CampApplicationController@unreturn')->name('camp_application.unreturn');
            Route::get('/withdraw/{registration}', 'CampApplicationController@withdraw')->name('camp_application.withdraw');
            Route::post('/withdraw/{registration}', 'CampApplicationController@withdraw')->name('camp_application.withdraw');
        });
        Route::get('/payment-download/{registration}', 'CampApplicationController@payment_download')->name('camp_application.payment_download');
        Route::get('/consent-download/{registration}', 'CampApplicationController@consent_download')->name('camp_application.consent_download');
        Route::get('/answer-file-download/{answer}', 'CampApplicationController@answer_file_download')->name('camp_application.answer_file_download');
    });
    Route::prefix('qualification')->group(function () {
        Route::get('/form-grade/{registration}/{question_set}', 'QualificationController@form_grade')->name('qualification.form_grade');
        Route::post('/manual-grade/{registration}/{question_set}', 'QualificationController@save_manual_grade')->name('qualification.save_manual_grade');
        Route::get('/form-finalize/{form_score}', 'QualificationController@form_finalize')->name('qualification.form_finalize');
        Route::post('/interview-save/{camp}', 'CandidateController@interview_save')->name('qualification.interview_save');
        Route::get('/interview-announce/{question_set}', 'CandidateController@interview_announce')->name('qualification.interview_announce');
        Route::post('/form-pass-save/{camp}', 'QualificationController@form_pass_save')->name('qualification.form_pass_save');
        Route::post('/document-approve/{camp}', 'CandidateController@document_approve_save')->name('qualification.document_approve_save');
        Route::get('/rank/{question_set}', 'CandidateController@rank')->name('qualification.candidate_rank');
        Route::post('/announce/{question_set}', 'CandidateController@announce')->name('qualification.candidate_announce');
        Route::get('/result/{question_set}', 'CandidateController@result')->name('qualification.candidate_result');
        Route::get('/data-export-selection/{question_set}', 'CandidateController@data_download_selection')->name('qualification.data_download_selection');
        Route::get('/data-download/{question_set}', 'CandidateController@data_download')->name('qualification.data_download');
        Route::get('/profile-qualification/{registration}', 'QualificationController@show_profile_detailed')->name('qualification.show_profile_detailed');
        Route::post('/form-return/{registration}', 'QualificationController@form_return')->name('qualification.form_return');
        Route::post('/form-reject/{registration}', 'QualificationController@form_reject')->name('qualification.form_reject');
    });
    Route::prefix('analytic')->group(function () {
        Route::get('/analytic/{camp}', 'AnalyticController@analytic')->name('analytic.analytic');
    });
    Route::prefix('profile')->group(function () {
        Route::get('/document-download/{user}/{type}', 'ProfileController@document_download')->name('camp_application.document_download');
        Route::get('/document-delete/{user}/{type}', 'ProfileController@document_delete')->name('camp_application.document_delete');
        Route::get('/profile-delete/{user}', 'ProfileController@profile_picture_delete')->name('camp_application.profile_picture_delete');
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
    if (in_array($locale, config('app.locales'))) {
        App::setLocale($locale);
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('locale');

/* About */

Route::get('/what-is-camppass', function () {
    return view('about.what_is_camppass');
})->name('what-is-camppass');

Route::get('/how-camppass-works', function () {
    return view('about.how_camppass_works');
})->name('how-camppass-works');

Route::get('/about-us', function () {
    return view('about.about_us');
})->name('about-us');

Route::get('/terms-of-services', function () {
    return view('about.terms_of_services');
})->name('terms-of-services');

Route::get('/privacy-policy', function () {
    return view('about.privacy_policy');
})->name('privacy-policy');