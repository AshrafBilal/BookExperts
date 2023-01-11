<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Validator, Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\ServiceCategory;
use App\Models\SubServiceCategory;
use App\Models\WorkProfile;
use App\Models\ProviderOtherCategory;

class SubServiceCategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, SubServiceCategory $category)
    {
        if ($request->ajax()) {
            $categoriesColl = $category->getAllCategories();

            return datatables()->of($categoriesColl)
                ->addIndexColumn()
                ->addColumn('service_name', function ($category) {
                return (@$category->serviceCategory->name);
            })
                ->addColumn('created_at', function ($category) {
                return changeTimeZone($category->created_at);
            })
                ->addColumn('action', function ($category) {
                $btn = '';
                $btn = '<a href="' . route('subServiceCategory.edit', base64_encode($category->id)) . '" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" data-id="' . base64_encode($category->id) . '" delete_form="delete_sub_service_category_form" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action',
                'image'
            ])
                ->make(true);
        }

        return view('admin.subServiceCategory.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $categories = ServiceCategory::latest()->pluck('name', 'id');
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => "required|max:100|regex:/^[a-zA-Z\s]+$/|unique:sub_service_categories,name,NULL,id,deleted_at,NULL,service_category_id," . $request->service_category_id,
                'description' => 'sometimes',
                'service_category_id' => 'required|exists:service_categories,id',
                'file_path' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5000'
            ], [
                'name.required' => 'The category name field is required.',
                'service_category_id.exists' => 'Invalid Service Category ID.',
                'service_category_id.required' => 'The service category type field is required.',
                'file_path.required' => 'The service category image field is required.',
                'file_path.max' => 'The Category Image must not be greater than 5 MB.'
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            try {
                $service = new SubServiceCategory();
                $service->name = $request->name;
                $service->service_category_id = $request->service_category_id;
                $service->description = $request->description;
                if ($request->hasFile('file_path')) {
                    $service->file_path = saveUploadedFile($request->file_path);
                }
                if (! empty($service->save())) {
                    return Redirect::route('subServiceCategory.index')->with('success', "Sub Service Category added successfully.");
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
        }
        return view('admin.subServiceCategory.create', compact('categories'));
    }

    public function edit(Request $request, $service_id)
    {
        $service = SubServiceCategory::findOrFail(base64_decode($service_id));
        $categories = ServiceCategory::pluck('name', 'id');
        return view("admin.subServiceCategory.update", compact('service', 'categories'));
    }

    public function create(Request $request)
    {
        $categories = ServiceCategory::latest()->pluck('name', 'id');

        return view('admin.subServiceCategory.create', compact('categories'));
    }

    public function update(Request $request, $service_id)
    {
        $service = SubServiceCategory::findOrFail(base64_decode($service_id));
        $categories = ServiceCategory::pluck('name', 'id');
        if ($request->isMethod('put')) {

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:100|regex:/^[a-zA-Z\s]+$/|unique:sub_service_categories,name,NULL,id,deleted_at,NULL' . $service->id . '|unique:sub_service_categories,service_category_id,' . $service->id,
                'description' => 'sometimes',
                'service_category_id' => 'required|exists:service_categories,id',
                'file_path' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:5000'
            ], [
                'name.required' => 'The category name field is required.',
                'service_category_id.exists' => 'Invalid Service Category ID.',
                'service_category_id.required' => 'The service category type field is required.',
                'file_path.required' => 'The service category image field is required.',
                'file_path.max' => 'The Category Image must not be greater than 5 MB.'
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            try {

                $service->name = $request->name;
                $service->service_category_id = $request->service_category_id;
                $service->description = $request->description;
                if ($request->hasFile('file_path')) {
                    $service->unlinkFiles();
                    $service->file_path = saveUploadedFile($request->file_path);
                }
                if ($service->save()) {
                    return Redirect::route('subServiceCategory.index')->with('success', "Sub Service Category updated successfully.");
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
        }
        return view("admin.subServiceCategory.update", compact('service', 'categories'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productObj = SubServiceCategory::find(base64_decode($id));

        if (! $productObj) {
            return returnNotFoundResponse('This Sub Service Category does not exist');
        }
        $workProfile = WorkProfile::where('service_category_id', $productObj->service_category_id)->exists();
        if (! empty($workProfile)) {
            $remove = SubServiceCategory::where([
                'service_category_id' => $productObj->service_category_id
            ])->pluck('id')->toArray();

            $users = WorkProfile::where([
                'service_category_id' => $productObj->service_category_id
            ])->get();
            foreach ($users as $key => $user) {
                $steps = json_decode($user->sub_service_category_id, true);
                $steps = array_diff($steps, $remove);
                $user->sub_service_category_id = json_encode($steps);
                $user->service_category_id = null;
                $user->save();
            }
            ProviderOtherCategory::whereIn('sub_service_category_id', $remove)->delete();
        }
        $hasDeleted = $productObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Sub Service Category deleted successfully');
        }
        
        return returnErrorResponse('Something went wrong. Please try again later');
    }
    
}
