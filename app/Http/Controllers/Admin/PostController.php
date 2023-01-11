<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use App\Models\File;

class PostController extends Controller
{

    public function index(Request $request, Post $post)
    {
        if ($request->ajax()) {

            $posts = $post->getAllPosts($request);

            return datatables()->of($posts)
                ->addIndexColumn()
                ->
            addColumn('user_id', function ($post) {
                return @$post->user->full_name;
            })
                ->addColumn('file_type', function ($post) {
                return @$post->getFileType();
            })
                ->addColumn('post_type', function ($post) {
                return @$post->getPostType();
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($post) {
                $btn = '';
                $btn = '<a href="' . route('posts.show', base64_encode($post->id)) . '" title="Edit"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($post->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

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

        return view('admin.posts.index');
    }

    public function show(Request $request, $id)
    {
        $post = Post::findOrFail(base64_decode($id));
        $videos = File::where([
            'model_id' => $post->id,
            'model_type' => get_class($post),
            'type_id' => FILE_TYPE_VIDEO
        ])->get();
        $images = File::where([
            'model_id' => $post->id,
            'model_type' => get_class($post),
            'type_id' => FILE_TYPE_IMAGE
        ])->get();
        $songs = File::where([
            'model_id' => $post->id,
            'model_type' => get_class($post),
            'type_id' => FILE_TYPE_AUDIO
        ])->get();
        return view("admin.posts.view", compact('post', 'videos', 'images', 'songs'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $productObj = Post::find(base64_decode($id));

        if (! $productObj) {
            return returnNotFoundResponse('This post does not exist');
        }

        $hasDeleted = $productObj->delete();
        if($hasDeleted){
            return returnSuccessResponse('Post deleted successfully');
        }
        
        return returnErrorResponse('Something went wrong. Please try again later');
    }
    

  
       
}
