<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property integer $id
 * @property integer $user_id
 * @property boolean $file_type
 * @property boolean $post_type
 * @property string $url
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property Comment[] $comments
 * @property Like[] $likes
 * @property TagUser[] $tagUsers
 */
class Post extends Model
{
    use SoftDeletes;

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
        'file_type',
        'post_type',
        'url',
        'description',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function delete()
    {
        $this->unlinkFiles();
        Comment::where('post_id', $this->id)->get()->each(function ($comment) {
            $comment->delete();
        });
        Like::where('post_id', $this->id)->get()->each(function ($like) {
            $like->delete();
        });

        return parent::delete();
    }

    public function unlinkFiles()
    {
        $files = File::where([
            'model_id' => $this->id,
            'model_type' => get_class($this)
        ])->get();
        foreach ($files as $file) {
            $fileUrl = basename($file->file);
            if (! empty($fileUrl)) {
                @Storage::disk('posts')->delete($fileUrl);
            }
            $file->delete();
        }

        return true;
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    /*
     * public function comments()
     * {
     * return $this->hasMany('App\Models\Comment');
     * }
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_comment_id');
    }

    public function postFiles()
    {
        return $this->hasMany('App\Models\File', 'model_id')->where([
            'model_id' => $this->id,
            'model_type' => get_class($this)
        ]);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany('App\Models\Like');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tagUsers()
    {
        return $this->hasMany('App\Models\TagUser');
    }

    public function getAllPosts()
    {
        return self::latest()->get();
    }

    public function getAllReportedPosts()
    {
        return self::join('report_posts', 'report_posts.post_id', 'posts.id')->whereNull('posts.deleted_at')
            ->select('*', 'posts.id as id')
            ->orderBy('report_posts.created_at', 'desc')
            ->get();
    }

    public function getTotalReportCount()
    {
        return ReportPost::where([
            'post_id' => $this->id
        ])->count();
    }

    public function getPostType()
    {
        $list = [
            POST_TYPE_NORMAL => 'Normal Post',
            POST_TYPE_STATUS_STORY => 'status Story Post',
            POST_TYPE_BOTH => 'Normal and status Story Post'
        ];
        return ! empty($list[$this->post_type]) ? $list[$this->post_type] : null;
    }

    public function getFileType()
    {
        $list = [
            FILE_TYPE_AUDIO => 'Audio',
            FILE_TYPE_IMAGE => 'Image',
            FILE_TYPE_VIDEO => 'Video',
            FILE_TYPE_OTHER => 'Video/Image',
            FILE_TYPE_PDF => 'PDF'
        ];
        return ! empty($list[$this->file_type]) ? $list[$this->file_type] : null;
    }

    public function jsonResponse($provider = true)
    {
        $json = [];
        $json['id'] = $this->id;
        $json['user_id'] = $this->user_id;
        $json['file_type'] = $this->file_type;
        $json['post_type'] = $this->post_type;
        if (! empty($this->postFiles->count())) {

            foreach ($this->postFiles as $file) {
                $json['files'][] = [
                    'id' => $file->id,
                    'file' => $file->file,
                    'type_id' => $file->type_id
                ];
            }
        } else {
            $json['files'] = [];
            self::where('id', $this->id)->delete();
            return false;
        }
        $json['description'] = $this->description;
        $json['is_liked'] = $this->isLikedByMe();
        $json['total_likes'] = $this->totalLikes();
        $json['total_comments'] = $this->totalComments();
        if (! empty($provider)) {
            $json['provider'] = @$this->user->minimizeJsonResponse(false, true);
        }
        $json['created_at'] = @$this->created_at->diffForHumans();
        $json['updated_at'] = @$this->updated_at->toDateTimeString();
        return $json;
    }

    public function isLikedByMe()
    {
        return Like::where([
            'post_id' => $this->id,
            'state_id' => ACTIVE_STATUS,
            'user_id' => Auth::id()
        ])->count();
    }

    public function totalLikes()
    {
        return Like::where([
            'post_id' => $this->id,
            'state_id' => ACTIVE_STATUS
        ])->join('users', 'likes.user_id', 'users.id')
            ->whereNull('users.deleted_at')
            ->count();
    }

    public function totalComments()
    {
        return Comment::where([
            'post_id' => $this->id
        ])->
        // ->whereNull('parent_comment_id')
        join('users', 'comments.user_id', 'users.id')
            ->whereNull('users.deleted_at')
            ->count();
    }

    public static function getServiceProviderPostList($request)
    {
        $page_limit = ! empty($request->query('page_limit')) ? $request->query('page_limit') : 10;
        $provider_id = ! empty($request->query('provider_id')) ? $request->query('provider_id') : null;
        $specialistIds = Follow::where([
            'follow_by' => Auth::id()
        ])->distinct()
            ->pluck('user_id')
            ->toArray();

        $query = self::latest()->where([
            'post_type' => POST_TYPE_NORMAL
        ]);
        if (! empty($provider_id)) {
            $query = $query->where([
                'user_id' => $provider_id
            ]);
        } else {
            $query = $query->whereIn('user_id', $specialistIds);
        }

        $reportPostIds = ReportPost::where([
            'reported_by' => Auth::id()
        ])->distinct()
            ->pluck('post_id')
            ->toArray();

        if (is_array($reportPostIds) && ! empty(count($reportPostIds))) {
            $query = $query->whereNotIn('id', $reportPostIds);
        }

        $reportedPosts = ReportPost::where('reported_by', auth::id())->pluck('post_id')->toArray();

        if (! empty($reportedPosts) && is_array($reportedPosts) && ! empty(count($reportedPosts))) {
            $query = $query->whereNotIn('posts.id', $reportedPosts);
        }

        $query = $query->paginate($page_limit);
        $items = $query->items();
        $json = [];
        foreach ($items as $item) {
            $json[] = $item->jsonResponse();
        }
        $data['notification_count'] = Notification::where([
            'receiver_id' => Auth::id(),
            'read' => NOTIFICATION_UN_READ
        ])->count();
        $data['next_page'] = ($query->nextPageUrl()) ? true : false;
        $data['posts'] = $json;
        $data['current_page'] = $query->currentPage();
        $data['per_page'] = $query->perPage();
        $data['links'] = $query->linkCollection();
        $data['total'] = $query->total();
        $data['total_pages'] = (int) ceil($query->total() / $query->perPage());
        return $data;
    }

    public static function getServiceProviderPostDetails($post_id)
    {
        $post = self::find($post_id);
        $data = (object) [];
        if (! empty($post)) {
            $data = $post->jsonResponse();
        }
        return $data;
    }

    public static function getServiceProviderStoryDetails($request)
    {
        $specialistIds = Follow::where([
            'follow_by' => Auth::id()
        ])->distinct()
            ->pluck('user_id')
            ->toArray();
        $providersIds = self::latest()->where([
            'post_type' => POST_TYPE_STATUS_STORY
        ])
            ->where('created_at', '>=', Carbon::now()->subDay()
            ->toDateTimeString())
            ->whereIn('user_id', $specialistIds)
            ->distinct()
            ->pluck('user_id')
            ->toArray();
        $reportedPosts = ReportPost::where('reported_by', auth::id())->pluck('post_id')->toArray();

        $json = $minJson = [];
        $providers = User::whereIn('id', $providersIds)->get();
        if (! $providers->isEmpty()) {
            foreach ($providers as $provider) {

                $stories = self::latest()->where([
                    'post_type' => POST_TYPE_STATUS_STORY,
                    'user_id' => $provider->id
                ])->where('created_at', '>=', Carbon::now()->subDay()
                    ->toDateTimeString());

                if (! empty($reportedPosts) && is_array($reportedPosts) && ! empty(count($reportedPosts))) {
                    $stories = $stories->whereNotIn('posts.id', $reportedPosts);
                }
                $stories = $stories->get();
                if (! empty($stories->count())) {
                    $minJson = $provider->minimizeJsonResponse();
                    foreach ($stories as $item) {
                        $minJson['story'][] = $item->jsonResponse(false);
                    }
                    $json[] = $minJson;
                }
            }
        }
        return $json;
    }

    public static function reportPost($request)
    {
        $report = ReportPost::where([
            'reported_by' => $request->reported_by,
            'post_id' => $request->post_id
        ])->first();
        $report = !empty($report)?$report:new ReportPost();
        $report->fill($request->all());
        if($report->save())
        {
            return true;
        }
        return false;
    }
    

    
}
