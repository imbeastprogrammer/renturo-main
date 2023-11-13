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
    ];

    protected $with = ['dynamicFormFields'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dynamicFormFields()
    {
        return $this->hasMany(DynamicFormField::class);
    }
}
