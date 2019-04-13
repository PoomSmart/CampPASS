<?php

namespace App\Http\Controllers;

use App\Common;
use App\Camp;
use App\CampCategory;
use App\Organization;

use App\Enums\OrganizationType;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $popular_camps = Camp::popularCamps()->get()->all();
        $camp_categories = Common::values(CampCategory::class);
        $university_categories = Common::values(Organization::class, $column = 'type', $value = OrganizationType::UNIVERSITY, $group = 'image');
        return view('home', compact('popular_camps', 'camp_categories', 'university_categories'));
    }
}