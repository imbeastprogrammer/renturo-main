<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicFormAvailability extends Model
{
    use HasFactory, SoftDeletes;

    const AVAILABILITY_TYPES = [
        'hourly',
        'daily',
    ];

    protected $fillable = [
        'dynamic_form_submission_id',
        'user_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'type',
        'minimum_duration',
        'status',
        'recurring'
    ];

    public function dynamicFormSubmission()
    {
        return $this->belongsTo(DynamicFormSubmission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
