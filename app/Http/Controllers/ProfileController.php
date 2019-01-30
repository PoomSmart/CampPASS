<?php

namespace App\Http\Controllers;

use App\User;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return $this->show(\Auth::user());
    }

    public function show(User $user)
    {
        if (!$user->isActivated())
            return redirect()->back()->with('error', 'This account has not been activated.');
        if ($user->isAdmin())
            return redirect()->back()->with('error', 'Error displaying the user');
        return view('profiles.show', compact('user'));
    }
}
