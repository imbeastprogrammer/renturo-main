<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicFormPage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'sort_no',
        'dynamic_form_id',
    ];

    protected $with = ['dynamicFormFields'];

    public function dynamicForm()
    {
        // Inverse relationship with DynamicForm
        return $this->belongsTo(DynamicForm::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dynamicFormFields()
    {
        // One-to-Many relationship with DynamicFormField
        return $this->hasMany(DynamicFormField::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
