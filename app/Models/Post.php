<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    const IS_APPROVED = 'approved';
    const IS_PENDING = 'pending';
    const IS_DECLINED = 'declined';

    protected $fillable = [
        'title',
        'description',
        'address',
        'status'
    ];

    protected $with = ['images'];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
