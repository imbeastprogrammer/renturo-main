<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormPageRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormPageRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\ReorderFormPagesRequest;
use App\Models\DynamicFormPage;
use App\Models\DynamicForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

/**
 * @OA\Tag(
 *     name="Admin Dynamic Form Pages",
 *     description="API endpoints for managing dynamic form pages in the admin interface"
 * )
 */
class DynamicFormPageController extends Controller
{
    /**
     * The number of items to show per page
     */
    protected const PER_PAGE = 20;
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/admin/form/pages/all",
     *     tags={"Admin Dynamic Form Pages"},
     *     summary="List all form pages",
     *     description="Get a paginated list of dynamic form pages with their fields and form information",
     *     @OA\Parameter(
     *         name="form_id",
     *         in="query",
     *         description="Filter pages by form ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term to filter pages by title or form name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form pages fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="dynamic_form_id", type="integer"),
     *                         @OA\Property(property="user_id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="sort_no", type="integer"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time"),
     *                         @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                     )
     *                 ),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="per_page", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to fetch form pages."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // Build the query with proper eager loading
            $query = DynamicFormPage::with([
                'dynamicForm' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'user'
            ]);

            // Filter by form if provided
            if ($request->has('form_id')) {
                $query->where('dynamic_form_id', $request->form_id);
            }

            // Filter by search term if provided
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhereHas('dynamicForm', function ($q) use ($searchTerm) {
                          $q->where('name', 'like', "%{$searchTerm}%");
                      });
                });
            }

            // Filter by trashed status if provided
            if ($request->boolean('with_trashed')) {
                $query->withTrashed();
            } elseif ($request->boolean('only_trashed')) {
                $query->onlyTrashed();
            }

            // Order by sort_no within each form
            $query->orderBy('dynamic_form_id')
                  ->orderBy('sort_no');

            // Paginate results
            $formPages = $query->paginate($request->input('per_page', self::PER_PAGE));

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form pages fetched successfully.',
                'data' => $formPages->map(function ($page) {
            return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'sort_no' => $page->sort_no,
                        'form' => [
                            'id' => $page->dynamicForm->id,
                            'name' => $page->dynamicForm->name,
                            'subcategory' => [
                                'id' => $page->dynamicForm->subCategory->id,
                                'name' => $page->dynamicForm->subCategory->name,
                                'category' => [
                                    'id' => $page->dynamicForm->subCategory->category->id,
                                    'name' => $page->dynamicForm->subCategory->category->name
                                ]
                            ]
                        ],
                        'fields_count' => $page->dynamicFormFields->count(),
                        'created_by' => [
                            'id' => $page->user->id,
                            'name' => $page->user->first_name . ' ' . $page->user->last_name
                        ],
                        'created_at' => $page->created_at,
                        'updated_at' => $page->updated_at,
                        'deleted_at' => $page->deleted_at
                    ];
                }),
                'meta' => [
                    'current_page' => $formPages->currentPage(),
                    'from' => $formPages->firstItem(),
                    'last_page' => $formPages->lastPage(),
                    'per_page' => $formPages->perPage(),
                    'to' => $formPages->lastItem(),
                    'total' => $formPages->total(),
                    'filters' => [
                        'search' => $request->search,
                        'form_id' => $request->form_id,
                        'with_trashed' => $request->boolean('with_trashed'),
                        'only_trashed' => $request->boolean('only_trashed')
                    ]
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            // For web interface, return Inertia view
            return Inertia::render('tenants/admin/post-management/dynamic-forms/pages/index', [
                'formPages' => $response['data'],
                'meta' => $response['meta']
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            $error = 'Database error while fetching form pages.';
            $code = 500;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $error = 'One or more related records not found.';
            $code = 404;
        } catch (\Exception $e) {
            $error = 'Failed to fetch form pages.';
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/admin/form/pages",
     *     tags={"Admin Dynamic Form Pages"},
     *     summary="Create a new form page",
     *     description="Create a new dynamic form page with automatic sort order",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dynamic_form_id", "title"},
     *             @OA\Property(property="dynamic_form_id", type="integer", description="ID of the parent form"),
     *             @OA\Property(property="title", type="string", description="Title of the page")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Page created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form page created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="dynamic_form_id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="sort_no", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to create form page."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormPageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFormPageRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create a new page instance
            $page = new DynamicFormPage($request->validated());
            $page->user_id = Auth::id();
            $page->sort_no = $page->getNextSortNumber();

            // Save the page
            $page->save();

            DB::commit();

            // Load relationships for response
            $page->load([
                'dynamicForm' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'user'
            ]);

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form page created successfully.',
                'data' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'sort_no' => $page->sort_no,
                    'form' => [
                        'id' => $page->dynamicForm->id,
                        'name' => $page->dynamicForm->name,
                        'description' => $page->dynamicForm->description,
                        'subcategory' => [
                            'id' => $page->dynamicForm->subCategory->id,
                            'name' => $page->dynamicForm->subCategory->name,
                            'category' => [
                                'id' => $page->dynamicForm->subCategory->category->id,
                                'name' => $page->dynamicForm->subCategory->category->name
                            ]
                        ]
                    ],
                    'fields' => [],  // Empty array for new page
                    'created_by' => [
                        'id' => $page->user->id,
                        'name' => $page->user->first_name . ' ' . $page->user->last_name
                    ],
                    'created_at' => $page->created_at,
                    'updated_at' => $page->updated_at,
                    'deleted_at' => null
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 201);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Check for unique constraint violation
            if ($e->getCode() === '23000') {
                $error = 'A page with this title already exists in this form.';
                $code = 422;
            } else {
                $error = 'Database error while creating form page.';
                $code = 500;
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'The selected form does not exist or has been deleted.';
            $code = 404;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = 'Failed to create form page.';
            $code = 500;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $error,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $code);
        }

        return redirect()->back()
            ->with('error', $error)
            ->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            // Fetch the page with all necessary relationships
            $page = DynamicFormPage::with([
                'dynamicForm' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'user'
            ])->findOrFail($id);

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form page fetched successfully.',
                'data' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'sort_no' => $page->sort_no,
                    'form' => [
                        'id' => $page->dynamicForm->id,
                        'name' => $page->dynamicForm->name,
                        'description' => $page->dynamicForm->description,
                        'subcategory' => [
                            'id' => $page->dynamicForm->subCategory->id,
                            'name' => $page->dynamicForm->subCategory->name,
                            'category' => [
                                'id' => $page->dynamicForm->subCategory->category->id,
                                'name' => $page->dynamicForm->subCategory->category->name
                            ]
                        ]
                    ],
                    'fields' => $page->dynamicFormFields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'label' => $field->input_field_label,
                            'name' => $field->input_field_name,
                            'type' => $field->input_field_type,
                            'required' => $field->is_required,
                            'sort_no' => $field->sort_no,
                            'data' => $field->data,
                            'created_at' => $field->created_at,
                            'updated_at' => $field->updated_at
                        ];
                    }),
                    'created_by' => [
                        'id' => $page->user->id,
                        'name' => $page->user->first_name . ' ' . $page->user->last_name
                    ],
                    'created_at' => $page->created_at,
                    'updated_at' => $page->updated_at,
                    'deleted_at' => $page->deleted_at
                ]
            ];

            if (request()->expectsJson()) {
                return response()->json($response, 200);
            }

            return Inertia::render('tenants/admin/post-management/dynamic-forms/pages/show', [
                'page' => $response['data']
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $error = 'Form page not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            $error = 'Database error while fetching form page.';
            $code = 500;
        } catch (\Exception $e) {
            $error = 'Failed to fetch form page.';
            $code = 500;
        }

        if (request()->expectsJson()) {
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $page = DynamicFormPage::with([
                'dynamicForm.subCategory',
                'dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                }
            ])->findOrFail($id);

            return Inertia::render('tenants/admin/post-management/dynamic-forms/pages/edit', [
                'page' => $page
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load form page for editing.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/admin/form/pages/{id}",
     *     tags={"Admin Dynamic Form Pages"},
     *     summary="Update a form page",
     *     description="Update a dynamic form page with title and sort order management",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the form page",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", description="New title for the page"),
     *             @OA\Property(property="sort_no", type="integer", description="New sort order for the page")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Page updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form page updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="dynamic_form_id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="sort_no", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to update form page."),
     *             @OA\Property(property="error", type="string", example="Form page not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to update form page."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormPageRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFormPageRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $page = DynamicFormPage::findOrFail($id);

            // Store old sort_no for reordering if needed
            $oldPosition = $page->sort_no;

            // Update the page
            $page->update($request->validated());

            // Handle sort_no changes if needed
            if ($request->has('sort_no') && $oldPosition !== $page->sort_no) {
                $page->reorderPages($oldPosition, $page->sort_no);
            }

            DB::commit();

            // Load relationships for response
            $page->load([
                'dynamicForm' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'user'
            ]);

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form page updated successfully.',
                'data' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'sort_no' => $page->sort_no,
                    'form' => [
                        'id' => $page->dynamicForm->id,
                        'name' => $page->dynamicForm->name,
                        'description' => $page->dynamicForm->description,
                        'subcategory' => [
                            'id' => $page->dynamicForm->subCategory->id,
                            'name' => $page->dynamicForm->subCategory->name,
                            'category' => [
                                'id' => $page->dynamicForm->subCategory->category->id,
                                'name' => $page->dynamicForm->subCategory->category->name
                            ]
                        ]
                    ],
                    'fields' => $page->dynamicFormFields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'label' => $field->input_field_label,
                            'name' => $field->input_field_name,
                            'type' => $field->input_field_type,
                            'required' => $field->is_required,
                            'sort_no' => $field->sort_no,
                            'data' => $field->data,
                            'created_at' => $field->created_at,
                            'updated_at' => $field->updated_at
                        ];
                    }),
                    'created_by' => [
                        'id' => $page->user->id,
                        'name' => $page->user->first_name . ' ' . $page->user->last_name
                    ],
                    'created_at' => $page->created_at,
                    'updated_at' => $page->updated_at,
                    'deleted_at' => $page->deleted_at
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Check for unique constraint violation
            if ($e->getCode() === '23000') {
                $error = 'A page with this title already exists in this form.';
                $code = 422;
            } else {
                $error = 'Database error while updating form page.';
                $code = 500;
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'Form page not found.';
            $code = 404;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = 'Failed to update form page.';
            $code = 500;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $error,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $code);
        }

        return redirect()->back()
            ->with('error', $error)
            ->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/admin/form/pages/{id}",
     *     tags={"Admin Dynamic Form Pages"},
     *     summary="Delete a form page",
     *     description="Soft delete a dynamic form page and reorder remaining pages",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the form page to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Page deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form page deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to delete form page."),
     *             @OA\Property(property="error", type="string", example="Form page not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to delete form page."),
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
            DB::beginTransaction();

            // Find the page with relationships for response
            $page = DynamicFormPage::with([
                'dynamicForm' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'user'
            ])->findOrFail($id);

            // Store page data before deletion for response
            $pageData = [
                'id' => $page->id,
                'title' => $page->title,
                'sort_no' => $page->sort_no,
                'form' => [
                    'id' => $page->dynamicForm->id,
                    'name' => $page->dynamicForm->name,
                    'description' => $page->dynamicForm->description,
                    'subcategory' => [
                        'id' => $page->dynamicForm->subCategory->id,
                        'name' => $page->dynamicForm->subCategory->name,
                        'category' => [
                            'id' => $page->dynamicForm->subCategory->category->id,
                            'name' => $page->dynamicForm->subCategory->category->name
                        ]
                    ]
                ],
                'fields' => $page->dynamicFormFields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'label' => $field->input_field_label,
                        'name' => $field->input_field_name,
                        'type' => $field->input_field_type,
                        'required' => $field->is_required,
                        'sort_no' => $field->sort_no,
                        'data' => $field->data
                    ];
                }),
                'created_by' => [
                    'id' => $page->user->id,
                    'name' => $page->user->first_name . ' ' . $page->user->last_name
                ],
                'created_at' => $page->created_at,
                'updated_at' => $page->updated_at
            ];

            // Reorder remaining pages
            $page->reorderPages($page->sort_no);

            // Delete the page (soft delete)
            $page->delete();

            DB::commit();

            // Prepare response with deleted page data
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form page deleted successfully.',
                'data' => array_merge($pageData, [
                    'deleted_at' => now()
                ])
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'Form page not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while deleting form page.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = 'Failed to delete form page.';
            $code = 500;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $error,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $code);
        }

        return redirect()->back()
            ->with('error', $error);
    }

    /**
     * Restore a soft-deleted form page.
     *
     * @OA\Post(
     *     path="/admin/form/pages/restore/{id}",
     *     tags={"Admin Dynamic Form Pages"},
     *     summary="Restore a deleted form page",
     *     description="Restore a soft-deleted dynamic form page and set it as the last page",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the form page to restore",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Page restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form page restored successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="dynamic_form_id", type="integer"),
     *                 @OA\Property(property="user_id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="sort_no", type="integer"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(property="deleted_at", type="null")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to restore form page."),
     *             @OA\Property(property="error", type="string", example="Form page not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to restore form page."),
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
            DB::beginTransaction();

            // Find the page with relationships
            $page = DynamicFormPage::withTrashed()->with([
                'dynamicForm' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicFormFields' => function ($query) {
                    $query->withTrashed()->orderBy('sort_no');
                },
                'user'
            ])->findOrFail($id);

            // Verify that the parent form exists and is not deleted
            if (!$page->dynamicForm || !$page->dynamicForm->exists) {
                throw new \Exception('The parent form no longer exists.');
            }

            // Set the restored page's sort_no to be last
            $page->sort_no = $page->getNextSortNumber();
            $page->save();

            // Restore the page and its fields
            $page->restore();

            // Refresh the model to get updated timestamps
            $page->refresh();

            DB::commit();

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form page restored successfully.',
                'data' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'sort_no' => $page->sort_no,
                    'form' => [
                        'id' => $page->dynamicForm->id,
                        'name' => $page->dynamicForm->name,
                        'description' => $page->dynamicForm->description,
                        'subcategory' => [
                            'id' => $page->dynamicForm->subCategory->id,
                            'name' => $page->dynamicForm->subCategory->name,
                            'category' => [
                                'id' => $page->dynamicForm->subCategory->category->id,
                                'name' => $page->dynamicForm->subCategory->category->name
                            ]
                        ]
                    ],
                    'fields' => $page->dynamicFormFields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'label' => $field->input_field_label,
                            'name' => $field->input_field_name,
                            'type' => $field->input_field_type,
                            'required' => $field->is_required,
                            'sort_no' => $field->sort_no,
                            'data' => $field->data,
                            'created_at' => $field->created_at,
                            'updated_at' => $field->updated_at,
                            'deleted_at' => null
                        ];
                    }),
                    'created_by' => [
                        'id' => $page->user->id,
                        'name' => $page->user->first_name . ' ' . $page->user->last_name
                    ],
                    'created_at' => $page->created_at,
                    'updated_at' => $page->updated_at,
                    'deleted_at' => null
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'Form page not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while restoring form page.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage() === 'The parent form no longer exists.'
                ? 'Cannot restore page: parent form does not exist.'
                : 'Failed to restore form page.';
            $code = $e->getMessage() === 'The parent form no longer exists.' ? 422 : 500;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $error,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $code);
        }

        return redirect()->back()
            ->with('error', $error);
    }

    /**
     * Reorder form pages.
     *
     * @OA\Post(
     *     path="/admin/form/pages/reorder",
     *     tags={"Admin Dynamic Form Pages"},
     *     summary="Reorder form pages",
     *     description="Update the sort order of multiple form pages in a single operation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"form_id", "pages"},
     *             @OA\Property(property="form_id", type="integer", description="ID of the form whose pages are being reordered"),
     *             @OA\Property(
     *                 property="pages",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"id", "sort_no"},
     *                     @OA\Property(property="id", type="integer", description="Page ID"),
     *                     @OA\Property(property="sort_no", type="integer", description="New sort order position")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Pages reordered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Form pages reordered successfully.")
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
     *                 @OA\Property(property="form_id", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="pages", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to reorder form pages."),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     *
     * @param  \App\Http\Requests\Tenants\Admin\FormBuilder\ReorderFormPagesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $formId = $request->get('form_id');
            $includeDeleted = $request->boolean('include_deleted', false);

            $pagesQuery = DynamicFormPage::with([
                'dynamicForm' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                }
            ]);

            if ($formId) {
                $pagesQuery->where('dynamic_form_id', $formId);
            }

            if ($query) {
                $pagesQuery->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                });
            }

            if (!$includeDeleted) {
                $pagesQuery->whereNull('deleted_at');
            }

            $pages = $pagesQuery->orderBy('sort_no')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Form pages retrieved successfully.',
                'data' => $pages
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to search form pages.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reorder(ReorderFormPagesRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            // Verify all pages exist and belong to the same form
            $pages = DynamicFormPage::with([
                'dynamicForm' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'user'
            ])->whereIn('id', collect($validated['pages'])->pluck('id'))
              ->get();

            // Verify all pages belong to the same form
            $formIds = $pages->pluck('dynamic_form_id')->unique();
            if ($formIds->count() !== 1) {
                throw new \Exception('All pages must belong to the same form.');
            }

            // Verify all pages exist
            if ($pages->count() !== count($validated['pages'])) {
                throw new \Exception('One or more pages not found.');
            }

            // Update sort order
            foreach ($validated['pages'] as $pageData) {
                $page = $pages->firstWhere('id', $pageData['id']);
                $page->sort_no = $pageData['sort_no'];
                $page->save();
            }

            DB::commit();

            // Refresh pages with new order
            $pages = DynamicFormPage::with([
                'dynamicForm' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($query) {
                    $query->withTrashed();
                },
                'dynamicFormFields' => function ($query) {
                    $query->orderBy('sort_no');
                },
                'user'
            ])->where('dynamic_form_id', $formIds->first())
              ->orderBy('sort_no')
              ->get();

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Form pages reordered successfully.',
                'data' => [
                    'form' => [
                        'id' => $pages->first()->dynamicForm->id,
                        'name' => $pages->first()->dynamicForm->name,
                        'description' => $pages->first()->dynamicForm->description,
                        'subcategory' => [
                            'id' => $pages->first()->dynamicForm->subCategory->id,
                            'name' => $pages->first()->dynamicForm->subCategory->name,
                            'category' => [
                                'id' => $pages->first()->dynamicForm->subCategory->category->id,
                                'name' => $pages->first()->dynamicForm->subCategory->category->name
                            ]
                        ]
                    ],
                    'pages' => $pages->map(function ($page) {
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
                                    'created_at' => $field->created_at,
                                    'updated_at' => $field->updated_at
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
                    })
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'One or more pages not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while reordering pages.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage() === 'All pages must belong to the same form.'
                ? 'Cannot reorder pages from different forms.'
                : 'Failed to reorder form pages.';
            $code = $e->getMessage() === 'All pages must belong to the same form.' ? 422 : 500;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $error,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], $code);
        }

        return redirect()->back()
            ->with('error', $error);
    }
}
