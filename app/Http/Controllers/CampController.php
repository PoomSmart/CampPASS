<?php


namespace App\Http\Controllers;


use App\Camp;
use Illuminate\Http\Request;


class CampController extends Controller
{ 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:camp-list');
         $this->middleware('permission:camp-create', ['only' => ['create','store']]);
         $this->middleware('permission:camp-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:camp-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $camps = Camp::latest()->paginate(5);
        return view('camps.index',compact('camps'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('camps.create');
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
            'name_en' => 'required',
            'name_th' => 'required',
            'short_description' => 'required',
        ]);


        Camp::create($request->all());


        return redirect()->route('camps.index')
                        ->with('success','Camp created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Camp  $camp
     * @return \Illuminate\Http\Response
     */
    public function show(Camp $camp)
    {
        return view('camps.show',compact('camp'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Camp  $camp
     * @return \Illuminate\Http\Response
     */
    public function edit(Camp $camp)
    {
        return view('camps.edit',compact('camp'));
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


        return redirect()->route('camps.index')
                        ->with('success','Camp updated successfully');
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


        return redirect()->route('camps.index')
                        ->with('success','Camp deleted successfully');
    }
}