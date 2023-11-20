<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsManagementController extends Controller
{
    public function personalInformation()
    {
        return Inertia::render('tenants/admin/settings/personal-information/index');
    }

    public function changePassword()
    {
        return Inertia::render('tenants/admin/settings/change-password/index');
    }
}
