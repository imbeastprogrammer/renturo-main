<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormRequest;
use Illuminate\Database\QueryException;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\DynamicForm;
use Inertia\Inertia;
use Exception;


/**
 * @OA\Tag(
 *     name="Admin Dynamic Forms",
 *     description="API endpoints for managing dynamic forms in the admin interface"
 * )
 * 
 * @OA\Server(
 *     url="{tenant}.renturo.test",
 *     description="Tenant Admin Server",
 *     @OA\ServerVariable(
 *         serverVariable="tenant",
 *         default="main",
 *         description="Tenant subdomain"
 *     )
 * )
 */
class DynamicFormController extends Controller
{
    /**
     * Display a listing of the dynamic forms.
     *
     * @OA\Get(
     *     path="/admin/form",
     *     tags={"Admin Dynamic Forms"},
     *     summary="List all dynamic forms",
     *     description="Get a paginated list of dynamic forms with their subcategories and categories",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term to filter forms by name or description",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="subcategory",
     *         in="query",
     *         description="Filter forms by subcategory ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic Form was successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(
     *                         property="subcategory",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(
     *                             property="category",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="perPage", type="integer"),
     *                 @OA\Property(property="currentPage", type="integer"),
     *                 @OA\Property(property="lastPage", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to fetch dynamic forms."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Start with a base query
            $query = DynamicForm::query();

            // Handle soft deletes
            if ($request->boolean('only_trashed')) {
                $query->onlyTrashed();
            } elseif ($request->boolean('with_trashed')) {
                $query->withTrashed();
            }

            // Filter by subcategory if provided
            if ($request->filled('subcategory')) {
                $query->where('subcategory_id', $request->subcategory);
            }

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Load relationships
            $query->with([
                'subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'subCategory.category' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPages' => function ($q) {
                    $q->withTrashed()->orderBy('sort_no');
                },
                'dynamicFormSubmissions' => function ($q) {
                    $q->withTrashed()->latest();
                },
                'user'
            ]);

            // Order by latest
            $query->latest();

            // Paginate the results
            $perPage = $request->per_page ?? 15;
            $forms = $query->paginate($perPage);

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic forms fetched successfully.',
                'data' => $forms->map(function ($form) {
            return [
                'id' => $form->id,
                'name' => $form->name,
                'description' => $form->description,
                        'subcategory' => $form->subCategory ? [
                            'id' => $form->subCategory->id,
                            'name' => $form->subCategory->name,
                            'category' => $form->subCategory->category ? [
                                'id' => $form->subCategory->category->id,
                                'name' => $form->subCategory->category->name
                            ] : null
                        ] : null,
                        'pages_count' => $form->dynamicFormPages->count(),
                        'submissions_count' => $form->dynamicFormSubmissions->count(),
                        'created_by' => [
                            'id' => $form->user->id,
                            'name' => $form->user->first_name . ' ' . $form->user->last_name
                ],
                'created_at' => $form->created_at,
                'updated_at' => $form->updated_at,
                'deleted_at' => $form->deleted_at
            ];
                }),
                'meta' => [
                    'current_page' => $forms->currentPage(),
                    'from' => $forms->firstItem(),
                    'last_page' => $forms->lastPage(),
                    'per_page' => $forms->perPage(),
                    'to' => $forms->lastItem(),
                    'total' => $forms->total(),
                    'filters' => [
                        'search' => $request->search,
                        'subcategory' => $request->subcategory,
                        'with_trashed' => $request->boolean('with_trashed'),
                        'only_trashed' => $request->boolean('only_trashed')
                    ]
                ]
            ];

            // For JSON requests
            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            // For Inertia requests, load additional data
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

            return Inertia::render('tenants/admin/post-management/dynamic-forms/index', [
                'forms' => $response['data'],
                'meta' => $response['meta'],
                'categories' => $categories,
                'can' => [
                    'create_forms' => auth()->user()->can('create-forms'),
                    'edit_forms' => auth()->user()->can('edit-forms'),
                    'delete_forms' => auth()->user()->can('delete-forms')
                ]
            ]);

        } catch (\Exception $e) {
            $error = 'Failed to fetch dynamic forms.';
            $code = 500;

        if ($request->expectsJson()) {
            return response()->json([
                    'status' => 'error',
                    'message' => $error,
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], $code);
            }

            return redirect()->back()->with('error', $error);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created dynamic form.
     *
     * @OA\Post(
     *     path="/admin/form",
     *     tags={"Admin Dynamic Forms"},
     *     summary="Create a new dynamic form",
     *     description="Create a new dynamic form with name, description, and subcategory",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "subcategory_id"},
     *             @OA\Property(property="name", type="string", description="Name of the form"),
     *             @OA\Property(property="description", type="string", description="Description of the form"),
     *             @OA\Property(property="subcategory_id", type="integer", description="ID of the subcategory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Form created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form was successfully created."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="subcategory_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="subcategory_id", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to create dynamic form."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFormRequest $request)
    {
        try {
            // Verify the subcategory exists and is not deleted
            $subcategory = SubCategory::with([
                'category' => function ($q) {
                    $q->withTrashed();
                }
            ])->findOrFail($request->subcategory_id);

            if ($subcategory->trashed()) {
                throw new \Exception('The selected subcategory has been deleted.');
            }

            if (!$subcategory->category || $subcategory->category->trashed()) {
                throw new \Exception('The parent category no longer exists or has been deleted.');
            }

            DB::beginTransaction();

            // Create the form with user ID
            $dynamicForm = DynamicForm::create(array_merge(
                $request->validated(),
                ['user_id' => auth()->id()]
            ));

            // Load relationships for the response
            $dynamicForm->load([
                'subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'subCategory.category' => function ($q) {
                    $q->withTrashed();
                },
                'user'
            ]);

            DB::commit();

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form created successfully.',
                'data' => [
                    'id' => $dynamicForm->id,
                    'name' => $dynamicForm->name,
                    'description' => $dynamicForm->description,
                    'subcategory' => [
                        'id' => $dynamicForm->subCategory->id,
                        'name' => $dynamicForm->subCategory->name,
                        'category' => [
                            'id' => $dynamicForm->subCategory->category->id,
                            'name' => $dynamicForm->subCategory->category->name
                        ]
                    ],
                    'created_by' => [
                        'id' => $dynamicForm->user->id,
                        'name' => $dynamicForm->user->first_name . ' ' . $dynamicForm->user->last_name
                    ],
                    'created_at' => $dynamicForm->created_at,
                    'updated_at' => $dynamicForm->updated_at
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 201);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'Subcategory not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while creating form.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = match ($e->getMessage()) {
                'The selected subcategory has been deleted.' => 'Cannot create form: selected subcategory is deleted.',
                'The parent category no longer exists or has been deleted.' => 'Cannot create form: parent category does not exist or is deleted.',
                default => 'Failed to create form.'
            };
            $code = match ($e->getMessage()) {
                'The selected subcategory has been deleted.',
                'The parent category no longer exists or has been deleted.' => 422,
                default => 500
            };
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $error,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $code);
        }

        return redirect()->back()->with('error', $error);
    }

    /**
     * Display the specified dynamic form.
     *
     * @OA\Get(
     *     path="/admin/form/{id}",
     *     tags={"Admin Dynamic Forms"},
     *     summary="Get a specific dynamic form",
     *     description="Get detailed information about a dynamic form including its pages and fields",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the dynamic form",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Form was successfully fetched."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(
     *                     property="subcategory",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="dynamicFormPages",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(
     *                             property="dynamicFormFields",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="label", type="string"),
     *                                 @OA\Property(property="type", type="string")
     *                             )
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Form not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Form not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to fetch form."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            // Handle soft deletes
            $query = DynamicForm::query();
            if ($request->boolean('with_trashed')) {
                $query->withTrashed();
            }

            // Find the form with relationships
            $form = $query->with([
                'subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'subCategory.category' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPages' => function ($q) {
                    $q->withTrashed()->orderBy('sort_no');
                },
                'dynamicFormPages.dynamicFormFields' => function ($q) {
                    $q->withTrashed()->orderBy('sort_no');
                },
                'dynamicFormPages.user',
                'dynamicFormPages.dynamicFormFields.user',
                'dynamicFormSubmissions' => function ($q) {
                    $q->withTrashed()->latest();
                },
                'user'
        ])->findOrFail($id);

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form fetched successfully.',
                'data' => [
                    'id' => $form->id,
                    'name' => $form->name,
                    'description' => $form->description,
                    'subcategory' => $form->subCategory ? [
                        'id' => $form->subCategory->id,
                        'name' => $form->subCategory->name,
                        'category' => $form->subCategory->category ? [
                            'id' => $form->subCategory->category->id,
                            'name' => $form->subCategory->category->name
                ] : null
            ] : null,
                    'pages' => $form->dynamicFormPages->map(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                            'sort_no' => $page->sort_no,
                            'fields' => $page->dynamicFormFields->map(function ($field) {
                        return [
                            'id' => $field->id,
                                    'label' => $field->input_field_label,
                                    'name' => $field->input_field_name,
                                    'type' => $field->input_field_type,
                                    'required' => $field->is_required,
                                    'sort_no' => $field->sort_no,
                                    'data' => $field->data,
                                    'created_by' => [
                                        'id' => $field->user->id,
                                        'name' => $field->user->first_name . ' ' . $field->user->last_name
                                    ],
                                    'created_at' => $field->created_at,
                                    'updated_at' => $field->updated_at,
                                    'deleted_at' => $field->deleted_at
                                ];
                            }),
                            'created_by' => [
                                'id' => $page->user->id,
                                'name' => $page->user->first_name . ' ' . $page->user->last_name
                            ],
                            'created_at' => $page->created_at,
                            'updated_at' => $page->updated_at,
                            'deleted_at' => $page->deleted_at
                ];
            }),
                    'submissions_count' => $form->dynamicFormSubmissions->count(),
                    'created_by' => [
                        'id' => $form->user->id,
                        'name' => $form->user->first_name . ' ' . $form->user->last_name
                    ],
                    'created_at' => $form->created_at,
                    'updated_at' => $form->updated_at,
                    'deleted_at' => $form->deleted_at
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            // For Inertia requests, load additional data
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

            return Inertia::render('tenants/admin/post-management/dynamic-forms/show', [
                'form' => $response['data'],
                'categories' => $categories,
                'can' => [
                    'edit_form' => auth()->user()->can('edit-forms'),
                    'delete_form' => auth()->user()->can('delete-forms'),
                    'manage_pages' => auth()->user()->can('manage-form-pages'),
                    'manage_fields' => auth()->user()->can('manage-form-fields')
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $error = 'Dynamic form not found.';
            $code = 404;
        } catch (\Exception $e) {
            $error = 'Failed to fetch dynamic form.';
            $code = 500;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $error,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $code);
        }

        return redirect()->back()->with('error', $error);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DynamicForm  $dynamicForm
     * @return \Illuminate\Http\Response
     */
    public function edit(DynamicForm $dynamicForm)
    {
        //
    }

    /**
     * Update the specified dynamic form.
     *
     * @OA\Put(
     *     path="/admin/form/{id}",
     *     tags={"Admin Dynamic Forms"},
     *     summary="Update a dynamic form",
     *     description="Update a dynamic form's name, description, or subcategory",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the dynamic form",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", description="New name for the form"),
     *             @OA\Property(property="description", type="string", description="New description for the form"),
     *             @OA\Property(property="subcategory_id", type="integer", description="New subcategory ID for the form")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form was successfully updated."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="subcategory_id", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Form not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Form not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="subcategory_id", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to update form."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFormRequest $request, $id)
    {
        try {
            // Find the form with relationships
            $form = DynamicForm::with([
                'subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'subCategory.category' => function ($q) {
                    $q->withTrashed();
                }
            ])->findOrFail($id);

            // If subcategory is being changed, verify the new one exists and is not deleted
            if ($request->filled('subcategory_id') && $request->subcategory_id !== $form->subcategory_id) {
                $subcategory = SubCategory::with([
                    'category' => function ($q) {
                        $q->withTrashed();
                    }
                ])->findOrFail($request->subcategory_id);

                if ($subcategory->trashed()) {
                    throw new \Exception('The selected subcategory has been deleted.');
                }

                if (!$subcategory->category || $subcategory->category->trashed()) {
                    throw new \Exception('The parent category no longer exists or has been deleted.');
                }
            }

            DB::beginTransaction();

            // Update the form
            $form->update($request->validated());

            // Load relationships for the response
            $form->load([
                'subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'subCategory.category' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPages' => function ($q) {
                    $q->withTrashed()->orderBy('sort_no');
                },
                'dynamicFormSubmissions' => function ($q) {
                    $q->withTrashed()->latest();
                },
                'user'
            ]);

            DB::commit();

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form updated successfully.',
                'data' => [
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
                    'submissions_count' => $form->dynamicFormSubmissions->count(),
                    'created_by' => [
                        'id' => $form->user->id,
                        'name' => $form->user->first_name . ' ' . $form->user->last_name
                    ],
                    'created_at' => $form->created_at,
                    'updated_at' => $form->updated_at,
                    'deleted_at' => $form->deleted_at
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = $e->getModel() === DynamicForm::class
                ? 'Dynamic form not found.'
                : 'Subcategory not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while updating form.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = match ($e->getMessage()) {
                'The selected subcategory has been deleted.' => 'Cannot update form: selected subcategory is deleted.',
                'The parent category no longer exists or has been deleted.' => 'Cannot update form: parent category does not exist or is deleted.',
                default => 'Failed to update form.'
            };
            $code = match ($e->getMessage()) {
                'The selected subcategory has been deleted.',
                'The parent category no longer exists or has been deleted.' => 422,
                default => 500
            };
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $error,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $code);
        }

        return redirect()->back()->with('error', $error);
    }

    /**
     * Remove the specified dynamic form.
     *
     * @OA\Delete(
     *     path="/admin/form/{id}",
     *     tags={"Admin Dynamic Forms"},
     *     summary="Delete a dynamic form",
     *     description="Soft delete a dynamic form and its related pages and submissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the dynamic form",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form was successfully deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Form not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Form not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to delete form."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Find the form with relationships
            $form = DynamicForm::with([
                'subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'subCategory.category' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPages' => function ($q) {
                    $q->withTrashed()->orderBy('sort_no');
                },
                'dynamicFormPages.dynamicFormFields' => function ($q) {
                    $q->withTrashed()->orderBy('sort_no');
                },
                'dynamicFormSubmissions' => function ($q) {
                    $q->withTrashed()->latest();
                },
                'user'
            ])->findOrFail($id);

            // Store form data before deletion
            $formData = [
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
                'pages' => $form->dynamicFormPages->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'fields_count' => $page->dynamicFormFields->count()
                    ];
                }),
                'submissions_count' => $form->dynamicFormSubmissions->count(),
                'created_by' => [
                    'id' => $form->user->id,
                    'name' => $form->user->first_name . ' ' . $form->user->last_name
                ],
                'created_at' => $form->created_at,
                'updated_at' => $form->updated_at
            ];

            DB::beginTransaction();

            // Delete the form (this will cascade to pages and fields due to foreign key constraints)
            $form->delete();

            DB::commit();

            // Add deletion timestamp to the response
            $formData['deleted_at'] = now();

            $response = [
                'status' => 'success',
                'message' => 'Dynamic form deleted successfully.',
                'data' => $formData
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'Dynamic form not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while deleting form.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = 'Failed to delete form.';
            $code = 500;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $error,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $code);
        }

        return redirect()->back()->with('error', $error);
    }

    /**
     * Restore a soft-deleted dynamic form.
     *
     * @OA\Post(
     *     path="/admin/form/restore/{id}",
     *     tags={"Admin Dynamic Forms"},
     *     summary="Restore a deleted form",
     *     description="Restore a soft-deleted dynamic form and its related pages and submissions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the dynamic form",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Form restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form was successfully restored.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Form not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Form not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to restore form."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        try {
            // Find the form with relationships
            $form = DynamicForm::withTrashed()->with([
                'subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'subCategory.category' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPages' => function ($q) {
                    $q->withTrashed()->orderBy('sort_no');
                },
                'dynamicFormPages.dynamicFormFields' => function ($q) {
                    $q->withTrashed()->orderBy('sort_no');
                },
                'dynamicFormSubmissions' => function ($q) {
                    $q->withTrashed()->latest();
                },
                'user'
            ])->findOrFail($id);

            // Verify the form is actually deleted
            if (!$form->trashed()) {
                throw new \Exception('Form is not deleted.');
            }

            // Verify the subcategory and category exist and are not deleted
            if (!$form->subCategory || $form->subCategory->trashed()) {
                throw new \Exception('The subcategory no longer exists or has been deleted.');
            }

            if (!$form->subCategory->category || $form->subCategory->category->trashed()) {
                throw new \Exception('The parent category no longer exists or has been deleted.');
            }

            DB::beginTransaction();

            // Restore the form
            $form->restore();

            // Restore all related pages and fields
            foreach ($form->dynamicFormPages as $page) {
                if ($page->trashed()) {
                    $page->restore();
                }

                foreach ($page->dynamicFormFields as $field) {
                    if ($field->trashed()) {
                        $field->restore();
                    }
                }
            }

            // Restore all related submissions
            foreach ($form->dynamicFormSubmissions as $submission) {
                if ($submission->trashed()) {
                    $submission->restore();
                }
            }

            // Refresh the model to get updated timestamps
            $form->refresh();

            DB::commit();

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form restored successfully.',
                'data' => [
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
                    'pages' => $form->dynamicFormPages->map(function ($page) {
                        return [
                            'id' => $page->id,
                            'title' => $page->title,
                            'fields_count' => $page->dynamicFormFields->count(),
                            'deleted_at' => $page->deleted_at
                        ];
                    }),
                    'submissions_count' => $form->dynamicFormSubmissions->count(),
                    'created_by' => [
                        'id' => $form->user->id,
                        'name' => $form->user->first_name . ' ' . $form->user->last_name
                    ],
                    'created_at' => $form->created_at,
                    'updated_at' => $form->updated_at,
                    'deleted_at' => null
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'Dynamic form not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while restoring form.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = match ($e->getMessage()) {
                'Form is not deleted.' => 'Cannot restore form: form is not deleted.',
                'The subcategory no longer exists or has been deleted.' => 'Cannot restore form: subcategory does not exist or is deleted.',
                'The parent category no longer exists or has been deleted.' => 'Cannot restore form: parent category does not exist or is deleted.',
                default => 'Failed to restore form.'
            };
            $code = match ($e->getMessage()) {
                'Form is not deleted.',
                'The subcategory no longer exists or has been deleted.',
                'The parent category no longer exists or has been deleted.' => 422,
                default => 500
            };
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $error,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $code);
        }

        return redirect()->back()->with('error', $error);
    }

    /**
     * Get form pages and fields.
     *
     * @OA\Get(
     *     path="/admin/form/all/{id}",
     *     tags={"Admin Dynamic Forms"},
     *     summary="Get form pages and fields",
     *     description="Get detailed information about a form's pages and fields for the form builder",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the dynamic form",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="with_trashed",
     *         in="query",
     *         description="Include soft deleted records",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(
     *                     property="subcategory",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pages",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="sort_no", type="integer"),
     *                         @OA\Property(
     *                             property="fields",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="label", type="string"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="type", type="string"),
     *                                 @OA\Property(property="required", type="boolean"),
     *                                 @OA\Property(property="sort_no", type="integer"),
     *                                 @OA\Property(property="data", type="object", nullable=true),
     *                                 @OA\Property(
     *                                     property="created_by",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer"),
     *                                     @OA\Property(property="name", type="string")
     *                                 ),
     *                                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="created_by",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string")
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time"),
     *                         @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                     )
     *                 ),
     *                 @OA\Property(property="submissions_count", type="integer"),
     *                 @OA\Property(
     *                     property="created_by",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Form not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Dynamic form not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to fetch dynamic form."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getFormPagesAndFields(Request $request, $id)
    {
        // Retrieve the dynamic form with its pages and fields
        $dynamicForm = DynamicForm::with('dynamicFormPages.dynamicFormFields')
            ->findOrFail($id);

        // For JSON request, return a success response
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Form was successfully fetched.',
                'data' => $dynamicForm,
            ], 200);
        }

        // For non-JSON requests, return an Inertia response
        // Redirect to the desired page and pass the necessary data
        return Inertia::render('tenants/admin/post-management/dynamic-forms/form-builder/index', ['dynamicForm' => $dynamicForm]);
    }

    /**
     * Update form pages and fields.
     *
     * @OA\Put(
     *     path="/admin/form/all/{id}",
     *     tags={"Admin Dynamic Forms"},
     *     summary="Update form pages and fields",
     *     description="Update a form's pages and fields in the form builder",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the dynamic form",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "dynamic_form_pages"},
     *             @OA\Property(property="name", type="string", description="Form name"),
     *             @OA\Property(property="description", type="string", description="Form description"),
     *             @OA\Property(
     *                 property="dynamic_form_pages",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"title", "dynamic_form_fields"},
     *                     @OA\Property(property="id", type="integer", nullable=true, description="Page ID (null for new pages)"),
     *                     @OA\Property(property="title", type="string", description="Page title"),
     *                     @OA\Property(property="sort_no", type="integer", description="Page sort order (optional, will be set based on array index)"),
     *                     @OA\Property(
     *                         property="dynamic_form_fields",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"input_field_label", "input_field_type", "is_required"},
     *                             @OA\Property(property="id", type="integer", nullable=true, description="Field ID (null for new fields)"),
     *                             @OA\Property(property="input_field_label", type="string", description="Field label"),
     *                             @OA\Property(property="input_field_type", type="string", description="Field type (text, textarea, select, radio, checkbox, file, etc.)"),
     *                             @OA\Property(property="is_required", type="boolean", description="Whether the field is required"),
     *                             @OA\Property(property="sort_no", type="integer", description="Field sort order (optional, will be set based on array index)"),
     *                             @OA\Property(
     *                                 property="data",
     *                                 type="object",
     *                                 nullable=true,
     *                                 description="Additional field data (options for select/radio/checkbox, validation rules for file uploads, etc.)",
     *                                 @OA\Property(property="options", type="array", @OA\Items(type="string"), description="Options for select/radio/checkbox fields"),
     *                                 @OA\Property(property="min", type="number", description="Minimum value for number fields"),
     *                                 @OA\Property(property="max", type="number", description="Maximum value for number fields"),
     *                                 @OA\Property(property="step", type="number", description="Step value for number fields"),
     *                                 @OA\Property(property="accept", type="string", description="Accepted file types for file upload fields"),
     *                                 @OA\Property(property="maxSize", type="integer", description="Maximum file size in bytes for file upload fields"),
     *                                 @OA\Property(property="pattern", type="string", description="Regex pattern for text fields"),
     *                                 @OA\Property(property="placeholder", type="string", description="Placeholder text for text/textarea fields"),
     *                                 @OA\Property(property="rows", type="integer", description="Number of rows for textarea fields"),
     *                                 @OA\Property(property="cols", type="integer", description="Number of columns for textarea fields")
     *                             )
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Form updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(
     *                     property="subcategory",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(
     *                         property="category",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pages",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="sort_no", type="integer"),
     *                         @OA\Property(
     *                             property="fields",
     *                             type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer"),
     *                                 @OA\Property(property="label", type="string"),
     *                                 @OA\Property(property="name", type="string"),
     *                                 @OA\Property(property="type", type="string"),
     *                                 @OA\Property(property="required", type="boolean"),
     *                                 @OA\Property(property="sort_no", type="integer"),
     *                                 @OA\Property(property="data", type="object", nullable=true),
     *                                 @OA\Property(
     *                                     property="created_by",
     *                                     type="object",
     *                                     @OA\Property(property="id", type="integer"),
     *                                     @OA\Property(property="name", type="string")
     *                                 ),
     *                                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                             )
     *                         ),
     *                         @OA\Property(
     *                             property="created_by",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string")
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time"),
     *                         @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                     )
     *                 ),
     *                 @OA\Property(property="submissions_count", type="integer"),
     *                 @OA\Property(
     *                     property="created_by",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Data format error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to update dynamic form."),
     *             @OA\Property(property="error", type="string", example="Data format error: One or more input field type have data that cannot be saved.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Form not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Dynamic form not found."),
     *             @OA\Property(property="error", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="description", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="dynamic_form_pages", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="dynamic_form_pages.*.title", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="dynamic_form_pages.*.dynamic_form_fields", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="dynamic_form_pages.*.dynamic_form_fields.*.input_field_label", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="dynamic_form_pages.*.dynamic_form_fields.*.input_field_type", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="dynamic_form_pages.*.dynamic_form_fields.*.is_required", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="dynamic_form_pages.*.dynamic_form_fields.*.data", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to update dynamic form."),
     *             @OA\Property(property="error", type="string", nullable=true)
     *         )
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFormPagesAndFields(Request $request, $id)
    {
        // Start a transaction
        DB::beginTransaction();

        try {

            // Find the DynamicForm
            $dynamicForm = DynamicForm::findOrFail($id);

            // Update DynamicForm details
            $dynamicForm->update($request->only(['name', 'description']));

            // Retrieve all current page titles with their IDs for the dynamic form
            $existingPages = $dynamicForm->dynamicFormPages->pluck('title', 'id');

            foreach ($request->input('dynamic_form_pages') as $index => $pageData) {

                // Check for duplicate title in new and existing pages
                foreach ($existingPages as $existingId => $existingTitle) {
                    if ((!isset($pageData['id']) || $pageData['id'] != $existingId) &&
                        $pageData['title'] == $existingTitle
                    ) {
                        throw new Exception('Duplicate page title: ' . $pageData['title']);
                    }
                }

                // Update existing DynamicFormPage or create new one
                $formPage = isset($pageData['id'])
                    ? $dynamicForm->dynamicFormPages()->findOrFail($pageData['id'])
                    : $dynamicForm->dynamicFormPages()->create([
                        'title' => $pageData['title'],
                        'sort_no' => $index + 1
                    ]);

                // Update the existingPages array
                $existingPages[$formPage->id] = $formPage->title;

                // Handle DynamicFormFields
                foreach ($pageData['dynamic_form_fields'] as $fieldIndex => $fieldData) {

                    // Generate the input_field_name based on the input_field_label
                    $fieldName = strtolower(trim($fieldData['input_field_label']));
                    $fieldName = preg_replace('/\s+/', '_', $fieldName); // Replace spaces with underscores

                    $formPage->dynamicFormFields()->updateOrCreate(
                        ['id' => $fieldData['id']],
                        [
                            'input_field_label' => $fieldData['input_field_label'],
                            'input_field_name' => $fieldName, // Use the generated field name
                            'input_field_type' => $fieldData['input_field_type'],
                            'is_required' => $fieldData['is_required'],
                            'sort_no' => $fieldIndex + 1, // Use the loop index for sorting
                            'data' => $fieldData['data'] ?? null
                        ]
                    );
                }
            }

            DB::commit();

            // For JSON request, return a success response
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Dynamic form updated successfully.',
                ], 201);
            }

            // For non-JSON requests, return an Inertia response
            // Redirect to the desired page and pass the necessary data
            return redirect()->back()->with([
                'message' => 'Dynamic form updated successfully.',
            ]);
       
        } catch (QueryException $ex) {

            // Rollback Transaction
            DB::rollBack();
    
            // Check if it's a data truncation issue
            if (str_contains($ex->getMessage(), 'Data truncated')) {
                $errorMessage = 'Data format error: One or more input field type have data that cannot be saved. Please check the acceptable field type to proceed.';
            } else {
                $errorMessage = $ex->getMessage();
            }
    
            // Handle JSON request
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update dynamic form.',
                    'errors' => $errorMessage
                ], 400);
            }
    
            // Handle non-JSON request
            return redirect()->back()->withErrors([
                'message' => 'Failed to update dynamic form.',
                'errors' => $errorMessage
            ]);

        }  catch (\Exception $e) {
            
            // Rollback Transaction
            DB::rollBack();

            // For JSON request, return a success response
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update dynamic form.',
                    'errors' => $e->getMessage()
                ], 404);
            }

            // For non-JSON requests, return an Inertia response
            // Redirect to the desired page and pass the necessary data
            return redirect()->back()->withErrors([
                'message' => 'Failed to update dynamic form.',
                'errors' => $e->getMessage()
            ]);
        }
    }
}
