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

    protected $fillable = [
        'id',
        'name',
        'status'
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'status',
        ];
    }
}
