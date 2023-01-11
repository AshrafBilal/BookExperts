<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $post_id
 * @property string $comment
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Post $post
 * @property User $user
 */
class Comment extends Model
{

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'integer';

    /**
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
        'created_at',
        'updated_at',
        'deleted_at',
        'parent_comment_id'
    ];

    public function delete()
    {
        CommentLike::where('comment_id', $this->id)->get()->each(function ($comment) {
            $comment->delete();
        });
        return parent::delete();
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }

    public function jsonResponse($provider = true)
    {
        $json = [];
        $json['id'] = $this->id;
        $json['user_id'] = $this->user_id;
        $json['parent_comment_id'] = $this->parent_comment_id;
        $json['post_id'] = $this->post_id;
        $json['comment'] = $this->comment;
        $json['total_likes'] = $this->getTotalLikes();
        $json['is_liked'] = $this->isLiked();
        $comment_by = @$this->user;
        $json['comment_by_name'] = @$comment_by->full_name;
        $json['comment_by_pic'] = @$comment_by->profile_file;
        $json['created_at'] = @$this->created_at->diffForHumans();
        return $json;
    }

    public function getTotalLikes()
    {
        return CommentLike::where([
            'post_id' => $this->post_id,
            'comment_id' => $this->id,
            'state_id' => ACTIVE_STATUS
        ])->count();
    }

    public function isLiked()
    {
        return CommentLike::where([
            'post_id' => $this->post_id,
            'comment_id' => $this->id,
            'state_id' => ACTIVE_STATUS,
            'user_id' => Auth::id()
        ])->count();
    }

    public static function getPostComments($request)
    {
        $json = $data = [];
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
            return $data;
        }
    }
}
