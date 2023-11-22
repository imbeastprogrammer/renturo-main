<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FormBuilderController extends Controller
{
    public function index()
    {
        return Inertia::render('tenants/admin/post-management/form-builder/index');
    }
}
