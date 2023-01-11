<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactUs;
use Illuminate\Support\Facades\Redirect;
use Validator;
use App\Notifications\SendContactUsEmail;
use App\Models\Page;

class SiteController extends Controller
{

    public function privacyPolicy(Request $request)
    {
        $page = Page::where([
            'type_id' => PAGE_TYPE_PRIVACY_POLICY
        ])->first();
        return view('site.privacy', compact('page'));
    }

    public function termCondition(Request $request)
    {
        $page = Page::where([
            'type_id' => PAGE_TYPE_TERMS_AND_CONDITION
        ])->first();
        return view('site.terms', compact('page'));
    }

    public function aboutUs(Request $request)
    {
        $page = Page::where([
            'type_id' => PAGE_TYPE_ABOUT_US
        ])->first();
        return view('site.about-us', compact('page'));
    }

    public function contactUs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        $model = new ContactUs();
        $model = $model->fill($request->all());
        if ($model->save()) {
            $userEmail = $model->email;
            $model->email = "Ahmed@justsaywhat.com";
            try {
                $model->notify(new SendContactUsEmail([
                    'userEmail' => $userEmail
                ]));
            } catch (\Exception $ex) {
                \Log::error($ex);
            }

            return Redirect::back()->with('success', "message sent to Admin.");

        }

        return Redirect::back()->with('error', "Something went wrong.");



    }
}
