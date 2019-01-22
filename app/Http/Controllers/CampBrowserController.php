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
        $max = config('const.app.max_paginate');
        $camps = Camp::latest();
        $camps = $camps->paginate($max);
        return view('camp_browser.index', compact('camps'))->with('i', (request()->input('page', 1) - 1) * $max);
    }
}
