<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @property integer $id
 * @property integer $report_to
 * @property integer $reported_by
 * @property int $report_type
 * @property string $comment
 * @property boolean $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property User $user
 * @property User $user
 */
class ReportUser extends Model
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
    public function reportedBy()
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

    public function getReportType()
    {
        $list = [
            1 => "It's pretending to be someone else",
            2 => 'Bullying or harassment',
            3 => 'False information',
            4 => "I just dont't like it"
        ];
        return ! empty($list[$this->report_type]) ? $list[$this->report_type] : null;
    }

    public static function reportUser($request)
    {
        $report = self::where([
            'reported_by' => $request->reported_by,
            'report_to' => $request->report_to
        ])->first();
        $report = ! empty($report) ? $report : new self();
        $report->fill($request->all());
        if ($report->save()) {
            return $report;
        }
        return false;
    }
}
