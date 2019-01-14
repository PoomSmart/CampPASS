<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use DB;
use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::orderBy('id', 'DESC')->paginate(10);
        return view('users.index', compact('data'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // TODO: User creation page for admin
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $data = $user->belongingCamps()->orderBy('id')->get();
        return view('users.show', compact('user', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'username' => 'required',
            'email' => 'required|email|unique:users,email,{$id}',
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
        $input = $request->all();
        if (!empty($input['password'])) { 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = array_except($input, array('password'));    
        }
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();
        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}