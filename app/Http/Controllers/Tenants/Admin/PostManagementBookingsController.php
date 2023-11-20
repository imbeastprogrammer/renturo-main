<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PostManagementBookingsController extends Controller
{
    public function index()
    {
        return Inertia::render('tenants/admin/post-management/bookings/index');
    }
}
