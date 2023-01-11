<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\File;
use App\Models\ReportPost;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class ReportedPostController extends Controller
{

    public function index(Request $request, Post $post)
    {
        if ($request->ajax()) {

            $posts = $post->getAllReportedPosts($request);
            return datatables()->of($posts)
                ->addIndexColumn()
                ->addColumn('user_id', function ($post) {
                return @$post->user->full_name;
            })
                ->addColumn('post_url', function ($post) {
                $post_url = '<a href="' . route('posts.show', base64_encode($post->id)) . '" title="Edit" target="_blank"><i class="fas fa-eye mr-1"></i></a>';
                return $post_url;
            })
                ->addColumn('report_count', function ($post) {
                return $post->getTotalReportCount();
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($post) {
                $btn = '';
                $btn = '<a href="' . route('reportedPosts.show', base64_encode($post->id)) . '" title="Details"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($post->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action',
                'post_url'
            ])
                ->make(true);
        }

        return view('admin.reported-posts.index');
    }

    public function reportIndex(Request $request, ReportPost $reportPost, $id)
    {
        if ($request->ajax()) {
            $request->request->add([
                'post_id' => base64_decode($id)
            ]);
            $posts = $reportPost->getAllReportedPosts($request);
            return datatables()->of($posts)
                ->addIndexColumn()
                ->addColumn('user_id', function ($post) {
                return @$post->user->full_name;
            })
                ->addColumn('report_type', function ($post) {
                return @$post->getPostReportType();
            })
                ->rawColumns([
                'action'
            ])
                ->addColumn('action', function ($post) {
                $btn = '';
                $btn = '<a href="' . route('reportedPosts.show', base64_encode($post->id)) . '" title="View"><i class="fas fa-eye mr-1"></i></a>';
                $btn .= '<a href="javascript:void(0);" delete_form="delete_service_provider_form"  data-id="' . base64_encode($post->id) . '" class="delete-datatable-record text-danger delete-service-provider-record" title="Delete"><i class="fas fa-trash ml-1"></i></a>';

                return $btn;
            })
                ->rawColumns([
                'action',
                'post_url'
            ])
                ->make(true);
        }

        return view('admin.reported-posts.index');
    }

    public function show(Request $request, $id)
    {
        $post = Post::findOrFail(base64_decode($id));

        return view("admin.reported-posts.view", compact('post'));
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
        $reportCount = ReportPost::where([
            'post_id' => $productObj->id
        ])->count();
        Notification::sendNotification([
            'sender_id' => Auth::id(),
            'receiver_id' => $productObj->user_id,
            'model' => $productObj,
            'type' => NOTIFICATION_TYPE_REPORT_POST_DELETED_BY_ADMIN,
            'message' => "Your post ID " . $productObj->id . " deleted by admin because its reported by " . $reportCount . " customers"
        ]);
        $hasDeleted = $productObj->delete();
        if ($hasDeleted) {
            return returnSuccessResponse('Post deleted successfully');
        }

        return returnErrorResponse('Something went wrong. Please try again later');
    }
    

  
       
}
