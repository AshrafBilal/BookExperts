<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Booking;

class BookingController extends Controller
{

    public function index(Request $request, Booking $Booking)
    {
        if ($request->ajax()) {

            $Bookings = $Booking->getAllBookings($request);

            return datatables()->of($Bookings)
                ->addIndexColumn()
                ->addColumn('user_id', function ($Booking) {
                return @$Booking->user->full_name;
            })
            ->addColumn('booking_date_time', function ($category) {
                return changeTimeZone($category->booking_date_time);
            })
                ->addColumn('status', function ($Booking) {
                return @$Booking->getBookingStatus();
            })
            ->addColumn('is_live_booking', function ($Booking) {
                return !empty($Booking->is_live_booking)?'Yes':'No';
            })
                ->addColumn('booking_type', function ($Booking) {
                return @$Booking->getBookingType();
            })
            
            ->addColumn('payment_method', function ($Booking) {
                return @$Booking->getPaymentMethod();
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($Booking) {
                $btn = '';
                $btn = '<a href="' . route('bookings.show', base64_encode($Booking->id)) . '" title="Edit"><i class="fas fa-eye mr-1"></i></a>';
                //$btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($Booking->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action'
            ])
                ->make(true);
        }

        return view('admin.bookings.index');
    }

    public function show(Request $request, $id)
    {
        $booking = Booking::findOrFail(base64_decode($id));
        return view("admin.bookings.view", compact('booking'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productObj = Booking::find(base64_decode($id));

        if (! $productObj) {
            return returnNotFoundResponse('This Booking does not exist');
        }
        if (in_array($productObj->status, [
            BOOKING_REJECT,
            BOOKING_CANCEL,
            BOOKING_PENDING
        ])) {
            $hasDeleted = $productObj->delete();
            if ($hasDeleted) {
                return returnSuccessResponse('Booking deleted successfully');
            }
        } else {
            return returnCustomErrorResponse('Only pending,rejected and cancelled booking allowed to delete.');
        }
        
        return returnCustomErrorResponse('Something went wrong. Please try again later');
    }
    

  
       
}
