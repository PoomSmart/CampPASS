<?php

namespace App\Http\Controllers;

use App\Camp;
use App\CampCategory;
use App\Program;
use App\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class CampController extends Controller
{
    protected $programs;
    protected $categories;
    protected $organizations;

    function __construct()
    {
        $this->middleware('permission:camp-list');
        $this->middleware('permission:camp-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:camp-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:camp-delete', ['only' => ['destroy']]);
        $this->programs = Program::all(['id', 'name']);
        $this->categories = CampCategory::all(['id', 'name']); // TODO: Localization
        $this->organizations = Organization::pluck('name_en'); // TODO: Localization
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $camps = \Auth::user()->hasRole('admin') ? Camp::latest() : \Auth::user()->belongingCamps()->latest();
        $camps = $camps->paginate(5);
        return view('camps.index', compact('camps'))->with('i', (request()->input('page', 1) - 1) * 5);
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
        $organizations = $this->organizations;
        return view('camps.create', compact('programs', 'categories', 'organizations'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'campcat_id' => 'required|exists:camp_categories,id',
            'org_id' => 'required|exists:organizations,id',
            'cp_id' => 'required|exists:camp_procedures,id',
            'name_en' => 'required_without:name_th',
            'name_th' => 'required_without:name_en',
            'short_description' => 'required|max:200',
            'required_programs' => 'nullable|integer',
            'min_gpa' => 'nullable|numeric|min:1.0|max:4.0',
            'other_conditions' => 'nullable|string|max:200',
            'application_fee' => 'nullable|integer|min:0',
            'url' => 'nullable|url|max:150',
            'fburl' => 'nullable|url|max:150',
            'app_opendate' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'app_closedate' => 'nullable|date_format:Y-m-d|after:app_opendate',
            'reg_opendate' => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'reg_closedate' => 'nullable|date_format:Y-m-d|after:reg_opendate',
            'event_startdate' => 'nullable|date_format:Y-m-d|after:tomorrow',
            'event_enddate' => 'nullable|date_format:Y-m-d|after_or_equal:event_startdate',
            'event_location_lat' => 'nullable|numeric|min:-90|max:90',
            'event_location_long' => 'nullable|numeric|min:-180|max:180',
            'quota' => 'integer|min:0',
            'approved' => 'boolean|false', // we prevent camps that try to approve themselves
        ]);
        Camp::create($request->all());
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
        View::share('object', $camp);
        return view('camps.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Camp  $camp
     * @return \Illuminate\Http\Response
     */
    public function edit(Camp $camp)
    {
        View::share('object', $camp);
        $programs = $this->programs;
        $categories = $this->categories;
        $organizations = $this->organizations;
        return view('camps.edit', compact('programs', 'categories', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Camp  $camp
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Camp $camp)
    {
         request()->validate([
            'name_en' => 'required',
            'name_th' => 'required',
            'short_description' => 'required',
        ]);
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
        $camp->delete();
        return redirect()->route('camps.index')->with('success', 'Camp deleted successfully');
    }
}