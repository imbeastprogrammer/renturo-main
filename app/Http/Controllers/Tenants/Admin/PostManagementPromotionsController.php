<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PostManagementPromotionsController extends Controller
{
    public function index()
    {
        return Inertia::render('tenants/admin/post-management/promotions/index');
    }

    public function edit($id)
    {
        return Inertia::render('tenants/admin/post-management/promotions/view-promotion/index');
    }
}
