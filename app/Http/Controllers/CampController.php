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
use Illuminate\Support\Facades\View;

class CampController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:camp-create', ['only' => ['create', 'store', 'index']]);
        $this->middleware('permission:camp-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:camp-delete', ['only' => ['destroy']]);
        $this->middleware('permission:camp-approve', ['only' => ['approve']]);
        $this->middleware('permission:camper-list', ['only' => ['registration']]);
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

    public function index()
    {
        $max = config('const.app.max_paginate');
        $camps = \Auth::user()->isAdmin() ? Camp::latest() : \Auth::user()->getBelongingCamps()->latest();
        $camps = $camps->paginate($max);
        return view('camps.index', compact('camps'))->with('i', (request()->input('page', 1) - 1) * $max);
    }

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

    public function store(StoreCampRequest $request)
    {
        try {
            $user = \Auth::user();
            if ($user->isCampMaker())
                $request->merge(['organization_id' => $user->organization_id]);
            $camp = Camp::create($request->all());
            if ($user->isAdmin())
                $camp->approve();
        } catch (\Exception $exception) {
            logger()->error($exception);
            return redirect()->back()->with('error', 'Camp failed to create.');
        }
        return redirect()->route('camps.index')->with('success', "Camp {$camp} created successfully.");
    }

    public function show(Camp $camp)
    {
        View::share('object', $camp);
        $category = CampCategory::find($camp->camp_category_id);
        return view('camps.show', compact('camp', 'category'));
    }

    public function registration(Camp $camp)
    {
        // TODO: Will we allow users to see this page if the candidates have been announced?
        $max = config('const.app.max_paginate');
        View::share('object', $camp);
        if (\Auth::user()->hasPermissionTo('camper-list')) {
            $form_scores = $camp->getFormScores();
            $data = $form_scores ? $form_scores->paginate($max) : null;
        } else
            $data = null;
        $category = CampCategory::find($camp->camp_category_id);
        return view('camps.registration', compact('camp', 'category', 'data'))->with('i', (request()->input('page', 1) - 1) * $max);
    }
    
    public function edit(Camp $camp)
    {
        \Auth::user()->canManageCamp($camp);
        View::share('object', $camp);
        $programs = $this->programs;
        $categories = $this->categories;
        $organizations = $this->getOrganizationsIfNeeded();
        $camp_procedures = $this->camp_procedures;
        $regions = $this->regions;
        $years = $this->years;
        return view('camps.edit', compact('programs', 'categories', 'organizations', 'camp_procedures', 'regions', 'years'));
    }

    public function approve(Camp $camp)
    {
        $camp->approve();
        return redirect()->back()->with('success', "Camp {$camp} has been approved.");
    }
    
    public function update(StoreCampRequest $request, Camp $camp)
    {
        \Auth::user()->canManageCamp($camp);
        $camp->update($request->all());
        return redirect()->route('camps.index')->with('success', "Camp {$camp} has been updated successfully.");
    }

    public function destroy(Camp $camp)
    {
        \Auth::user()->canManageCamp($camp);
        $camp->delete();
        return redirect()->route('camps.index')->with('success', 'Camp deleted successfully');
    }

    /**
     * Return the camps (filtered by column-value or neither) as a sort of array.
     * 
     */
    public function get_camps($column = null, $value = null)
    {
        $camps = Camp::allApproved();
        if ($column && $value) {
            $camps = $camps->where($column, $value);
            $result = [];
            $camps->latest()->chunk(3, function ($chunk) use (&$result) {
                foreach ($chunk as $camp) {
                    $result[] = $camp;
                }
            });
            return $result;
        } else {
            $max_fetch = config('const.camp.max_fetch');
            $output_camps = [];
            $category_ids = [];
            $category_count = CampCategory::count();
            $camps->latest()->chunk(3, function ($chunk) use (&$max_fetch, &$category_count, &$output_camps, &$category_ids) {
                foreach ($chunk as $camp) {
                    $category = $camp->camp_category;
                    $category_name = $category->getName();
                    if (!isset($output_camps[$category_name])) {
                        $output_camps[$category_name] = [];
                        $category_ids[$category_name] = $category->id;
                    }
                    if (count($output_camps[$category_name]) < $max_fetch) {
                        $output_camps[$category_name][] = $camp;
                        if (--$category_count == 0)
                            break;
                    }
                }
            });
            return [
                'categorized_camps' => $output_camps,
                'category_ids' => $category_ids,
            ];
        }
    }
    
    public function browser()
    {
        $data = $this->get_camps();
        $categorized_camps = $data['categorized_camps'];
        $category_ids = $data['category_ids'];
        return view('camps.browser', compact('categorized_camps', 'category_ids'));
    }

    public function by_category(CampCategory $record)
    {
        $camps = $this->get_camps('camp_category_id', $record->id);
        return view('camps.by_category', compact('camps', 'record'));
    }

    public function by_organization(Organization $record)
    {
        $camps = $this->get_camps('organization_id', $record->id);
        return view('camps.by_category', compact('camps', 'record'));
    }
}