<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicFormSubmission extends Model
{
    use HasFactory, SoftDeletes; 

    protected $fillable = [
        'dynamic_form_id',
        'user_id',
        'data',
    ];

    public function dynamicForm()
    {
        return $this->belongsTo(DynamicForm::class, 'dynamic_form_id');
    }
}
