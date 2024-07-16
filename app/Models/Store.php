<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'url',
        'logo',
        'category_id',
        'sub_category_id',
        'address',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
    ];

    public function users() {
        return $this->belongsTo(User::class);
    }

    // Relationship to FormSubmissions
    public function dynamicFormSubmissions()
    {
        return $this->hasMany(DynamicFormSubmission::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function subCategory() {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
}
