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
        $categorized_camps = [];
        foreach (Camp::allApproved()->latest()->get() as $camp) {
            $category = $camp->camp_category()->name;
            if (!isset($categorized_camps[$category]))
                $categorized_camps[$category] = [];
            $categorized_camps[$category][] = $camp;
        }
        return view('camp_browser.index', compact('categorized_camps'));
    }
}
