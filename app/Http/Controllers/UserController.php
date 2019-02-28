<?php

namespace App\Http\Controllers;

use App\User;
use App\Answer;
use App\QuestionManager;

use App\Enums\QuestionType;

use App\Http\Requests\StoreUserRequest;
use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use DB;
use Hash;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:user-list');
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $max = config('const.app.max_paginate');
        $data = User::orderBy('username')->paginate($max);
        return view('users.index', compact('data'))->with('i', ($request->input('page', 1) - 1) * $max);
    }
    
    public function create()
    {
        return null;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->create($request->all());
            $user->assignRole($request->input('roles'));
            event(new Registered($user));
        } catch (\Exception $exception) {
            logger()->error($exception);
            return redirect()->route('users.index');
        }
        return redirect()->route('users.index')->with('message', 'User created successfully. Activation awaits.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $data = $user->getBelongingCamps()->orderBy('id')->get();
        return view('users.show', compact('user', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\StoreUserRequest $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUserRequest $request, User $user)
    {
        $user->update($request->all());
        DB::table('model_has_roles')->where('model_id', $user->id)->delete();
        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if ($user->isCamper()) {
            foreach (Answer::where('camper_id', $user->id)->cursor() as $answer) {
                $question = $answer->question;
                if ($question->type == QuestionType::FILE) {
                    $registration = $answer->registration;
                    $camp = $registration->camp;
                    $directory = QuestionManager::questionSetDirectory($camp->id);
                    Storage::disk('local')->delete("{$directory}/{$question->json_id}/{$user->id}");
                }
            }
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}