<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Inertia\Inertia;

class PostManagementPropertiesController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return Inertia::render('tenants/admin/post-management/properties/index', ['posts' => $posts]);
    }
}
