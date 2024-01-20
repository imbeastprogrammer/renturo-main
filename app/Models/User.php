<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;
use Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const ROLE_ADMIN = 'ADMIN';
    const ROLE_OWNER = 'OWNER';
    const ROLE_USER = 'USER';
    const ROLE_PARTNER = 'PARTNER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'role',
        'email',
        'username',
        'mobile_number',
        'password',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['verified_mobile_no'];

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::title($this->first_name . ' ' . $this->last_name)
        );
    }

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => Hash::make($value)
        );
    }

    protected function getVerifiedMobileNoAttribute()
    {
        return $this->mobileVerification()->latest()->first();
    }

    public function mobileVerification()
    {
        return $this->hasMany(MobileVerification::class);
    }

    public function dynamicFormPages()
    {
        return $this->hasMany(DynamicFormPage::class);
    }

    public function dynamicFormFields()
    {
        return $this->hasMany(dynamicFormFields::class);
    }

    public function store() {
        return $this->hasMany(Store::class);
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

    /**
     * The chats that the user belongs to.
     */
    public function chats()
    {
        return $this->belongsToMany(Chat::class)->withPivot('is_admin');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
