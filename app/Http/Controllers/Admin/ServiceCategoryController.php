<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Validator, Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\ServiceCategory;
use App\Models\WorkProfile;
use App\Models\SubServiceCategory;
use App\Models\ProviderOtherCategory;

class ServiceCategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ServiceCategory $category)
    {
        if ($request->ajax()) {
            $categoriesColl = $category->getAllCategories();

            return datatables()->of($categoriesColl)
                ->addIndexColumn()
                ->addColumn('category_type', function ($category) {
                return ($category->category_type == 1) ? 'Normal' : 'Other';
            })
                ->addColumn('created_at', function ($category) {
                return changeTimeZone($category->created_at);
            })
                ->addColumn('action', function ($category) {
                $btn = '';
                $btn = '<a href="' . route('serviceCategory.edit', base64_encode($category->id)) . '" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_category_form"  data-id="' . base64_encode($category->id) . '" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action',
                'image'
            ])
                ->make(true);
        }

        return view('admin.serviceCategory.index');
    }

    public function create(Request $request)
    {
        return view('admin.serviceCategory.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|regex:/^[a-zA-Z\s]+$/|unique:service_categories,name|max:100',
                'name' => "required|max:100|regex:/^[a-zA-Z\s]+$/|unique:service_categories,name,NULL,id,deleted_at,NULL",
                'description' => 'sometimes',
                'category_type' => 'required'
            ], [
                'name.required' => 'The category name field is required.'
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }

            if ($request->category_type == 2) {

                $otherCategory = ServiceCategory::where([
                    'category_type' => $request->category_type
                ])->exists();
                if (! empty($otherCategory)) {
                    return Redirect::back()->with('error', "Only one other category can be added.");
                }
            }
            try {

                $service = new ServiceCategory();
                $service->name = $request->name;
                $service->category_type = $request->category_type;
                if (! empty($service->save())) {
                    return Redirect::route('serviceCategory.index')->with('success', "Service Category added successfully.");
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
        }
        return view('admin.serviceCategory.create');
    }

    public function edit(Request $request, $service_id)
    {
        $service = ServiceCategory::findOrFail(base64_decode($service_id));

        return view("admin.serviceCategory.update", compact('service'));
    }

    public function update(Request $request, $service_id)
    {
        $id = base64_decode($service_id);
        $service = ServiceCategory::findOrFail(base64_decode($service_id));
        if ($request->isMethod('put')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100|regex:/^[a-zA-Z\s]+$/|unique:service_categories,name,NULL,id,deleted_at,NULL' . $service->id,
                'description' => 'sometimes',
                'category_type' => 'required'
            ]);
            DB::beginTransaction();

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }

            if ($request->category_type == 2) {

                $otherCategory = ServiceCategory::where([
                    'category_type' => $request->category_type
                ])->where('id', '!=', $id)->count();
                if ($otherCategory) {
                    return Redirect::back()->with('error', "Only one other category can be added.");
                }
            }

            try {

                $service->name = $request->name;
                $service->description = $request->description;
                $service->category_type = $request->category_type;

                if ($service->save()) {

                    DB::commit();
                    return Redirect::route('serviceCategory.index')->with('success', "Service Category updated successfully.");
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return Redirect::back()->with('error', $e->getMessage());
            }
        }
        return view("admin.serviceCategory.update", compact('service'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productObj = ServiceCategory::find(base64_decode($id));

        if (! $productObj) {
            return returnCustomErrorResponse('This Service Category does not exist');
        }

        if ($productObj->category_type == 2) {
            return returnCustomErrorResponse('Others category cannot be allowed to delete.');
        }

        $workProfile = WorkProfile::where('service_category_id', $productObj->id)->exists();
        if (! empty($workProfile)) {
            $remove = SubServiceCategory::where([
                'service_category_id' => $productObj->id
            ])->pluck('id')->toArray();

            $users = WorkProfile::where([
                'service_category_id' => $productObj->id
            ])->get();
            foreach ($users as $key => $user) {
                $steps = json_decode($user->sub_service_category_id, true);
                $steps = array_diff($steps, $remove);
                $user->sub_service_category_id = json_encode($steps);
                $user->service_category_id = null;
                $user->save();
            }
            ProviderOtherCategory::whereIn('sub_service_category_id', $remove)->delete();
           // return returnCustomErrorResponse('This service cannot be deleted ,because already booked by some service provider.');
        }
        $hasDeleted = $productObj->delete();
        if ($hasDeleted){
            return returnSuccessResponse('Service Category deleted successfully');
        }
        return returnCustomErrorResponse('Something went wrong. Please try again later');
    }
}
