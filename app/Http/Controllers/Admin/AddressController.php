<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Address;
use App\Models\Booking;

class AddressController extends Controller
{

    public function index(Request $request, Address $Address)
    {
        if ($request->ajax()) {

            $Addresss = $Address->getAllAddress($request);
            return datatables()->of($Addresss)
                ->addIndexColumn()
            ->addColumn('phone_number', function ($Address) {
                return $Address->phone_code.$Address->phone_number;
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($Address) {
                $btn = '';
                $btn = '<a href="' . route('address.show', base64_encode($Address->id)) . '" title="Edit"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($Address->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action',
                'active_status',
                'profile_verified',
                'profile_status_action'
            ])
                ->make(true);
        }

        return view('admin.address.index');
    }

    public function show(Request $request, $id)
    {
        $address = Address::findOrFail(base64_decode($id));
        return view("admin.address.view", compact('address'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $object = Address::find(base64_decode($id));

        if (! $object) {
            return returnNotFoundResponse('This Address does not exist');
        }
        $exist = Booking::where('address_id', $object->id)->exists();
        if (! empty($exist)) {
            return returnCustomErrorResponse('This Address cannot be allowed to delete, because already linked with customer booking.');
        }
        $hasDeleted = $object->delete();
        if($hasDeleted){
            return returnSuccessResponse('Address deleted successfully');
        }
        
        return returnErrorResponse('Something went wrong. Please try again later');
    }
    

  
       
}
