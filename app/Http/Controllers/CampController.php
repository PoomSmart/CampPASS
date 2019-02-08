<?php

namespace App\Http\Controllers;

use App\Camp;
use App\CampCategory;
use App\CampProcedure;
use App\Common;
use App\Program;
use App\Organization;
use App\Region;
use App\User;
use App\Year;

use App\Http\Requests\StoreCampRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class CampController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:camp-create', ['only' => ['create', 'store', 'index']]);
        $this->middleware('permission:camp-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:camp-delete', ['only' => ['destroy']]);
        $this->middleware('permission:camp-approve', ['only' => ['approve']]);
        $this->programs = Common::values(Program::class);
        $this->categories = Common::values(CampCategory::class);
        $this->organizations = null;
        $this->camp_procedures = Common::values(CampProcedure::class);
        $this->regions = Common::values(Region::class);
        $this->years = Common::values(Year::class);
    }

    private function getOrganizationsIfNeeded()
    {
        if (is_null($this->organizations)) {
            if (\Auth::user()->hasPermissionTo('organization-list'))
                $this->organizations = Common::values(Organization::class);
            else
                $this->organizations = array(Organization::find($id = \Auth::user()->organization_id));
        }
        return $this->organizations;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $max = config('const.app.max_paginate');
        $camps = \Auth::user()->isAdmin() ? Camp::latest() : \Auth::user()->belongingCamps()->latest();
        $camps = $camps->paginate($max);
        return view('camps.index', compact('camps'))->with('i', (request()->input('page', 1) - 1) * $max);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $programs = $this->programs;
        $categories = $this->categories;
        $organizations = $this->getOrganizationsIfNeeded();
        $camp_procedures = $this->camp_procedures;
        $regions = $this->regions;
        $years = $this->years;
        return view('camps.create', compact('programs', 'categories', 'organizations', 'camp_procedures', 'regions', 'years'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCampRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCampRequest $request)
    {
        try {
            $user = \Auth::user();
            if ($user->isCampMaker())
                $request->merge(['organization_id' => $user->organization_id]);
            $camp = Camp::create($request->all());
            if ($user->isAdmin()) {
                $camp->approved = true;
                $camp->save();
            }
        } catch (\Exception $exception) {
            logger()->error($exception);
            return redirect()->back()->with('error', 'Camp failed to create.');
        }
        return redirect()->route('camps.index')->with('success', "Camp {$camp} created successfully.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Camp  $camp
     * @return \Illuminate\Http\Response
     */
    public function show(Camp $camp)
    {
        $max = config('const.app.max_paginate');
        View::share('object', $camp);
        if (\Auth::user()->hasPermissionTo('camper-list'))
            $data = $camp->registrations()->paginate($max);
        $category = CampCategory::find($camp->camp_category_id);
        return view('camps.show', compact('camp', 'category', 'data'))->with('i', (request()->input('page', 1) - 1) * $max);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Camp  $camp
     * @return \Illuminate\Http\Response
     */
    public function edit(Camp $camp)
    {
        \Auth::user()->canManageCamp($camp);
        View::share('object', $camp);
        $programs = $this->programs;
        $categories = $this->categories;
        $organizations = $this->getOrganizationsIfNeeded();
        $camp_procedures = $this->camp_procedures;
        $regions = $this->regions;
        $years = $this ->years;
        return view('camps.edit', compact('programs', 'categories', 'organizations', 'camp_procedures', 'regions', 'years'));
    }

    public function approve(Camp $camp)
    {
        $camp->approved = true;
        $camp->save();
        return redirect()->route('camps.index')->with('success', "Camp {$camp} has been approved");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreCampRequest  $request
     * @param  \App\Camp  $camp
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCampRequest $request, Camp $camp)
    {
        \Auth::user()->canManageCamp($camp);
        $camp->update($request->all());
        return redirect()->route('camps.index')->with('success', "Camp {$camp} updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Camp  $camp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Camp $camp)
    {
        \Auth::user()->canManageCamp($camp);
        $camp->delete();
        return redirect()->route('camps.index')->with('success', 'Camp deleted successfully');
    }
}