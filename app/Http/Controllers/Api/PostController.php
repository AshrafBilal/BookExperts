<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\File;

class PostController extends Controller
{

    /**
     * Add post
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function addPost(Request $request)
    {
        $user = $request->user();
        if (! empty($request->post_id)) {
            $rules = [
                'post_id' => 'required|exists:posts,id,deleted_at,NULL',
                'description' => 'sometimes|max:500'
            ];
        } else {
            $rules = [
                'post_type' => 'required',
                'file' => 'required',
                'description' => 'sometimes|max:500'
            ];
        }
        $user = Auth::user();
        $request->request->add([
            'user_id' => $user->id
        ]);

        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages[0]));
        }
        try {
            
            DB::beginTransaction();
            $post = Post::find($request->post_id);
            $post = ! empty($post) ? $post : new Post();
            $post->fill($request->all());
            if ($post->save()) {
                if (! empty($request->audio_file)) {
                    $file = new File();
                    $file->original_name = $request->audio_file;
                    $file->model_id = $post->id;
                    $file->model_type = get_class($post);
                    $file->type_id = FILE_TYPE_AUDIO;
                    $file->user_id = $post->user_id;
                    $file->file = $request->audio_file;
                    $file->save();
                }

                if ($request->hasFile('file')) {
                    saveMultipleFiles($request, $post, 'posts');
                }
                if (! empty($request->post_id)) {
                    $message = ($request->post_type == POST_TYPE_STATUS_STORY) ? 'Story updated successfully.' : 'Post updated successfully.';
                } else {
                    $message = ($request->post_type == POST_TYPE_STATUS_STORY) ? 'Story added successfully.' : 'Post added successfully.';
                }
                DB::commit();
                return returnSuccessResponse($message);
            } else {
                return returnErrorResponse('Post not saved.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return returnErrorResponse($e->getMessage());
        }
    }

    /**
     * Delete post
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function deletePost(Request $request)
    {
        $rules = [
            'post_id' => 'required'
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $post = Post::where([
            'id' => $request->post_id,
            'user_id' => Auth::id()
        ])->first();
        if (! empty($post)) {
            if ($post->delete()) {
                return returnSuccessResponse('Post deleted successfully');
            } else {
                return returnErrorResponse('Post not deleted');
            }
        }
        return returnErrorResponse('Post not found');
    }

    /**
     * Get Service provider posts listing
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function myPosts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'tip_amount' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $posts = Post::getServiceProviderPostList($request);
        return returnSuccessResponse("Post request list", $posts);
    }

    /**
     * Get Service provider story details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function storyDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required',
            'tip_amount' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $posts = Post::getServiceProviderStoryDetails($request);
        return returnSuccessResponse("Story details", $posts, true);
    }

    /**
     * Customer like dislike service provider Post
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function likeDislikePost(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id,deleted_at,NULL',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        try {
            DB::beginTransaction();

            $request->request->add([
                'user_id' => Auth::id()
            ]);
            $Post = Like::where([
                'user_id' => $request->user_id,
                'post_id' => $request->post_id
            ])->first();
            if (! $Post) {
                $Post = new Like();
            }
            $Post->user_id = $request->user_id;
            $Post->post_id = $request->post_id;
            $Post->state_id = $request->status;
            $Post->created_at = date('Y-m-d H:i:s');
            $Post->save();
            if (empty($Post->post->user_id)) {
                return returnErrorResponse("Invalid post id.");
            }
            if ($request->status == ACTIVE_STATUS) {
                Notification::sendNotification([
                    'sender_id' => $user->id,
                    'receiver_id' => $Post->post->user_id,
                    'model' => $Post,
                    'type' => NOTIFICATION_TYPE_CUSTOMER_LIKE_POST,
                    'message' => ucwords($user->getFullName()) . ' like your post.'
                ]);
            }
            DB::commit();
            $message = ($request->status == 1) ? "Post liked successfully." : "Post unliked successfully.";
            return returnSuccessResponse($message);
        } catch (\Exception $e) {
            DB::rollback();
            return returnErrorResponse($e->getMessage());
        }
    }

    public function postLikes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id,deleted_at,NULL',
            'booking_id' => 'required',
            'tip_amount' => 'required'

        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }

        $serviceIds = Like::where([
            'state_id' => ACTIVE_STATUS,
            'post_id' => $request->post_id
        ])->join('users', 'likes.user_id', 'users.id')
            ->whereNull('users.deleted_at')
            ->distinct()
            ->pluck('user_id')
            ->toArray();
        $providers = User::whereIn('id', $serviceIds)->get();
        $response = [];
        foreach ($providers as $key => $provider) {
            $response[] = $provider->minimizeJsonCustomerResponse();
        }
        return returnSuccessResponse('Data sent successfully', $response, true);
    }

    /**
     * Get post details
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function postDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id,deleted_at,NULL',
            'booking_id' => 'required',
            'tip_amount' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $post = Post::getServiceProviderPostDetails($request->post_id);
        return returnSuccessResponse("Post details", $post);
    }

    public function reportPost(Request $request)
    {
        $rules = [
            'post_id' => 'required|exists:posts,id,deleted_at,NULL',
            'report_type' => 'required',
            'comment' => 'required|max:500'
        ];
        $inputArr = $request->all();
        $validator = Validator::make($inputArr, $rules);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $post = Post::find($request->post_id);
        if (! empty($post)) {
            $request->request->add([
                'report_to' => $post->user_id,
                'reported_by' => Auth::id()
            ]);
                $report = Post::reportPost($request);
                if (!empty($report)) {
                return returnSuccessResponse('Post report submitted successfully');
            } else {
                return returnErrorResponse('Post report not submitted');
            }
        }
        return returnErrorResponse('Post not found');
    }
}
