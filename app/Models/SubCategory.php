<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'category_id',
        'name'
    ];

    public function category()
    {
         // Inverse relationship with Category
        return $this->belongsTo(Category::class);
    }

    public function dynamicForms()
    {
        // One-to-Many relationship with DynamicForm
        return $this->hasMany(DynamicForm::class, 'subcategory_id');
    }
}
