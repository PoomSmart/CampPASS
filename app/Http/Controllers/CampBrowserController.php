<?php

namespace App\Http\Controllers;

use App\Camp;

use Illuminate\Http\Request;

class CampBrowserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $camps = Camp::allApproved()->latest()->get();
        return view('camp_browser.index', compact('camps'));
    }
}
