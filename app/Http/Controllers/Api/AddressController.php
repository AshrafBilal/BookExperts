<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Address;
use App\Models\Booking;

class AddressController extends Controller
{

    /**
     * Service provider add address
     *
     * @param Request $request
     * @param Address $address
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function add(Request $request, Address $address)
    {
        $user = $request->user();
        $request->request->add([
            'user_id' => $user->id
        ]);
        $rules = [
            'user_id' => 'required',
            'address1' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'default_address' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        try {

            $address = Address::where('user_id', $user->id)->first();
            $address = ! empty($address) ? $address : new Address();
            $result = $address->fill($request->all());

            if (! $result->save()) {

                return returnErrorResponse('Unable to save');
            }

            return returnSuccessResponse('Address detail added successfully', $result->jsonResponse());
        } catch (\Exception $e) {
            return returnErrorResponse($e->getMessage());
        }
    }

    /**
     * Service provider get address
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function getAddress(Request $request)
    {
        $address = Address::where([
            'user_id' => Auth::id()
        ])->first();

        if (! empty($address)) {
            return returnSuccessResponse('Address details', $address->jsonResponse());
        }
        return returnErrorResponse('Address not found');
    }

    /**
     * Customer add address
     *
     * @param Request $request
     * @param Address $address
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function customerAddAddress(Request $request, Address $address)
    {
        $user = $request->user();
        $request->request->add([
            'user_id' => $user->id
        ]);
        $rules = [
            'user_id' => 'required',
            'address1' => 'required',
            'first_name' => 'sometimes',
            'zip_code' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'street' => 'required',
            'phone_code' => 'required',
            'phone_number' => 'required',
            'country' => 'required',
            'default_address' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        try {
            if (! empty($request->default_address)) {
                Address::where([
                    'user_id' => $user->id
                ])->update([
                    'default_address' => INACTIVE_STATUS
                ]);
            }
            $addedAddress = Address::where([
                'user_id' => $user->id
            ])->count();
            if (empty($addedAddress)) {
                $request->request->add([
                    'default_address' => ACTIVE_STATUS
                ]);
            }

            $address = new Address();
            $result = $address->fill($request->all());

            if (! $result->save()) {
                return returnErrorResponse('Unable to save');
            }

            return returnSuccessResponse('Address detail added successfully', $result->jsonResponse());
        } catch (\Exception $e) {
            return returnErrorResponse($e->getMessage());
        }
    }

    /**
     * Customer update address
     *
     * @param Request $request
     * @param Address $address
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function customerUpdateAddress(Request $request)
    {
        $user = $request->user();
        $request->request->add([
            'user_id' => $user->id
        ]);
        $rules = [
            'address_id' => 'required',
            'user_id' => 'required',
            'address1' => 'required',
            'first_name' => 'required',
            'zip_code' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'street' => 'required',
            'phone_code' => 'required',
            'phone_number' => 'required',
            'country' => 'required',
            'default_address' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        try {
            if (! empty($request->default_address)) {
                Address::where([
                    'user_id' => $user->id
                ])->update([
                    'default_address' => INACTIVE_STATUS
                ]);
            }
            $address = Address::where([
                'id' => $request->address_id,
                'user_id' => $user->id
            ])->first();
            if (! empty($address)) {
                $addedAddress = Address::where([
                    'user_id' => $user->id
                ])->count();
                if ($addedAddress <= ACTIVE_STATUS) {
                    $request->request->add([
                        'default_address' => ACTIVE_STATUS
                    ]);
                }
                $result = $address->fill($request->all());

                if (! $result->save()) {
                    return returnErrorResponse('Unable to save');
                }

                return returnSuccessResponse('Address detail updated successfully', $result->jsonResponse());
            } else {
                return returnErrorResponse("Address not found");
            }
        } catch (\Exception $e) {
            return returnErrorResponse($e->getMessage());
        }
    }

    /**
     * Get Customer address list
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function getCustomerAddressList(Request $request)
    {
        $user = Auth::user();
        $defaultAddress = Address::where([
            'user_id' => $user->id,
            'default_address' => ACTIVE_STATUS
        ])->count();
        if ($defaultAddress > 1) {
            $defaultID = Address::where([
                'user_id' => $user->id,
                'default_address' => ACTIVE_STATUS
            ])->orderBy('updated_at', 'desc')->val('id');
            if (! empty($defaultID)) {
                Address::where([
                    'user_id' => $user->id,
                    'default_address' => ACTIVE_STATUS
                ])->update([
                    'default_address' => INACTIVE_STATUS
                ]);
                Address::where([
                    'id' => $defaultID,
                    'user_id' => $user->id
                ])->update([
                    'default_address' => ACTIVE_STATUS
                ]);
            }
        }

        $addresses = Address::where([
            'user_id' => Auth::id()
        ])->orderBy('default_address', 'asc')
            ->latest()
            ->get();
        $data = [];
        if (! empty($addresses->count())) {
            foreach ($addresses as $address) {
                $data[] = $address->jsonResponse();
            }
            return returnSuccessResponse('Customer Address List', $data);
        }
        return returnSuccessResponse('No Address found', $data, true);
    }

    /**
     * Get customer address
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function getCustomerAddress(Request $request)
    {
        $rules = [
            'address_id' => 'required'
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $address = Address::where([
            'id' => $request->address_id,
            'user_id' => Auth::id()
        ])->first();

        if (! empty($address)) {
            return returnSuccessResponse('Address details', $address->jsonResponse());
        }
        return returnErrorResponse('Address not found');
    }

    /**
     * Delete address
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function deleteAddress(Request $request)
    {
        $user = $request->user();
        $rules = [
            'address_id' => 'required'
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }

       

        $bookingExist = Booking::where('address_id', $request->address_id)->exists();
        if (! empty($bookingExist)) {
            return returnErrorResponse('This address cannot be allowed to delete, because already linked with your past bookings.');
        }

        $addressCount = Address::where([
            'user_id' => $user->id
        ])->count();
        if ($addressCount == ACTIVE_STATUS) {
            Address::where([
                'user_id' => $user->id
            ])->update([
                'default_address' => ACTIVE_STATUS
            ]);
            return returnErrorResponse('Customer default address cannot be allowed to delete.');
        }
        $address = Address::where([
            'id' => $request->address_id
        ])->first();
        if (! empty($address)) {
            $order = Booking::where([
                'id' => $address->id,
                'user_id' => $user->id
            ])->exists();

            if (! empty($order)) {
                return returnErrorResponse('This Address can not be deleted, because already been used in your booked service order.');
            }
            if ($address->delete()) {
                $addresses = Address::where([
                    'user_id' => Auth::id()
                ])->orderBy('default_address', 'asc')
                ->latest()
                ->get();
                $data = [];
                if (! empty($addresses->count())) {
                    foreach ($addresses as $address) {
                        $data[] = $address->jsonResponse();
                    }
                }
                return returnSuccessResponse('Address deleted successfully', $data, true);
            } else {
                return returnErrorResponse('Address not deleted');
            }
        }
        return returnErrorResponse('Address not found');
    }

    /**
     * Customer update address
     *
     * @param Request $request
     * @param Address $address
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function makeDefaultAddress(Request $request)
    {
        $user = $request->user();
        $request->request->add([
            'user_id' => $user->id
        ]);
        $rules = [
            'address_id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        try {

            Address::where([
                'user_id' => $user->id
            ])->update([
                'default_address' => INACTIVE_STATUS
            ]);

            $address = Address::where([
                'id' => $request->address_id,
                'user_id' => $user->id
            ])->first();
            if (! empty($address)) {

                $request->request->add([
                    'default_address' => ACTIVE_STATUS
                ]);

                $result = $address->fill($request->all());

                if (! $result->save()) {
                    return returnErrorResponse('Unable to save');
                }
                $addresses = Address::where([
                    'user_id' => Auth::id()
                ])->orderBy('default_address', 'asc')
                    ->latest()
                    ->get();
                $data = [];
                if (! empty($addresses->count())) {
                    foreach ($addresses as $address) {
                        $data[] = $address->jsonResponse();
                    }
                }
                return returnSuccessResponse('Address set default address successfully', $data,true);
            } else {
                return returnErrorResponse("Address not found");
            }
        } catch (\Exception $e) {
            return returnErrorResponse($e->getMessage());
        }
    }
    
}
