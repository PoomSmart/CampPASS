<?php

namespace App\Http\Controllers;

use App\Camp;
use App\CampCategory;
use App\Organization;

use Illuminate\Http\Request;

class CampBrowserController extends Controller
{
    /**
     * Return the camps (filtered by column-value or neither) as a sort of array.
     * 
     */
    public function get_camps($column = null, $value = null)
    {
        $camps = Camp::allApproved();
        if ($column && $value) {
            $camps = $camps->where($column, $value);
            $result = [];
            $camps->latest()->chunk(3, function ($chunk) {
                foreach ($chunk as $camp) {
                    $result[] = $camp;
                }
            });
            return $result;
        } else {
            $max_fetch = config('const.camp.max_fetch');
            $output_camps = [];
            $category_ids = [];
            $category_count = CampCategory::count();
            $camps->latest()->chunk(3, function ($chunk) use (&$max_fetch, &$category_count, &$output_camps, &$category_ids) {
                foreach ($chunk as $camp) {
                    $category = $camp->camp_category;
                    $category_name = $category->getName();
                    if (!isset($output_camps[$category_name])) {
                        $output_camps[$category_name] = [];
                        $category_ids[$category_name] = $category->id;
                    }
                    if (count($output_camps[$category_name]) < $max_fetch) {
                        $output_camps[$category_name][] = $camp;
                        if (--$category_count == 0)
                            break;
                    }
                }
            });
            return [
                'categorized_camps' => $output_camps,
                'category_ids' => $category_ids,
            ];
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->get_camps();
        $categorized_camps = $data['categorized_camps'];
        $category_ids = $data['category_ids'];
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
