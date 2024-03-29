<?php

namespace App\Http\Controllers;

use App\Camp;
use App\CampCategory;
use App\CampProcedure;
use App\Common;
use App\Program;
use App\Registration;
use App\Organization;
use App\Region;
use App\User;

use App\Http\Controllers\CampApplicationController;
use App\Http\Controllers\CandidateController;

use App\Notifications\NewCampRegistered;

use App\Enums\EducationLevel;

use App\Http\Requests\StoreCampRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;

class CampController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:camp-create', ['only' => ['create', 'store', 'index']]);
        $this->middleware('permission:camp-edit', ['only' => ['edit', 'update', 'attribute_delete']]);
        $this->middleware('permission:camp-delete', ['only' => ['destroy']]);
        $this->middleware('permission:camp-approve', ['only' => ['approve']]);
        $this->middleware('permission:camper-list', ['only' => ['registration']]);
        $this->programs = Common::values(Program::class);
        $this->categories = Common::values(CampCategory::class);
        $this->organizations = null;
        $this->camp_procedures = Common::values(CampProcedure::class);
        $this->regions = Common::values(Region::class);
    }

    public function check(Camp $camp, bool $skip_check = false)
    {
        if (!$skip_check && !$camp->approved) {
            $prevent = true;
            $user = auth()->user();
            if ($user && $user->can('camp-edit'))
                $prevent = !$user->canManageCamp($camp);
            if ($prevent)
                throw new \CampPASSException(trans('camp.ApproveFirst'));
        }
    }

    private function getOrganizationsIfNeeded(bool $no_perm_check = false)
    {
        if (is_null($this->organizations)) {
            $user = auth()->user();
            if ($no_perm_check || ($user && $user->can('organization-list')))
                $this->organizations = Common::values(Organization::class);
            else
                $this->organizations = array(Organization::find($id = $user->organization_id));
        }
        return $this->organizations;
    }

    public function index()
    {
        $camps = auth()->user()->isAdmin() ? Camp::latest() : auth()->user()->getBelongingCamps()->latest();
        $registration_counts = [];
        $total_registrations = 0;
        foreach ($camps->get() as $camp) {
            $registration_counts[$camp->id] = $camp->approved ? $camp->registrations_conditional()->count() : 0;
            $total_registrations += $registration_counts[$camp->id];
        }
        $summary = trans('camp.SummaryText', [
            'total_registrations' => $total_registrations,
            'total_unique_campers' => Registration::distinct()->count('camper_id'),
        ]);
        $camps = $camps->paginate(Common::maxPagination());
        return Common::withPagination(view('camps.index', compact('camps', 'registration_counts', 'summary')));
    }

    public function create()
    {
        $programs = $this->programs;
        $categories = $this->categories;
        $organizations = $this->getOrganizationsIfNeeded();
        $camp_procedures = $this->camp_procedures;
        $regions = $this->regions;
        $education_levels = EducationLevel::getLocalizedConstants('year');
        return view('camps.create', compact('programs', 'categories', 'organizations', 'camp_procedures', 'regions', 'education_levels'));
    }

    public function parseFiles($request, Camp $camp)
    {
        $directory = Common::publicCampDirectory($camp->id);
        foreach (['banner', 'poster', 'parental_consent'] as $filename) {
            if ($request->hasFile($filename)) {
                $name = "{$filename}.{$request->{$filename}->getClientOriginalExtension()}";
                $camp->update([
                    $filename => $name,
                ]);
                Storage::putFileAs($directory, $request->file($filename), $name);
            }
        }
    }

    public function attribute_download(Camp $camp, $name)
    {
        $this->check($camp);
        $directory = Common::publicCampDirectory($camp->id);
        return Common::downloadFile("{$directory}/{$camp->{$name}}");
    }

    public function attribute_delete(Camp $camp, $name)
    {
        $this->check($camp);
        $directory = Common::publicCampDirectory($camp->id);
        return Common::deleteFile("{$directory}/{$camp->{$name}}");
    }

    public function store(StoreCampRequest $request)
    {
        try {
            $user = auth()->user();
            if ($user->isCampMaker()) {
                $request->merge([
                    'organization_id' => $user->organization_id
                ]);
            }
            $camp = Camp::create($request->all());
            if ($user->isAdmin())
                $camp->approve();
            else
                Common::admin()->notify(new NewCampRegistered($camp));
            $this->parseFiles($request, $camp);
        } catch (\Exception $exception) {
            logger()->error($exception);
            throw new \CampPASSExceptionRedirectBack(trans('camp.CampFailedToCreate'));
        }
        $camp_text = Common::getLocalizedName($camp);
        return redirect()->route('camps.index')->with('success', trans('camp.CampCreatedSuccessfully', ['camp' => $camp_text]));
    }

    public function update(StoreCampRequest $request, Camp $camp)
    {
        auth()->user()->canManageCamp($camp);
        $input = $request->except(Camp::$once);
        $camp->update($input);
        $this->parseFiles($request, $camp);
        $camp_text = Common::getLocalizedName($camp);
        return redirect()->back()->with('success', trans('camp.CampUpdatedSuccessfully', ['camp' => $camp_text]));
    }

    public function show(Camp $camp)
    {
        $this->check($camp);
        View::share('object', $camp);
        $category = $camp->camp_category;
        $same_camps = $camp->sameOrganizerCamps();
        return view('camps.show', compact('camp', 'category', 'same_camps'));
    }

    public function registration(Camp $camp)
    {
        $this->check($camp);
        if ($camp->candidate_announced)
            return redirect()->route('qualification.candidate_result', $camp->id);
        View::share('object', $camp);
        if (auth()->user()->can('camper-list')) {
            $registrations = $camp->registrations();
            $total_registrations = $registrations->count();
            $registrations = $registrations->orderBy('registrations.status') // "Group" by registration status
                                ->orderBy('registrations.returned'); // Seperated by whether the form has been returned
            $data = $registrations->orderBy('submission_time')->paginate(Common::maxPagination());
            $has_payment = $camp->paymentOnly() ? true : $camp->candidate_announced && $camp_procedure->deposit_required;
            $has_consent = $camp->parental_consent;
            View::share('has_payment', $has_payment);
            View::share('has_consent', $has_consent);
            View::share('return_reasons', CandidateController::form_returned_reasons($has_payment));
        } else {
            $data = null;
            $total_registrations = 0;
        }
        $category = $camp->camp_category;
        return Common::withPagination(view('camps.registration', compact('camp', 'category', 'data', 'total_registrations')));
    }

    public function edit(Camp $camp)
    {
        auth()->user()->canManageCamp($camp);
        View::share('object', $camp);
        $programs = $this->programs;
        $categories = $this->categories;
        $organizations = $this->getOrganizationsIfNeeded();
        $camp_procedures = $this->camp_procedures;
        $regions = $this->regions;
        $education_levels = EducationLevel::getLocalizedConstants('year');
        return view('camps.edit', compact('programs', 'categories', 'organizations', 'camp_procedures', 'regions', 'education_levels'));
    }

    public static function approve(Camp $camp, bool $silent = false)
    {
        if ($camp->approved)
            return;
        $camp->approve();
        // Notify all camp makers in charge that their camp has been approved
        foreach ($camp->camp_makers() as $campmaker) {
            $campmaker->notify(new NewCampApproved($camp, false));
        }
        // Notify all campers for this newly added camp
        foreach (User::campers()->where('status', 1)->get() as $camper) {
            $camper->notify(new NewCampApproved($camp, true));
        }
        if (!$silent)
            return redirect()->back()->with('success', trans('camp.CampHasBeenApproved', ['camp' => $camp]));
    }

    public function destroy(Camp $camp)
    {
        auth()->user()->canManageCamp($camp);
        Storage::delete(Common::campDirectory($camp->id));
        Storage::delete(Common::publicCampDirectory($camp->id));
        $camp->delete();
        return redirect()->route('camps.index')->with('success', trans('camp.CampDeletedSuccessfully'));
    }

    /**
     * Return the camps (filtered by column-value or neither) as a sort of array.
     *
     */
    public function get_camps($query_pairs = null, bool $categorized = false)
    {
        $camps = Camp::allApproved();
        if ($query_pairs) {
            // Apply search query right away if there are any
            foreach ($query_pairs as $pair) {
                $column = $pair[0];
                $value = $pair[1];
                $method = $pair[2];
                $comparator = isset($pair[3]) ? $pair[3] : null;
                if ($comparator == 'LIKE')
                    $value = "%{$pair[1]}%";
                if ($method == 'whereJsonContains')
                    $camps = $camps->{$method}($column, $value);
                else
                    $camps = $camps->{$method}($column, $comparator ? $comparator : '=', $value);
            }
        }
        if (!$categorized) {
            $result = [];
            $camps->chunk(3, function ($chunk) use (&$result) {
                foreach ($chunk as $camp) {
                    $result[] = $camp;
                }
            });
            return Arr::sort($result, function ($camp) {
                return $camp->app_close_date;
            });
        } else {
            $max_fetch = config('const.camp.max_fetch');
            $output_camps = [];
            $category_ids = [];
            $category_count = CampCategory::count();
            // The maximum number that should not exceed, or we would otherwise do unnecessary work
            $camps = $camps->limit($max_fetch * $category_count);
            $camps->chunk(3, function ($chunk) use (&$max_fetch, &$category_count, &$output_camps, &$category_ids) {
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
            // Sort the camps with their category alphabetically
            ksort($output_camps);
            // Sort the camps in each category by application close date, or remove the category entirely if there is no such camps there
            foreach ($output_camps as $category_name => &$category) {
                if (empty($output_camps[$category_name]))
                    unset($output_camps[$category_name]);
                else
                    $category = Arr::sort($category, function ($camp) {
                        return $camp->app_close_date;
                    });
            }
            return [
                'categorized_camps' => $output_camps,
                'category_ids' => $category_ids,
            ];
        }
    }

    public function browser()
    {
        $query = Input::get('query', null);
        $query_pairs = $query ? [
            [ 'name_en', $query, 'where', 'LIKE', ],
            [ 'name_th', $query, 'orWhere', 'LIKE', ],
        ] : [];
        $education_level = Input::get('education_level', null);
        if ($education_level) {
            $query_pairs[] = [
                'acceptable_education_levels', (int)$education_level, 'whereJsonContains',
            ];
        }
        $region = Input::get('region', null);
        if ($region) {
            $query_pairs[] = [
                'acceptable_regions', (int)$region, 'whereJsonContains',
            ];
        }
        $organization_id = Input::get('organization_id', null);
        if ($organization_id) {
            $query_pairs[] = [
                'organization_id', (int)$organization_id, 'where',
            ];
        }
        $data = $this->get_camps($query_pairs, $categorized = true);
        $categorized_camps = $data['categorized_camps'];
        $category_ids = $data['category_ids'];
        $education_levels = EducationLevel::getLocalizedConstants('year');
        $regions = $this->regions;
        $organizations = $this->getOrganizationsIfNeeded($no_perm_check = true);
        return view('camps.browser', compact('categorized_camps', 'category_ids', 'organizations', 'organization_id', 'education_levels', 'education_level', 'regions', 'region'));
    }

    public function by_category(CampCategory $record)
    {
        $query_pairs = [
            [ 'camp_category_id', $record->id, 'where', ],
        ];
        $education_level = Input::get('education_level', null);
        if ($education_level) {
            $query_pairs[] = [
                'acceptable_education_levels', (int)$education_level, 'whereJsonContains',
            ];
            View::share('education_level', $education_level);
        }
        $region = Input::get('region', null);
        if ($region) {
            $query_pairs[] = [
                'acceptable_regions', (int)$region, 'whereJsonContains',
            ];
            View::share('region', Region::find($region));
        }
        $organization_id = Input::get('organization_id', null);
        if ($organization_id) {
            $query_pairs[] = [
                'organization_id', (int)$organization_id, 'where',
            ];
            View::share('organization_id', Organization::find($organization_id));
        }
        $camps = $this->get_camps($query_pairs);
        return view('camps.by_category', compact('camps', 'record'));
    }

    public function by_organization(Organization $record)
    {
        $data = $this->get_camps($query_pairs = [
            [ 'organization_id', $record->id, 'where', ],
        ], $categorized = true);
        $categorized_camps = $data['categorized_camps'];
        $category_ids = $data['category_ids'];
        return view('camps.by_category', compact('categorized_camps', 'category_ids', 'record'));
    }
}