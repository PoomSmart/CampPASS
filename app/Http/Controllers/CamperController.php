<?php

namespace App\Http\Controllers;

use App\Camp;
use App\User;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

class CamperController extends UserController
{
    public function index(Request $request)
    {
        $data = User::campers()->orderBy('id', 'DESC')->paginate(10);
        return view('campers.index', compact('data'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function campersForCamp(Camp $camp)
    {
        // TODO: make it correct
        $registrations = $camp->registrations()->select('id')->get();
    }

    public function create() {}

    public function store(StoreUserRequest $request) {}

    public function edit($id) {}

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
