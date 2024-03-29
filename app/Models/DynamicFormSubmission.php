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
        'store_id',
        'data'
    ];

    public function dynamicForm()
    {
        return $this->belongsTo(DynamicForm::class, 'dynamic_form_id');
    }

    // Relationship to Store
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function dynamicFormAvailability()
    {
        return $this->hasMany(DynamicFormAvailability::class);
    }

}
