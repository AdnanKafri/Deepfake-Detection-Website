<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'analysis_id',
        'segment_index',
        'prediction',
        'confidence',
        'original_image_path',
        'cropped_face_path',
        'extra_json',
    ];

    public function analysis()
    {
        return $this->belongsTo(Analysis::class);
    }
}
