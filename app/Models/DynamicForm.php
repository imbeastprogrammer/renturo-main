<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicForm extends Model
{
    use HasFactory, SoftDeletes;

    // Fillable properties to protect against mass-assignment
    protected $fillable = [
        'user_id',
        'name', 
        'description', 
        'subcategory_id'
    ];

    public function dynamicFormPages(){

        // One-to-Many relationship with DynamicFormPage
        return $this->hasMany(DynamicFormPage::class);
    }
    
    public function subCategory(){

        // Many-to-One relationship with SubCategory
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function user()
    {
        // Many-to-One relationship with User
        return $this->belongsTo(User::class);
    }

    public function dynamicFormSubmissions()
    {
        return $this->hasMany(DynamicFormSubmission::class);
    }
}
