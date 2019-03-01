<?php

namespace App\Http\Controllers;

use App\Common;
use App\User;
use App\Religion;
use App\School;
use App\Province;
use App\Program;
use App\Badge;

use App\Http\Requests\StoreUserRequest;

use App\Enums\EducationLevel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    function __construct()
    {
        $this->middleware('auth', ['only' => ['index', 'edit', 'update', 'my_camps']]);
        $this->organizations = null;
    }

    public function index()
    {
        return $this->show(\Auth::user());
    }

    public function authenticate(User $user, bool $me = false)
    {
        if ($me && $user->id != \Auth::user()->id)
            throw new \CampPASSExceptionPermission();
        if (!$user->isActivated() && (!\Auth::user() || !\Auth::user()->isAdmin()))
            throw new \CampPASSException(trans('exception.AccountNotActivate'));
        if ($user->isAdmin())
            throw new \CampPASSException(trans('exception.ErrorDisplayUser'));
    }

    public function show(User $user)
    {
        $this->authenticate($user);
        $badges = $user->isCamper() ? $user->badges : null;
        return view('profiles.show', compact('user', 'badges'));
    }

    public function edit(User $user)
    {
        $this->authenticate($user, $me = true);
        $religions = Common::values(Religion::class);
        $schools = Common::values(School::class);
        $provinces = Common::values(Province::class);
        $programs = Common::values(Program::class);
        $education_levels = EducationLevel::getLocalizedConstants('camper');
        View::share('object', $user);
        return view('profiles.edit', compact('user', 'religions', 'schools', 'provinces', 'programs', 'education_levels'));
    }

    public function update(StoreUserRequest $request, User $user)
    {
        // TODO: It seems that the user will get logged out after updating their password
        $this->authenticate($user, $me = true);
        $user->update($request->all());
        if ($request->hasFile('transcript')) {
            $directory = self::fileDirectory($user->id);
            $path = Storage::disk('local')->putFileAs($directory, $request->file('transcript'), 'transcript.pdf');
        }
        if ($request->hasFile('certificate')) {
            $directory = self::fileDirectory($user->id);
            $path = Storage::disk('local')->putFileAs($directory, $request->file('certificate'), 'certificate.pdf');
        }
        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function notifications()
    {
        return auth()->user()->unreadNotifications()->limit(5)->get()->toArray();
    }

    public function my_camps(User $user)
    {
        if (!$user->isCamper())
            throw new \CampPASSException(trans('app.UnavailableFeature'));
        $this->authenticate($user, $me = true);
        $categorized_registrations = [];
        foreach ($user->registrations as $registration) {
            $status = $registration->getStatus();
            if (!isset($categorized_registrations[$status]))
                $categorized_registrations[$status] = [];
            $categorized_registrations[$status][] = $registration;
        }
        ksort($categorized_registrations);
        return view('profiles.my_camps', compact('categorized_registrations'));
    }

    public function document_download(User $user, $type)
    {
        $directory = self::fileDirectory($user->id);
        return Storage::download("{$directory}/{$type}.pdf");
    }

    public function document_delete(User $user, $type)
    {
        $directory = self::fileDirectory($user->id);
        return Storage::delete("{$directory}/{$type}.pdf");
    }

    public static function fileDirectory(int $user_id)
    {
        return Common::userDirectory($user_id)."/files";
    }
}