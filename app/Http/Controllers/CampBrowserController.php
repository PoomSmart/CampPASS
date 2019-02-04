<?php

namespace App\Http\Controllers;

use App\Camp;
use App\CampCategory;
use App\Organization;

use Illuminate\Http\Request;

class CampBrowserController extends Controller
{
    protected $category_ids = [];

    public function get_camps($column = null, $value = null)
    {
        $max_fetch = config('const.camp.max_fetch');
        $camps = Camp::allApproved();
        if ($column && $value) {
            $camps = $camps->where($column, $value);
            return $camps->latest()->get();
        }
        $output_camps = [];
        $category_count = CampCategory::count();
        foreach ($camps->latest()->get() as $camp) {
            $category = $camp->camp_category();
            $category_name = $category->name;
            if (!isset($output_camps[$category_name])) {
                $output_camps[$category_name] = [];
                $this->category_ids[$category_name] = $category->id;
            }
            if (count($output_camps[$category_name]) < $max_fetch) {
                $output_camps[$category_name][] = $camp;
                if (--$category_count == 0)
                    break;
            }
        }
        return $output_camps;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categorized_camps = $this->get_camps();
        $category_ids = $this->category_ids;
        return view('camp_browser.index', compact('categorized_camps', 'category_ids'));
    }

    public function by_category(CampCategory $record)
    {
        $camps = $this->get_camps('camp_category_id', $record->id);
        return view('camp_browser.by_category', compact('camps', 'record'));
    }

    public function by_organization(Organization $record)
    {
        $camps = $this->get_camps('organization_id', $record->id);
        return view('camp_browser.by_category', compact('camps', 'record'));
    }
}
