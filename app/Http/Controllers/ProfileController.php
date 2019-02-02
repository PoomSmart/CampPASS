<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    function __construct()
    {
        $this->middleware('auth', ['only' => ['edit', 'store']]);
    }

    public function index()
    {
        return $this->show(\Auth::user());
    }

    public function authenticate(User $user, $me = false)
    {
        if ($me && $user->id != \Auth::user()->id)
            throw new \App\Exceptions\CampPASSException();
        if (!$user->isActivated())
            throw new \App\Exceptions\CampPASSException('This account has not been activated.');
        if ($user->isAdmin())
            throw new \App\Exceptions\CampPASSException('Error displaying the user.');
    }

    public function show(User $user)
    {
        $this->authenticate($user);
        return view('profiles.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authenticate($user, $me = true);
        return view('profiles.edit', compact('user'));
    }
}
