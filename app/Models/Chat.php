<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        // other attributes as needed
    ];

    /**
     * The users that belong to the chat.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('is_admin');
    }

    /**
     * Get the messages for the chat.
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

}
