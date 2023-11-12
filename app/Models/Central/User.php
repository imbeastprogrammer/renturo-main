<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    const ROLE_SUPER_ADMIN = 'SUPER-ADMIN';

    protected $fillable = [
        'first_name',
        'last_name',
        'role',
        'email',
        'mobile_number',
        'password',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Hash::make($value)
        );
    }

    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser() {
        return $this->hasMany(User::class, 'id', 'updated_by');
    }

    public function deletedByUser() {
        return $this->hasMany(User::class, 'id', 'deleted_by');
    }
}
