<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth, Validator, Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\Booking;
use App\Models\BookingService;

class ServiceController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Service $service)
    {
        if ($request->ajax()) {
            $categoriesColl = $service->getAllServices();

            return datatables()->of($categoriesColl)
                ->addIndexColumn()
                ->addColumn('action', function ($service) {
                $btn = '';
                // $btn = '<a href="' . route('service.edit', base64_encode($service->id)) . '" title="Edit"><i class="fas fa-edit mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_category_form"  data-id="' . base64_encode($service->id) . '" class="text-danger delete-datatable-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->addColumn('service_id', function ($category) {
                return (@$category->subServiceCategory->name);
            })
                ->addColumn('service_visit', function ($category) {
                    return (@$category->getServiceType());
            })
                ->addColumn('user_id', function ($category) {
                return (@$category->user->full_name);
            })
                ->addColumn('service_provider_id', function ($category) {
                return (@$category->serviceProvider->full_name);
            })
                ->rawColumns([
                'action',
                'image'
            ])
                ->make(true);
        }

        return view('admin.services.index');
    }

    public function create(Request $request)
    {
        return view('admin.services.create');
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
                'name' => 'required|unique:service_categories,name|max:100|regex:/^[a-zA-Z\s]+$/',
                'description' => 'sometimes'
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            try {

                $service = new ServiceCategory();
                $service->name = $request->name;
                $service->description = $request->description;
                if (! empty($service->save())) {
                    return Redirect::route('service.index')->with('success', "Service added successfully.");
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
        }
        return view('admin.services.create');
    }

    public function edit(Request $request, $service_id)
    {
        $service = ServiceCategory::findOrFail(base64_decode($service_id));

        return view("admin.services.update", compact('service'));
    }

    public function update(Request $request, $service_id)
    {
        $service = ServiceCategory::findOrFail(base64_decode($service_id));
        if ($request->isMethod('put')) {
            $validator = Validator::make($request->all(), [
                'name' => "required|unique:service_categories,name,{$service->id}|max:100|regex:/^[a-zA-Z\s]+$/",
                'description' => 'sometimes'
            ]);

            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
            }
            try {

                $service->name = $request->name;
                $service->description = $request->description;

                if ($service->save()) {
                    return Redirect::route('service.index')->with('success', "Service updated successfully.");
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
        }
        return view("admin.services.update", compact('service'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service = Service::find(base64_decode($id));

        if (! $service) {
            return returnNotFoundResponse('This Service does not exist');
        }
        $booking = BookingService::where('service_id', $service->id)->exists();
        if (! empty($booking)) {
            return returnCustomErrorResponse('This service cannot be deleted ,because already booked by some customers.');
        }
        $hasDeleted = $service->delete();
        if ($hasDeleted){
            return returnSuccessResponse('Service deleted successfully');
        }
        
        return returnErrorResponse('Something went wrong. Please try again later');
    }
}
