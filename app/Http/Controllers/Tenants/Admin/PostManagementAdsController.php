<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PostManagementAdsController extends Controller
{
    public function index()
    {
        return Inertia::render('tenants/admin/post-management/advertisements/index');
    }

    public function show($id)
    {
        return Inertia::render('tenants/admin/post-management/advertisements/view-advertisement/index');
    }
}
