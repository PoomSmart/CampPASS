<?php

namespace App\Http\Controllers;

use App\Camp;
use App\User;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;

class CamperController extends Controller
{
    function __construct()
    {
        // TODO: refine
        $this->middleware('permission:camper-list');
        $this->middleware('permission:camper-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        $data = User::campers()->orderBy('id', 'DESC')->paginate(10);
        return view('campers.index', compact('data'))->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create() {}

    public function store(StoreUserRequest $request) {}

    public function edit($id) {}

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
