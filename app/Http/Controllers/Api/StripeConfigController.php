<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Stripe\Stripe;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StripeConfigController extends Controller
{

    private $stripe;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    }

    public function connectWithStripe(Request $request)
    {
        Log::error("callback url success");
        $code = $request->input('code');
        $clientSecret = Config::get('services.stripe.secret');
        if (empty($code)) {
            return returnNotFoundResponse("please send code.");
        }
        Stripe::setApiKey($clientSecret);
        try {
            $response = \Stripe\OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $code
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            // Since it's a decline, \Stripe\Exception\CardException will be caught
            return returnErrorResponse($e->getMessage());
        } catch (\Stripe\Exception\RateLimitException $e) {
            return returnErrorResponse($e->getMessage());
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return returnErrorResponse($e->getMessage());
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            return returnErrorResponse($e->getMessage());
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
            return returnErrorResponse($e->getMessage());
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return returnErrorResponse($e->getMessage());
        } catch (\Exception $e) {
            return returnErrorResponse($e->getMessage());
            // Something else happened, completely unrelated to Stripe
        }

        $user = User::find(base64_decode($request->input('state')));
        if (empty($user)) {
            return returnNotFoundResponse("NotFoundResponse.");
        }
        if (! empty($response->stripe_user_id)) {

            $update = User::where('id', $user->id)->update([
                'stripe_connect_id' => $response->stripe_user_id
            ]);
            if ($update) {
                return true;
            }
        } else {
            return returnNotFoundResponse("Something Went Wrong.");
        }
    }

    public function stripeData()
    {
        $stripeKey = Config::get('services.stripe.key');
        $secretId = Config::get('services.stripe.secret');
        $clientId = Config::get('services.stripe.client_id');
        $redirect_url = url("connect-with-stripe");
        $user_id = base64_encode(Auth::id());
        $data['stripe_connect_url'] = "https://connect.stripe.com/oauth/authorize?response_type=code&client_id=$clientId&amp;&scope=read_write&redirect_uri=$redirect_url&state=$user_id";
        $data['stripe_key'] = $stripeKey;
        $data['secret_id'] = $secretId;
        $data['client_id'] = $clientId;
        return returnSuccessResponse('Data sent successfully', $data);
    }

    public function disconnectWithStripe(Request $request)
    {
        $userObj = $request->user();
        if (! $userObj) {
            return notAuthorizedResponse('User is not authorized');
        }
        $hasUpdate = $userObj->updateUser($userObj->id, [
            'stripe_connect_id' => null
        ]);
        if ($hasUpdate) {
            return true;
        } else {
            return returnErrorResponse("Sorry, Your account is not disconnect.");
        }
    }

    public function stripeToken($inputArr)
    {

        // Create Stripe Token
        $token = $this->stripe->tokens->create([
            "card" => [
                "number" => $inputArr['card_number'],
                "exp_month" => $inputArr['exp_month'],
                "exp_year" => $inputArr['exp_year'],
                "cvc" => $inputArr['cvc'],
                "name" => $inputArr['name']
            ]
        ]);

        return $token['id'];
    }

    public function addCard(Request $request)
    {
        $user = auth()->user();
        if ($user->role_id != NORMAL_USER_TYPE) {
            return returnErrorResponse("User is not authenticated.");
        }

        $validator = Validator::make($request->all(), [
            'card_number' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'cvc' => 'required',
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }

        $input = $request->all();

        try {
            $card_token = $this->stripeToken($input);
            if (! $user->stripe_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->getFullName(),
                    'phone' => $user->phone_number,
                    'description' => $user->about,
                    'address' => $user->address
                ]);
                $user->stripe_id = $customer['id'];
            } else {
                \Stripe\Customer::update($user->stripe_id, [
                    'email' => $user->email,
                    'name' => $user->getFullName(),
                    'phone' => $user->phone_number,
                    'description' => $user->about,
                    'address' => $user->address
                ]);
            }

            // tok_visa is the token which will generate in client side
            $stripeCard = \Stripe\Customer::createSource($user->stripe_id, [
                'source' => $card_token
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            return returnErrorResponse($e->getMessage());
        } catch (\Stripe\Exception\RateLimitException $e) {
            // Too many requests made to the API too quickly
            return returnErrorResponse($e->getMessage());
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid parameters were supplied to Stripe's API
            return returnErrorResponse($e->getMessage());
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            return returnErrorResponse($e->getMessage());
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
            return returnErrorResponse($e->getMessage());
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            return returnErrorResponse($e->getMessage());
        } catch (\Exception $ex) {
            return returnErrorResponse($ex->getMessage());
        }

        if (! empty($stripeCard['id'])) {
            $userCardObj = $user->save();
            if ($userCardObj) {
                return returnSuccessResponse("Your card has been added successfully.", [
                    'card_id' => $stripeCard['id']
                ]);
            } else {
                return returnErrorResponse("Unable to add card. Please try again later.");
            }
        } else {
            return returnErrorResponse("Unable to add card. Please try again later.");
        }
    }

    public function cardListing()
    {
        $user = auth()->user();

        if ($user->role_id != NORMAL_USER_TYPE) {
            return returnErrorResponse("User is not authenticated.");
        }

        if (! $user->stripe_id) {
            return returnSuccessResponse("No card found of the user.");
        }
        try {
            $userDetails = \Stripe\Customer::retrieve($user->stripe_id, []);
        } catch (\Exception $ex) {
            return returnErrorResponse($ex->getMessage());
        }
        try {
            $userCards = \Stripe\Customer::allSources($user->stripe_id, [
                'object' => 'card'
            ]);
        } catch (\Exception $ex) {
            return returnErrorResponse($ex->getMessage());
        }

        $userCards = array(
            'userDetails' => $userDetails,
            'cardsDetails' => $userCards->data
        );

        return returnSuccessResponse("Card Listing", $userCards);
    }

    public function deleteCard(Request $request)
    {
        $user = auth()->user();

        if ($user->role_id != NORMAL_USER_TYPE) {
            return returnErrorResponse("User is not authenticated.");
        }
        $validator = Validator::make($request->all(), [
            'card_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        if (! $user->stripe_id) {
            return returnSuccessResponse("No card found of the user.");
        }

        try {
            $hasDeleted = \Stripe\Customer::deleteSource($user->stripe_id, $request->input('card_id'));
        } catch (\Exception $ex) {
            return returnErrorResponse($ex->getMessage());
        }

        if ($hasDeleted) {

            try {
                $userDetails = \Stripe\Customer::retrieve($user->stripe_id, []);
            } catch (\Exception $ex) {
                return returnErrorResponse($ex->getMessage());
            }
            try {
                $userCards = \Stripe\Customer::allSources($user->stripe_id, [
                    'object' => 'card'
                ]);
            } catch (\Exception $ex) {
                return returnErrorResponse($ex->getMessage());
            }

            $userCards = array(
                'userDetails' => $userDetails,
                'cardsDetails' => $userCards->data
            );

            return returnSuccessResponse("Selected card deleted successfully.", $userCards, true);
        }

        return returnSuccessResponse("Unable to delete card.");
    }

    public function setDefaultCard(Request $request)
    {
        $user = auth()->user();

        if ($user->role_id != NORMAL_USER_TYPE) {
            return returnErrorResponse("User is not authenticated.");
        }
        $validator = Validator::make($request->all(), [
            'card_id' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }

        if (! $user->stripe_id) {
            return returnSuccessResponse("No card found of the user.");
        }

        try {
            \Stripe\Customer::update($user->stripe_id, [
                'default_source' => $request->input('card_id')
            ]);
            $hasUpdated = $user->save();
        } catch (\Exception $ex) {
            return returnErrorResponse($ex->getMessage());
        }

        if ($hasUpdated) {
            try {
                $userDetails = \Stripe\Customer::retrieve($user->stripe_id, []);
            } catch (\Exception $ex) {
                return returnErrorResponse($ex->getMessage());
            }
            try {
                $userCards = \Stripe\Customer::allSources($user->stripe_id, [
                    'object' => 'card'
                ]);
            } catch (\Exception $ex) {
                return returnErrorResponse($ex->getMessage());
            }
            
            $userCards = array(
                'userDetails' => $userDetails,
                'cardsDetails' => $userCards->data
            );
            return returnSuccessResponse("Selected card set as default card successfully.",$userCards,true);
        }
        return returnSuccessResponse("Unable to delete card.");
    }
}
