<?php

namespace App\Http\Controllers;

use App\Camp;
use App\CampCategory;
use App\CampProcedure;
use App\Program;
use App\Organization;
use App\User;

use App\Http\Requests\StoreCampRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class CampController extends Controller
{
    protected $programs;
    protected $categories;
    protected $organizations;
    protected $camp_procedures;

    function __construct()
    {
        $this->middleware('permission:camp-list');
        $this->middleware('permission:camp-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:camp-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:camp-delete', ['only' => ['destroy']]);
        $this->programs = Program::all(['id', 'name']);
        $this->categories = CampCategory::all(['id', 'name']); // TODO: Localization
        $this->organizations = null;
        $this->camp_procedures = CampProcedure::all(['id', 'title']); // TODO: Localization
    }

    private function getOrganizationsIfNeeded()
    {
        if (is_null($this->organizations)) {
            if (\Auth::user()->hasPermissionTo('org-list'))
                $this->organizations = Organization::all();
            else
                $this->organizations = array(Organization::find($id=\Auth::user()->org_id));
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
        $organizations = $this-getOrganizationsIfNeeded();
        $camp_procedures = $this->camp_procedures;
        return view('camps.create', compact('programs', 'categories', 'organizations', 'camp_procedures'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCampRequest $request)
    {
        try {
            Camp::create($request->all());
        } catch (\Exception $exception) {
            Log::channel('stderr')->error($exception);
            return redirect()->route('camps.index');
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
        $organizations = $this->getOrganizationsIfNeeded();
        $camp_procedures = $this->camp_procedures;
        return view('camps.edit', compact('programs', 'categories', 'organizations', 'camp_procedures'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @param  \App\Camp  $camp
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCampRequest $request, Camp $camp)
    {
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