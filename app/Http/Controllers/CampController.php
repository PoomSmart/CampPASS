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

use App\Notifications\NewCampRegistered;

use App\Http\Requests\StoreCampRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

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
        $camps = \Auth::user()->isAdmin() ? Camp::latest() : \Auth::user()->getBelongingCamps()->latest();
        $camps = $camps->paginate(Common::maxPagination());
        return Common::withPagination(view('camps.index', compact('camps')));
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
            else
                Common::admin()->notify(new NewCampRegistered($camp));
        } catch (\Exception $exception) {
            logger()->error($exception);
            return redirect()->back()->with('error', 'Camp failed to create.');
        }
        return redirect()->route('camps.index')->with('success', "Camp {$camp} created successfully.");
    }

    public function check(Camp $camp, bool $skip_check = false)
    {
        if (!$skip_check && !$camp->approved)
            throw new \CampPASSException(trans('camp.ApproveFirst'));
    }

    public function show(Camp $camp)
    {
        $this->check($camp, $skip_check = \Auth::user() && \Auth::user()->isAdmin());
        View::share('object', $camp);
        $category = CampCategory::find($camp->camp_category_id);
        return view('camps.show', compact('camp', 'category'));
    }

    public function registration(Camp $camp)
    {
        $this->check($camp);
        $question_set = $camp->question_set;
        if ($question_set && $question_set->announced)
            return redirect()->route('qualification.candidate_result', $question_set->id);
        $max = config('const.app.max_paginate');
        View::share('object', $camp);
        if (\Auth::user()->hasPermissionTo('camper-list')) {
            $registrations = $camp->registrations();
            $total_registrations = $registrations->count();
            $data = $registrations->paginate(Common::maxPagination());
        } else {
            $data = null;
            $total_registrations = 0;
        }
        $category = CampCategory::find($camp->camp_category_id);
        return Common::withPagination(view('camps.registration', compact('camp', 'category', 'data', 'total_registrations')));
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
        $input = $request->except(Camp::$once);
        $camp->update($input);
        return redirect()->back()->with('success', "Camp {$camp} has been updated successfully.");
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
    public function get_camps($query_pairs = null, bool $categorized = false)
    {
        $camps = Camp::allApproved();
        if ($query_pairs) {
            // Apply search query right away if there are any
            $pair = $query_pairs[0];
            $camps = $camps->where($pair[0], $pair[2] ? $pair[2] : '=', $pair[2] ? "%{$pair[1]}%" : $pair[1]);
            unset($query_pairs[0]);
            foreach ($query_pairs as $pair) {
                $camps = $camps->orWhere($pair[0], $pair[2] ? $pair[2] : '=', $pair[2] ? "%{$pair[1]}%" : $pair[1]);
            }
        }
        if (!$categorized) {
            $result = [];
            $camps->chunk(3, function ($chunk) use (&$result) {
                foreach ($chunk as $camp) {
                    $result[] = $camp;
                }
            });
            // Randomize the order of camps
            shuffle($result);
            return $result;
        } else {
            $max_fetch = config('const.camp.max_fetch');
            $output_camps = [];
            $category_ids = [];
            $category_count = CampCategory::count();
            // The maximum number that should not exceed, or we would otherwise do unnecessary work
            $camps = $camps->limit($max_fetch * $category_count);
            $camps->chunk(3, function ($chunk) use (&$max_fetch, &$category_count, &$output_camps, &$category_ids) {
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
            // Sort the camps with their category alphabetically
            ksort($output_camps);
            // Randomize the order of camps in each category
            foreach ($output_camps as &$category) {
                shuffle($category);
            }
            return [
                'categorized_camps' => $output_camps,
                'category_ids' => $category_ids,
            ];
        }
    }
    
    public function browser()
    {
        $query = Input::get('query', null);
        $data = $this->get_camps($query_pairs = $query ? [
            [ 'name_en', $query, 'LIKE', ],
            [ 'name_th', $query, 'LIKE', ],
        ] : null, $categorized = true);
        $categorized_camps = $data['categorized_camps'];
        $category_ids = $data['category_ids'];
        return view('camps.browser', compact('categorized_camps', 'category_ids'));
    }

    public function by_category(CampCategory $record)
    {
        $camps = $this->get_camps($query_pairs = [
            'camp_category_id', $record->id, null,
        ]);
        return view('camps.by_category', compact('camps', 'record'));
    }

    public function by_organization(Organization $record)
    {
        $camps = $this->get_camps($query_pairs = [
            'organization_id', $record->id, null,
        ]);
        return view('camps.by_category', compact('camps', 'record'));
    }
}