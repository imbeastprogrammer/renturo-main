<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RolesManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('central/super-admin/administration/roles/index');
    }

    public function create()
    {
        return Inertia::render('central/super-admin/administration/roles/add-role/index');
    }

    public function edit($id)
    {
        return Inertia::render('central/super-admin/administration/roles/edit-role/index');
    }
}
