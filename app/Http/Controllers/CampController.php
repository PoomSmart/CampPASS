<?php

namespace App\Http\Controllers;

use App\Camp;
use App\CampCategory;
use App\CampProcedure;
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
        $this->middleware('permission:camp-list', ['only' => ['show']]); // TODO: This must accept guests too
        $this->middleware('permission:camp-create', ['only' => ['create', 'store', 'index']]);
        $this->middleware('permission:camp-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:camp-delete', ['only' => ['destroy']]);
        $this->middleware('permission:camp-approve', ['only' => ['approve']]);
        $this->programs = Program::all(['id', 'name']);
        $this->categories = CampCategory::all(['id', 'name']);
        $this->organizations = null;
        $this->camp_procedures = CampProcedure::all(['id', 'title']);
        $this->regions = Region::all(['id', 'name']);
        $this->years = Year::all(['id', 'name']);
    }

    private function getOrganizationsIfNeeded()
    {
        if (is_null($this->organizations)) {
            if (\Auth::user()->hasPermissionTo('organization-list'))
                $this->organizations = Organization::all();
            else
                $this->organizations = array(Organization::find($id=\Auth::user()->organization_id));
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
        $camps = \Auth::user()->hasRole('admin') ? Camp::latest() : \Auth::user()->belongingCamps()->latest();
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
            if (\Auth::user()->isCampMaker())
                $request->merge(['organization_id' => \Auth::user()->organization_id]);
            $camp = Camp::create($request->all());
            if (\Auth::user()->isAdmin()) {
                $camp->approved = true;
                $camp->save();
            }
        } catch (\Exception $exception) {
            logger()->error($exception);
            return redirect()->back()->with('error', 'Camp failed to create.');
        }
        return redirect()->route('camps.index')->with('success', 'Camp created successfully.');
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
        $data = $camp->registrations()->paginate($max);
        $category = CampCategory::find($camp->camp_category_id)->getName();
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
        if (!\Auth::user()->canManageCamp($camp))
            return redirect()->back()->with('error', trans('app.NoPermissionError'));
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
        return redirect()->route('camps.index')->with('success', "Camp {$camp->getName()} has been approved");
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
        if (!\Auth::user()->canManageCamp($camp))
            return redirect()->route('camps.index')->with('error', trans('app.NoPermissionError'));
        $camp->update($request->all());
        return redirect()->route('camps.index')->with('success', 'Camp updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Camp  $camp
     * @return \Illuminate\Http\Response
     */
    public function destroy(Camp $camp)
    {
        if (!\Auth::user()->canManageCamp($camp))
            return redirect()->back()->with('error', trans('app.NoPermissionError'));
        $camp->delete();
        return redirect()->route('camps.index')->with('success', 'Camp deleted successfully');
    }
}