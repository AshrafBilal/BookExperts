<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\Page;

class HomeController extends Controller
{

    public function privacyPolicy()
    {
        $page = Page::where([
            'page_type' => PRIVACY_POLICY
        ])->first();
        return view("common.privacy-policy", compact('page'));
    }

    public function termCondition()
    {
        $page = Page::where([
            'page_type' => TERMS_AND_CONDITION
        ])->first();
        return view("common.term-condition", compact('page'));
    }

    public function aboutUs()
    {
        $page = Page::where([
            'page_type' => ABOUT_US
        ])->first();
        return view("common.about-us", compact('page'));
    }
    
    public function testSocket()
    {
       
        return view("common.test-socket");
    }

}
