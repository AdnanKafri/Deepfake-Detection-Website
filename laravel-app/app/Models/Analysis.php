<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_name',
        'file_type',
        'prediction',
        'confidence',
        'category',
        'user_feedback',
        'report_flag',
        'result_json',
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
