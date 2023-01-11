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
use App\Models\Comment;
use App\Models\User;
use App\Models\CommentLike;

class CommentController extends Controller
{

    /**
     * add comment on post
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse|unknown
     */
    public function addPostComment(Request $request)
    {
        $user = $request->user();
        $rules = [
            'post_id' => 'required',
            'comment' => 'required|required:max:500'
        ];
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
        $postComment = new Comment();

        $postComment->fill($request->all());
        if ($postComment->save()) {
            $request->request->add([
                'post_id' => $postComment->post_id
            ]);
            if ($user->role_id == NORMAL_USER_TYPE && empty($request->parent_comment_id)) {
                Notification::sendNotification([
                    'sender_id' => $user->id,
                    'receiver_id' => $postComment->post->user_id,
                    'model' => $postComment,
                    'type' => NOTIFICATION_TYPE_CUSTOMER_COMMENT_ON_POST,
                    'message' => ucwords($user->getFullName()) . ' commented on your post.'
                ]);
            }
            $comments = Comment::getPostComments($request);
            return returnSuccessResponse("Post comment submitted successfully.", $comments);
        } else {
            return returnErrorResponse('Post comment not saved.');
        }
    }

    /**
     * post comments listing API
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse
     */
    public function postComments(Request $request)
    {
        $json = $data = [];
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id,deleted_at,NULL',
            'booking_id' => 'required',
            'tip_amount' => 'required'
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        $exist = Comment::where([
            'post_id' => $request->post_id
        ])->exists();
        if (! empty($exist)) {
            $comments = Comment::where([
                'post_id' => $request->post_id
            ])->whereNull('parent_comment_id')->get();

            foreach ($comments as $comment) {
                $json = $comment->jsonResponse();
                $nested_comments = Comment::where([
                    'post_id' => $request->post_id,
                    'parent_comment_id' => $comment->id
                ])->get();

                if (! empty($nested_comments->count())) {
                    foreach ($nested_comments as $nested_comment) {
                        $json['nested_comments'][] = $nested_comment->jsonResponse();
                    }
                } else {
                    $json['nested_comments'] = [];
                }
                $data[] = $json;
            }
            return returnSuccessResponse('Data sent successfully', $data, true);
        }
        return returnSuccessResponse('Data sent successfully', [], true);
    }

    /**
     * Like and unlike post comment
     *
     * @param Request $request
     * @throws HttpResponseException
     * @return \Illuminate\Http\JsonResponse
     */
    public function likeDislikeComment(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|exists:posts,id,deleted_at,NULL',
            'comment_id' => 'required|exists:comments,id,deleted_at,NULL',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->first();
            throw new HttpResponseException(returnValidationErrorResponse($errorMessages));
        }
        try {
            DB::beginTransaction();

            $request->request->add([
                'user_id' => $user->id
            ]);
            $CommentLike = CommentLike::where([
                'user_id' => $request->user_id,
                'post_id' => $request->post_id,
                'comment_id' => $request->comment_id
            ])->first();
            $comment = Comment::find($request->comment_id);

            if (! $CommentLike) {
                $CommentLike = new CommentLike();
            }
            $CommentLike->user_id = $request->user_id;
            $CommentLike->post_id = $request->post_id;
            $CommentLike->state_id = $request->status;
            $CommentLike->comment_id = $request->comment_id;
            $CommentLike->created_at = date('Y-m-d H:i:s');
            $CommentLike->save();
            if ($comment->user_id != $user->id && $request->status == ACTIVE_STATUS && $user->role_id == NORMAL_USER_TYPE) {
                Notification::sendNotification([
                    'sender_id' => $user->id,
                    'receiver_id' => $CommentLike->post->user_id,
                    'model' => $CommentLike,
                    'type' => NOTIFICATION_TYPE_CUSTOMER_LIKE_COMMENT,
                    'message' => ucwords($user->getFullName()) . ' like your comment.'
                ]);
               
            }
            DB::commit();
            $message = ($request->status == 1) ? "Comment liked successfully." : "Comment unliked successfully.";
            return returnSuccessResponse($message);
        } catch (\Exception $e) {
            DB::rollback();
            return returnErrorResponse($e->getMessage());
        }
    }
}
