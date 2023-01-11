<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProviderTiming;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use App\Models\Rating;
use App\Models\BookingService;
use App\Models\Booking;

class RatingController extends Controller
{

    /**
     * Rate and review service provider booking services after complete service
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function rateAndReview(Request $request)
    {
        $user = $request->user();
        $rules = [
            'service_id' => 'required',
            'rating' => 'required',
            'review' => 'sometimes:max:500'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $service = BookingService::where([
            'id' => $request->service_id,
            //'status' => BOOKING_COMPLETE
        ])->first();
        if (! empty($service)) {

            $result = Rating::saveRating($request);

            if (! empty($result)) {
                return returnSuccessResponse("Rating for " . ucwords(@$service->bookedService->name) . " is submitted successfully.");
            } else {
                return returnErrorResponse('unable to save rating and review.');
            }
        } else {
            return returnErrorResponse('Invalid service booking ID.');
        }
    }

    /**
     * Rate and review service provider booking services after complete service
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function rateAndReviewCustomer(Request $request)
    {
        $rules = [
            'order_id' => 'required',
            'rating' => 'required',
            'review' => 'sometimes:max:500'
        ];

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        $booking = Booking::where([
            'id' => $request->order_id
        ])->first();
        if (! empty($booking)) {

            $result = Rating::saveCustomerRating($request);

            if (! empty($result)) {
                return returnSuccessResponse('Your customer rating and reviews  submitted successfully.');
            } else {
                return returnErrorResponse('unable to save rating and review.');
            }
        } else {
            return returnErrorResponse('Invalid service booking ID.');
        }
    }

    /**
     * Get Service provider posts listing
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function reviewList(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'service_id' => 'required',
            'rating' => 'required',
            'review' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        if ($user) {
            $posts = Rating::getReviewList($request);
            return returnSuccessResponse("Post request list", $posts);
        }
        return returnErrorResponse('Unable to get POst request.');
    }
}
