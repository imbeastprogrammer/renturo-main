<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use App\Models\DynamicForm;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FormBuilderController extends Controller
{
    /**
     * Display the form builder page.
     *
     * This page allows administrators to create and manage dynamic forms.
     * It preloads necessary data like categories, subcategories, and existing forms.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        try {
            // Verify user is authenticated and authorized
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            if (!Auth::user()->can('access-admin')) {
                abort(403, 'Unauthorized action.');
            }

            // Load necessary data with proper eager loading
            $categories = Category::with(['subCategories' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'subcategories' => $category->subCategories->map(function ($subcategory) {
                        return [
                            'id' => $subcategory->id,
                            'name' => $subcategory->name
                        ];
                    })
                ];
            });

            $forms = DynamicForm::with([
                'subCategory.category',
                'dynamicFormPages' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'dynamicFormPages.dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'user'
            ])
            ->latest()
            ->get()
            ->map(function ($form) {
                return [
                    'id' => $form->id,
                    'name' => $form->name,
                    'description' => $form->description,
                    'subcategory' => [
                        'id' => $form->subCategory->id,
                        'name' => $form->subCategory->name,
                        'category' => [
                            'id' => $form->subCategory->category->id,
                            'name' => $form->subCategory->category->name
                        ]
                    ],
                    'pages_count' => $form->dynamicFormPages->count(),
                    'fields_count' => $form->dynamicFormPages->sum(function ($page) {
                        return $page->dynamicFormFields->count();
                    }),
                    'created_by' => [
                        'id' => $form->user->id,
                        'name' => $form->user->first_name . ' ' . $form->user->last_name
                    ],
                    'created_at' => $form->created_at,
                    'updated_at' => $form->updated_at
                ];
            });

            // Pass data to the Inertia view
            return Inertia::render('tenants/admin/post-management/form-builder/index', [
                'categories' => $categories,
                'forms' => $forms,
                'can' => [
                    'create_forms' => Auth::user()->can('create-forms'),
                    'edit_forms' => Auth::user()->can('edit-forms'),
                    'delete_forms' => Auth::user()->can('delete-forms')
                ]
            ]);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Form Builder Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return error response
            if (request()->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to load form builder.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            // Redirect with error message
            return redirect()->back()->with('error', 'Failed to load form builder. Please try again.');
        }
    }
}
