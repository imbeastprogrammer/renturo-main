<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ImageUpload extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'dynamic_form_submission_id',
        'file_name',
        'file_path',
        'file_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dynamicFormsubmission()
    {
        return $this->belongsTo(DynamicFormSubmission::class);
    }
}
