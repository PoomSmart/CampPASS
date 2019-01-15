<?php

namespace App\Http\Controllers;

use App\Camp;
use App\CampCategory;
use App\Program;
use App\Organization;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $this->organizations = null;
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
        if (is_null($this->organizations)) {
            if (\Auth::user()->hasPermissionTo('org-list'))
                $this->organizations = Organization::all();
            else
                $this->organizations = array(Organization::find($id=\Auth::user()->org_id));
        }
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
        $canList = \Auth::user()->hasPermissionTo('org-list');
        if (!$canList)
            $request->merge(['org_id' => \Auth::user()->org_id]);
        Log::channel('stderr')->error($request->all());
        request()->validate([
            'campcat_id' => 'required|exists:camp_categories,id',
            'org_id' => $canList ? 'required|exists:organizations,id' : 'required|in:{$org_id}',
            'cp_id' => 'required|exists:camp_procedures,id',
            'name_en' => 'required_without:name_th',
            'name_th' => 'required_without:name_en',
            'short_description_en' => 'nullable|required_without:short_description_th|string|max:200',
            'short_description_th' => 'nullable|required_without:short_description_en|string|max:200',
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
        $data = $this->campersForCamp($camp);
        return view('camps.show', compact('camp', 'data'));
    }

    /**
     * Return the campers that belong to the given camp.
     * 
     */
    public function campersForCamp(Camp $camp)
    {
        // TODO: make it correct
        $registrations = $camp->registrations()->select('camper_id')->get();
        $campers = User::campers()->whereIn('id', $registrations)->get();
        return $campers;
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
            'name_en' => 'required_without:name_th|string|max:100',
            'name_th' => 'required_without:name_en|string|max:100',
            'short_description_en' => 'nullable|string|max:200',
            'short_description_th' => 'nullable|string|max:200',
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