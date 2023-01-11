<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Models\Page;

class PageController extends Controller
{
    
    
    
    public function index(Request $request)
    {
        $pages = Page::latest()->get();
        return view("admin.pages.index", compact('pages'));
    }
    
    public function addPage(Request $request)
    {
        
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|unique:pages,title|max:100|regex:/^[a-zA-Z0-9\s]+$/',
                'description'=>'required|sometimes',
                'page_type' => 'required|unique:pages,type_id|max:100|regex:/^[a-zA-Z0-9\s]+$/',
                
            ]);
            
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
                
            }
            try {
                
                $page = new Page();
                $page->title  = $request->title;
                $page->type_id  = $request->page_type;
                $page->description  = $request->description;
                if(!empty($page->save())){
                    return Redirect::route('pages')->with('success', "Page added successfully.");
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
        }
        return view("admin.pages.add-page");
    }
    
    public function updatePage(Request $request,$id)
    {
        
        $page = Page::findOrFail(base64_decode($id));
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'title' => "required|unique:pages,title,{$page->id}|max:100|regex:/^[a-zA-Z0-9\s]+$/",
                'description'=>'required',
                'page_type' => "required|unique:pages,type_id,{$page->id}|max:100|regex:/^[a-zA-Z0-9\s]+$/",
                
            ]);
            
            if ($validator->fails()) {
                return Redirect::back()->withInput()->withErrors($validator);
                
            }
            try {
                
                $page->title  = $request->title;
                $page->description  = $request->description;
                $page->type_id  = $request->page_type;
                
                if($page->save()){
                    return Redirect::route('pages')->with('success', "Page updated successfully.");
                }
            } catch (\Exception $e) {
                return Redirect::back()->with('error', $e->getMessage());
            }
        }
        return view("admin.pages.update-page",compact('page'));
    }
    
    public function pageDetails(Request $request,$id)
    {
        
        $page = Page::findOrFail(base64_decode($id));
        return view("admin.pages.page-detail",compact('page'));
    }
    
    public function deletePage(Request $request)
    {
        if ($request->isMethod('delete')) {
            $page = Page::find($request->page_id);
            if (! empty($page)) {
                $page->delete();
                return Redirect::back()->with('success', "Page deleted successfully.");
            }
        }
        return Redirect::back()->with('error', "Page not deleted.");
        
    }
    
    
}
