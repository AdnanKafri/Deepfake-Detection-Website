<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    use HasFactory;

    public const STATUS_QUEUED = 'queued';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'file_type',
        'status',
        'prediction',
        'confidence',
        'category',
        'user_feedback',
        'report_flag',
        'result_json',
        'error_message',
        'queued_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'confidence' => 'float',
        'report_flag' => 'boolean',
        'queued_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(AnalysisDetail::class);
    }
}
