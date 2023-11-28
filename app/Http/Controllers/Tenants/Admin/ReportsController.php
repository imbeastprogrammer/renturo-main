<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportsController extends Controller
{
    public function index()
    {
        $reports = [];
        return Inertia::render('tenants/admin/user-management/reports/index', ['reports' => $reports]);
    }

    public function show()
    {
        return Inertia::render('tenants/admin/user-management/reports/view-report/index');
    }
}
