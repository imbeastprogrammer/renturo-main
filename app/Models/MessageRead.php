<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageRead extends Model
{
    protected $fillable = ['user_id', 'message_id', 'read_at'];

    // Define the table if it's not the default 'message_reads'
    protected $table = 'message_reads';

    // Define relationships if necessary, e.g., to User and Message models
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
