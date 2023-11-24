<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasFactory, HasDatabase, HasDomains, SoftDeletes;

    const ACTIVE_STATUS = 'active';
    const INACTIVE_STATUS = 'inactive';

    const PLAN_TYPES = [
        'demo',
        'starter_plan',
        'professional_plan',
        'enterprise_plan',
        'custom_plan'
    ];

    protected $fillable = [
        'id',
        'company',
        'status',
        'plan_type',
        'created_by',
        'updated_by',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'company',
            'status',
            'plan_type',
            'created_by',
            'updated_by'
        ];
    }

    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser() {
        return $this->hasMany(User::class, 'id', 'updated_by');
    }
}

