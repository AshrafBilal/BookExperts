<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProviderTiming;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProviderTimingController extends Controller
{

    /**
     * Add Provider Availability Timing
     *
     * @param Request $request
     * @param Service $service
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function add(Request $request, ProviderTiming $providerTiming)
    {
        // return returnSuccessResponse('Post Data',[$request->all()]);
        DB::beginTransaction();
        try {
            $user = $request->user();
            $request->request->add([
                'user_id' => $user->id
            ]);
            $days = $request->days;
            if (is_array($days)) {
                foreach ($days as $selectDay => $dayData) {
                    if (! empty($dayData['full_day'])) {
                        if ($dayData['full_day'] == OPEN_SELECT_SLOTS) {
                            $timings = @$dayData['time'];
                            if (is_array($timings)) {
                                ProviderTiming::where([
                                    'day' => $selectDay,
                                    'user_id' => $user->id
                                ])->delete();

                                foreach ($timings as $day => $time) {
                                    $timeArray = explode('to', $time);
                                    $start_time = @$timeArray[0];
                                    $end_time = @$timeArray[1];
                                    $model = new ProviderTiming();
                                    $model->day = $selectDay;
                                    $model->off_day_type = $dayData['full_day'];
                                    $model->start_time = $start_time;
                                    $model->end_time = $end_time;
                                    $model->user_id = $user->id;
                                    $model->save();
                                }
                            }
                        } else if ($dayData['full_day'] == ON_LEAVE) {
                            ProviderTiming::where([
                                'day' => $selectDay,
                                'user_id' => $user->id
                            ])->delete();
                            $model = new ProviderTiming();
                            $model->day = $selectDay;
                            $model->off_day_type = $dayData['full_day'];
                            $model->start_time = null;
                            $model->end_time = null;
                            $model->user_id = $user->id;
                            $model->save();
                        } else {

                            ProviderTiming::where([
                                'day' => $selectDay,
                                'user_id' => $user->id
                            ])->delete();
                            $model = new ProviderTiming();
                            $model->day = $selectDay;
                            $model->off_day_type = $dayData['full_day'];
                            $model->start_time = null;
                            $model->end_time = null;
                            $model->user_id = $user->id;
                            $model->save();
                        }
                    }
                }

                DB::commit();
                return returnSuccessResponse('Availability Timing added successfully');
            } else {
                return returnErrorResponse("Invalid json data format.");
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            return returnErrorResponse($ex->getMessage());
        }
    }

    public function getProviderTiming(Request $request)
    {
        
		 $validator = Validator::make($request->all(), [
            'address1' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'street' => 'required',
            'city' => 'required',
            'zip_code' => 'required',
            'default_address' => 'required',
            'address_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        if (! empty($request->query('provider_id')) || ! empty($request->query('date'))) {

            $response = ProviderTiming::getProviderTiming($request->query('provider_id'), $request->query('date'));
        } else {
            $response = ProviderTiming::getProviderTiming(Auth::id(), $request->query('date'));
        }
        return returnSuccessResponse('Data sent successfully', $response);
    }

    public function getIndividualProviderTiming(Request $request)
    {
        $rules = [
            'provider_id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessage));
        }
        $response = ProviderTiming::getIndividualProviderTiming($request->query('provider_id'));
        return returnSuccessResponse('Data sent successfully', $response);
    }

    public function deleteProviderTiming(Request $request)
    {
        $user = $request->user();
        $rules = [
            'id' => 'required'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessage));
        }
        $providerTiming = ProviderTiming::where([
            'id' => $request->id,
            'user_id' => $user->id
        ])->first();
        if (! empty($providerTiming)) {
            if ($providerTiming->delete()) {
                $response = ProviderTiming::getProviderTiming();
                return returnSuccessResponse('Availability timing deleted successfully', $response);
            }
            return returnErrorResponse('Unable to delete Availability timing.');
        }
        return returnErrorResponse('Availability timing not found.');
    }
    
   
}
