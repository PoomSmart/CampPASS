<?php

namespace App\Http\Controllers;

use App\Common;
use App\Camp;
use App\CampCategory;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $popular_camps = Camp::popularCamps()->get();
        $camp_categories = Common::values(CampCategory::class);
        return view('home', compact('popular_camps', 'camp_categories'));
    }
}
