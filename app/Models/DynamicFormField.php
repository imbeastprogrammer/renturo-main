<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DynamicFormField extends Model
{
    use HasFactory, SoftDeletes;

    const FIELD_TYPES = [
        'text',
        'number',
        'email',
        'password',
        'file',
        'checkbox',
        'radio',
        'date',
        'time',
        'datetime-local'
    ];

    protected $fillable = [
        'user_id',
        'dynamic_form_page_id',
        'input_field_label',
        'input_field_name',
        'input_field_type',
        'is_required',
        'is_multiple',
        'sort_no',
        'data'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dynamicFormPage()
    {
        return $this->belongsTo(DynamicFormPage::class);
    }
}
