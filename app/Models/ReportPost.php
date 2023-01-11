<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property integer $report_to
 * @property integer $reported_by
 * @property integer $post_id
 * @property int $report_type
 * @property string $comment
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property Post $post
 * @property User $user
 * @property User $user
 */
class ReportPost extends Model
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
        'report_to',
        'reported_by',
        'post_id',
        'report_type',
        'comment',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }

    public function getAllReportedPosts($request = null)
    {
        $reports = self::latest();
        if (! empty($request->post_id)) {
            $reports = $reports->where('post_id', $request->post_id);
        }
        return $reports->get();
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'reported_by');
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reportTo()
    {
        return $this->belongsTo('App\Models\User', 'report_to');
    }

    public function getPostReportType()
    {
        $list = [
            NUDITY_OR_SEXUAL_ACTIVITY => 'Nudity or sexual activity',
            VIOLENCE_OR_DANGEROUS_ORGANIZATIONS => 'Violence or dangerous organizations',
            HATE_SPEECH_OR_SYMBOLS => 'hate speech or symbols',
            SALE_OF_ILLEGAL_OR_REGULATED_GOODS => 'Sale of illegal or regulated goods',
            BULLYING_OR_HARASSMENT => 'Bullying or harassment',
            INTELLECTUAL_PROPERLY_VIOLATION => 'Intellectual properly violation',
            SUICIDE_OR_SELF => 'Suicide or self',
            SCAM_OR_FRAUD => 'Scam or fraud',
            FALSE_INFORMATION => 'False information',
            I_JUST_DONT_LIKE_IT => 'I just dont like it'
        ];
        return ! empty($list[$this->report_type]) ? $list[$this->report_type] : null;
    }
}
