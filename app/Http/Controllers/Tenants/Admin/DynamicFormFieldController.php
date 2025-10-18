<?php

namespace App\Http\Controllers\Tenants\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Tenants\Admin\FormBuilder\StoreFormFieldRequest;
use App\Http\Requests\Tenants\Admin\FormBuilder\UpdateFormFieldRequest;
use Illuminate\Support\Facades\DB;
use App\Models\DynamicFormField;
use App\Models\DynamicFormPage;

/**
 * @OA\Schema(
 *     schema="FieldData",
 *     type="object",
 *     description="Field type-specific data",
 *     @OA\Property(property="pattern", type="string", nullable=true, description="Regex pattern for text/textarea fields"),
 *     @OA\Property(property="placeholder", type="string", nullable=true, description="Placeholder text"),
 *     @OA\Property(property="rows", type="integer", example=3, description="Number of rows for textarea"),
 *     @OA\Property(property="cols", type="integer", example=40, description="Number of columns for textarea"),
 *     @OA\Property(property="min", type="number", nullable=true, description="Minimum value for number fields"),
 *     @OA\Property(property="max", type="number", nullable=true, description="Maximum value for number/rating fields"),
 *     @OA\Property(property="step", type="number", example=1, description="Step value for number fields"),
 *     @OA\Property(property="options", type="array", @OA\Items(type="string"), description="Options for select/radio/checklist"),
     *     @OA\Property(property="accept", type="string", example="image/*", description="Accepted file types"),
 *     @OA\Property(property="maxSize", type="integer", example=5242880, description="Maximum file size in bytes"),
 *     @OA\Property(property="depends_on", type="integer", description="ID of the field this field depends on"),
 *     @OA\Property(property="show_if", type="string", example="equals", description="Dependency condition"),
 *     @OA\Property(property="value", type="string", description="Value for dependency condition"),
 *     @OA\Property(property="min_items", type="integer", example=1, description="Minimum items for repeater"),
 *     @OA\Property(property="max_items", type="integer", example=10, description="Maximum items for repeater"),
 *     @OA\Property(property="fields", type="array", @OA\Items(ref="#/components/schemas/RepeaterField"), description="Fields for repeater"),
 *     @OA\Property(property="rows", type="array", @OA\Items(type="string"), description="Row labels for matrix"),
 *     @OA\Property(property="columns", type="array", @OA\Items(type="string"), description="Column labels for matrix"),
 *     @OA\Property(property="country_code", type="string", example="+1", description="Default country code for phone"),
 *     @OA\Property(property="format", type="string", example="###-###-####", description="Phone number format")
 * )
 */

/**
 * @OA\Schema(
 *     schema="RepeaterField",
 *     type="object",
 *     @OA\Property(property="label", type="string", description="Field label"),
 *     @OA\Property(property="type", type="string", description="Field type"),
 *     @OA\Property(property="required", type="boolean", description="Whether the field is required")
 * )
 */

/**
 * @OA\Schema(
 *     schema="DynamicFormFieldResponse",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="label", type="string"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="type", type="string"),
 *     @OA\Property(property="required", type="boolean"),
 *     @OA\Property(property="sort_no", type="integer"),
     *     @OA\Property(property="data", type="object", nullable=true, description="Field-specific data"),
 *     @OA\Property(
 *         property="page",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="title", type="string"),
 *         @OA\Property(
 *             property="form",
 *             type="object",
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(
 *                 property="subcategory",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(
 *                     property="category",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="created_by",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
 * )
 */

/**
 * @OA\Tag(
 *     name="Admin Dynamic Form Fields",
 *     description="API endpoints for managing form fields in the admin interface"
 * )
 */
class DynamicFormFieldController extends Controller
{

    /**
     * Display a listing of the form fields.
     *
     * @OA\Get(
     *     path="/admin/form/fields",
     *     tags={"Admin Dynamic Form Fields"},
     *     summary="List all form fields",
     *     description="Get a paginated list of form fields with their pages and forms",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page_id",
     *         in="query",
     *         description="Filter by page ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by field label or name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="with_trashed",
     *         in="query",
     *         description="Include soft deleted records",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="only_trashed",
     *         in="query",
     *         description="Show only soft deleted records",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form fields fetched successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="dynamic_form_page_id", type="integer"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="input_field_label", type="string"),
     *                     @OA\Property(property="input_field_name", type="string"),
     *                     @OA\Property(
     *                         property="input_field_type",
     *                         type="string",
     *                         enum={
     *                             "heading", "body", "text", "textarea", "number",
     *                             "email", "date", "time", "select", "checkbox",
     *                             "radio", "checklist", "attachment", "rating",
     *                             "password", "multiselect", "file", "hidden",
     *                             "color", "url", "phone", "currency", "matrix",
     *                             "repeater"
     *                         }
     *                     ),
     *                     @OA\Property(property="is_required", type="boolean"),
     *                     @OA\Property(property="sort_no", type="integer"),
     *                     @OA\Property(property="data", type="object", nullable=true, description="Field-specific data"),
     *                     @OA\Property(property="value", type="string", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time"),
     *                     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="from", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="to", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            // Start with a base query
            $query = DynamicFormField::query();

            // Handle soft deletes
            if ($request->boolean('only_trashed')) {
                $query->onlyTrashed();
            } elseif ($request->boolean('with_trashed')) {
                $query->withTrashed();
            }

            // Filter by page ID if provided
            if ($request->filled('page_id')) {
                $query->where('dynamic_form_page_id', $request->page_id);
            }

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('input_field_label', 'like', "%{$search}%")
                      ->orWhere('input_field_name', 'like', "%{$search}%");
                });
            }

            // Load relationships
            $query->with([
                'dynamicFormPage' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPage.dynamicForm' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPage.dynamicForm.subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPage.dynamicForm.subCategory.category' => function ($q) {
                    $q->withTrashed();
                },
                'user'
            ]);

            // Order by page ID and sort number
            $query->orderBy('dynamic_form_page_id')
                  ->orderBy('sort_no');

            // Paginate the results
            $fields = $query->paginate($request->per_page ?? 15);

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form fields fetched successfully.',
                'data' => $fields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'label' => $field->input_field_label,
                        'name' => $field->input_field_name,
                        'type' => $field->input_field_type,
                        'required' => $field->is_required,
                        'sort_no' => $field->sort_no,
                        'data' => $field->data,
                        'page' => [
                            'id' => $field->dynamicFormPage->id,
                            'title' => $field->dynamicFormPage->title,
                            'form' => [
                                'id' => $field->dynamicFormPage->dynamicForm->id,
                                'name' => $field->dynamicFormPage->dynamicForm->name,
                                'subcategory' => [
                                    'id' => $field->dynamicFormPage->dynamicForm->subCategory->id,
                                    'name' => $field->dynamicFormPage->dynamicForm->subCategory->name,
                                    'category' => [
                                        'id' => $field->dynamicFormPage->dynamicForm->subCategory->category->id,
                                        'name' => $field->dynamicFormPage->dynamicForm->subCategory->category->name
                                    ]
                                ]
                            ]
                        ],
                        'created_by' => [
                            'id' => $field->user->id,
                            'name' => $field->user->first_name . ' ' . $field->user->last_name
                        ],
                        'created_at' => $field->created_at,
                        'updated_at' => $field->updated_at,
                        'deleted_at' => $field->deleted_at
                    ];
                }),
                'meta' => [
                    'current_page' => $fields->currentPage(),
                    'from' => $fields->firstItem(),
                    'last_page' => $fields->lastPage(),
                    'per_page' => $fields->perPage(),
                    'to' => $fields->lastItem(),
                    'total' => $fields->total(),
                    'filters' => [
                        'search' => $request->search,
                        'page_id' => $request->page_id,
                        'with_trashed' => $request->boolean('with_trashed'),
                        'only_trashed' => $request->boolean('only_trashed')
                    ]
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Exception $e) {
            $error = 'Failed to fetch form fields.';
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
     * Store a newly created form field.
     *
     * @OA\Post(
     *     path="/admin/form/fields",
     *     tags={"Admin Dynamic Form Fields"},
     *     summary="Create new form fields",
     *     description="Create one or more new form fields for a page",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dynamic_form_page_id", "fields"},
     *             @OA\Property(property="dynamic_form_page_id", type="integer"),
     *             @OA\Property(
     *                 property="fields",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"input_field_label", "input_field_type"},
     *                     @OA\Property(property="input_field_label", type="string"),
     *                     @OA\Property(
     *                         property="input_field_type",
     *                         type="string",
     *                         enum={
     *                             "heading", "body", "text", "textarea", "number",
     *                             "email", "date", "time", "select", "checkbox",
     *                             "radio", "checklist", "attachment", "rating",
     *                             "password", "multiselect", "file", "hidden",
     *                             "color", "url", "phone", "currency", "matrix",
     *                             "repeater"
     *                         }
     *                     ),
     *                     @OA\Property(property="is_required", type="boolean"),
     *                     @OA\Property(property="data", type="object", nullable=true, description="Field-specific data")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Fields created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Dynamic form fields created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="dynamic_form_page_id", type="integer"),
     *                     @OA\Property(property="user_id", type="integer"),
     *                     @OA\Property(property="input_field_label", type="string"),
     *                     @OA\Property(property="input_field_name", type="string"),
     *                     @OA\Property(
     *                         property="input_field_type",
     *                         type="string",
     *                         enum={
     *                             "heading", "body", "text", "textarea", "number",
     *                             "email", "date", "time", "select", "checkbox",
     *                             "radio", "checklist", "attachment", "rating",
     *                             "password", "multiselect", "file", "hidden",
     *                             "color", "url", "phone", "currency", "matrix",
     *                             "repeater"
     *                         }
     *                     ),
     *                     @OA\Property(property="is_required", type="boolean"),
     *                     @OA\Property(property="sort_no", type="integer"),
     *                     @OA\Property(property="data", type="object", nullable=true, description="Field-specific data"),
     *                     @OA\Property(property="value", type="string", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
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
     *                 @OA\Property(
     *                     property="fields.0.input_field_label",
     *                     type="array",
     *                     @OA\Items(type="string", example="The field label is required.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized"
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFormFieldRequest $request)
    {
        try {
            // Verify the page exists and load its relationships
            $page = DynamicFormPage::with([
                'dynamicForm' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicForm.subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($q) {
                    $q->withTrashed();
                }
            ])->findOrFail($request->dynamic_form_page_id);

            // Verify the page's form exists and is not deleted
            if (!$page->dynamicForm || $page->dynamicForm->trashed()) {
                throw new \Exception('The parent form no longer exists or has been deleted.');
            }

            DB::beginTransaction();

            $fields = collect($request->fields)->map(function ($fieldData, $index) use ($request, $page) {
                // Create the field
                $field = DynamicFormField::create([
                    'dynamic_form_page_id' => $page->id,
                    'input_field_label' => $fieldData['input_field_label'],
                    'input_field_name' => $fieldData['input_field_name'],
                    'input_field_type' => $fieldData['input_field_type'],
                    'is_required' => $fieldData['is_required'],
                    'data' => $fieldData['data'] ?? null,
                    'sort_no' => $index + 1,
                    'user_id' => auth()->id()
                ]);

                // Load relationships for the response
                return $field->load([
                    'dynamicFormPage' => function ($q) {
                        $q->withTrashed();
                    },
                    'dynamicFormPage.dynamicForm' => function ($q) {
                        $q->withTrashed();
                    },
                    'user'
                ]);
            });

            DB::commit();

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form fields created successfully.',
                'data' => [
                    'page' => [
                        'id' => $page->id,
                        'title' => $page->title,
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
                        ]
                    ],
                    'fields' => $fields->map(function ($field) {
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
                            'updated_at' => $field->updated_at
                        ];
                    })
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 201);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'Form page not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while creating form fields.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = $e->getMessage() === 'The parent form no longer exists or has been deleted.'
                ? 'Cannot create fields: parent form does not exist or is deleted.'
                : 'Failed to create form fields.';
            $code = $e->getMessage() === 'The parent form no longer exists or has been deleted.' ? 422 : 500;
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update form fields.
     *
     * @OA\Put(
     *     path="/admin/form/fields/{id}",
     *     tags={"Admin Dynamic Form Fields"},
     *     summary="Update form fields",
     *     description="Update multiple form fields for a form page",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Form page ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dynamic_form_page_id","fields"},
     *             @OA\Property(property="dynamic_form_page_id", type="integer"),
     *             @OA\Property(
     *                 property="fields",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"input_field_label","input_field_name","input_field_type","is_required"},
     *                     @OA\Property(property="id", type="integer", nullable=true),
     *                     @OA\Property(property="input_field_label", type="string"),
     *                     @OA\Property(property="input_field_name", type="string"),
     *                     @OA\Property(property="input_field_type", type="string"),
     *                     @OA\Property(property="is_required", type="boolean"),
     *                     @OA\Property(property="data", type="object", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateFormFieldRequest $request, $id)
    {
        try {
            // Verify the page exists and load its relationships
            $page = DynamicFormPage::with([
                'dynamicForm' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicForm.subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicForm.subCategory.category' => function ($q) {
                    $q->withTrashed();
                }
            ])->findOrFail($id);

            // Verify the page's form exists and is not deleted
            if (!$page->dynamicForm || $page->dynamicForm->trashed()) {
                throw new \Exception('The parent form no longer exists or has been deleted.');
            }

            // Verify the page ID in the URL matches the one in the request
            if ($page->id !== (int)$request->dynamic_form_page_id) {
                throw new \Exception('Page ID mismatch.');
            }

            DB::beginTransaction();

            // Get existing field IDs from the request
            $fieldIds = collect($request->fields)
                ->pluck('id')
                ->filter()
                ->values()
                ->all();

            // Soft delete fields not included in the request
            DynamicFormField::where('dynamic_form_page_id', $page->id)
                ->whereNotIn('id', $fieldIds)
            ->delete();
            
            // Update or create fields
            $fields = collect($request->fields)->map(function ($fieldData, $index) use ($page) {
                if (isset($fieldData['id'])) {
                    // Update existing field
                    $field = DynamicFormField::where('id', $fieldData['id'])
                        ->where('dynamic_form_page_id', $page->id)
                        ->firstOrFail();

                    $field->update([
                        'input_field_label' => $fieldData['input_field_label'],
                        'input_field_name' => $fieldData['input_field_name'],
                        'input_field_type' => $fieldData['input_field_type'],
                        'is_required' => $fieldData['is_required'],
                        'data' => $fieldData['data'] ?? null,
                        'sort_no' => $index + 1
                    ]);
                } else {
                    // Create new field
                    $field = DynamicFormField::create([
                        'dynamic_form_page_id' => $page->id,
                        'input_field_label' => $fieldData['input_field_label'],
                        'input_field_name' => $fieldData['input_field_name'],
                        'input_field_type' => $fieldData['input_field_type'],
                        'is_required' => $fieldData['is_required'],
                        'data' => $fieldData['data'] ?? null,
                        'sort_no' => $index + 1,
                        'user_id' => auth()->id()
                    ]);
                }

                // Load relationships for the response
                return $field->load([
                    'dynamicFormPage' => function ($q) {
                        $q->withTrashed();
                    },
                    'dynamicFormPage.dynamicForm' => function ($q) {
                        $q->withTrashed();
                    },
                    'user'
                ]);
            });

            DB::commit();

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form fields updated successfully.',
                'data' => [
                    'page' => [
                        'id' => $page->id,
                        'title' => $page->title,
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
                        ]
                    ],
                    'fields' => $fields->map(function ($field) {
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
                    })
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = $e->getModel() === DynamicFormPage::class
                ? 'Form page not found.'
                : 'One or more fields not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while updating form fields.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = match ($e->getMessage()) {
                'The parent form no longer exists or has been deleted.' => 'Cannot update fields: parent form does not exist or is deleted.',
                'Page ID mismatch.' => 'Page ID in URL does not match the one in request body.',
                default => 'Failed to update form fields.'
            };
            $code = match ($e->getMessage()) {
                'The parent form no longer exists or has been deleted.',
                'Page ID mismatch.' => 422,
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Remove a form field.
     *
     * @OA\Delete(
     *     path="/admin/form/fields/{id}",
     *     tags={"Admin Dynamic Form Fields"},
     *     summary="Delete a form field",
     *     description="Soft delete a form field",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Form field ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request, $id)
    {
        try {
            // Find the field with relationships
            $field = DynamicFormField::with([
                'dynamicFormPage' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPage.dynamicForm' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPage.dynamicForm.subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPage.dynamicForm.subCategory.category' => function ($q) {
                    $q->withTrashed();
                },
                'user'
            ])->findOrFail($id);

            // Store field data before deletion
            $fieldData = [
                'id' => $field->id,
                'label' => $field->input_field_label,
                'name' => $field->input_field_name,
                'type' => $field->input_field_type,
                'required' => $field->is_required,
                'sort_no' => $field->sort_no,
                'data' => $field->data,
                'page' => [
                    'id' => $field->dynamicFormPage->id,
                    'title' => $field->dynamicFormPage->title,
                    'form' => [
                        'id' => $field->dynamicFormPage->dynamicForm->id,
                        'name' => $field->dynamicFormPage->dynamicForm->name,
                        'subcategory' => [
                            'id' => $field->dynamicFormPage->dynamicForm->subCategory->id,
                            'name' => $field->dynamicFormPage->dynamicForm->subCategory->name,
                            'category' => [
                                'id' => $field->dynamicFormPage->dynamicForm->subCategory->category->id,
                                'name' => $field->dynamicFormPage->dynamicForm->subCategory->category->name
                            ]
                        ]
                    ]
                ],
                'created_by' => [
                    'id' => $field->user->id,
                    'name' => $field->user->first_name . ' ' . $field->user->last_name
                ],
                'created_at' => $field->created_at,
                'updated_at' => $field->updated_at
            ];

            // Verify the field's page and form exist
            if (!$field->dynamicFormPage || $field->dynamicFormPage->trashed()) {
                throw new \Exception('The parent page no longer exists or has been deleted.');
            }

            if (!$field->dynamicFormPage->dynamicForm || $field->dynamicFormPage->dynamicForm->trashed()) {
                throw new \Exception('The parent form no longer exists or has been deleted.');
            }

            DB::beginTransaction();

            // Delete the field
            $field->delete();

            // Reorder remaining fields
            DynamicFormField::where('dynamic_form_page_id', $field->dynamic_form_page_id)
                ->where('sort_no', '>', $field->sort_no)
                ->decrement('sort_no');

            DB::commit();

            // Add deletion timestamp to the response
            $fieldData['deleted_at'] = now();

            $response = [
                'status' => 'success',
                'message' => 'Dynamic form field deleted successfully.',
                'data' => $fieldData
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'Form field not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while deleting form field.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = match ($e->getMessage()) {
                'The parent page no longer exists or has been deleted.' => 'Cannot delete field: parent page does not exist or is deleted.',
                'The parent form no longer exists or has been deleted.' => 'Cannot delete field: parent form does not exist or is deleted.',
                default => 'Failed to delete form field.'
            };
            $code = match ($e->getMessage()) {
                'The parent page no longer exists or has been deleted.',
                'The parent form no longer exists or has been deleted.' => 422,
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
     * Restore a soft-deleted form field.
     *
     * @OA\Post(
     *     path="/admin/form/fields/restore/{id}",
     *     tags={"Admin Dynamic Form Fields"},
     *     summary="Restore a deleted form field",
     *     description="Restore a soft-deleted form field",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Form field ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function restore(Request $request, $id)
    {
        try {
            // Find the field with relationships
            $field = DynamicFormField::withTrashed()->with([
                'dynamicFormPage' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPage.dynamicForm' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPage.dynamicForm.subCategory' => function ($q) {
                    $q->withTrashed();
                },
                'dynamicFormPage.dynamicForm.subCategory.category' => function ($q) {
                    $q->withTrashed();
                },
                'user'
            ])->findOrFail($id);

            // Verify the field is actually deleted
            if (!$field->trashed()) {
                throw new \Exception('Field is not deleted.');
            }

            // Verify the field's page and form exist and are not deleted
            if (!$field->dynamicFormPage || $field->dynamicFormPage->trashed()) {
                throw new \Exception('The parent page no longer exists or has been deleted.');
            }

            if (!$field->dynamicFormPage->dynamicForm || $field->dynamicFormPage->dynamicForm->trashed()) {
                throw new \Exception('The parent form no longer exists or has been deleted.');
            }

            DB::beginTransaction();

            // Get the next available sort number
            $maxSort = DynamicFormField::where('dynamic_form_page_id', $field->dynamic_form_page_id)
                ->max('sort_no');
            $field->sort_no = is_null($maxSort) ? 0 : $maxSort + 1;
            $field->save();

            // Restore the field
            $field->restore();

            // Refresh the model to get updated timestamps
            $field->refresh();

            DB::commit();

            // Transform the data for consistent response format
            $response = [
                'status' => 'success',
                'message' => 'Dynamic form field restored successfully.',
                'data' => [
                    'id' => $field->id,
                    'label' => $field->input_field_label,
                    'name' => $field->input_field_name,
                    'type' => $field->input_field_type,
                    'required' => $field->is_required,
                    'sort_no' => $field->sort_no,
                    'data' => $field->data,
                    'page' => [
                        'id' => $field->dynamicFormPage->id,
                        'title' => $field->dynamicFormPage->title,
                        'form' => [
                            'id' => $field->dynamicFormPage->dynamicForm->id,
                            'name' => $field->dynamicFormPage->dynamicForm->name,
                            'subcategory' => [
                                'id' => $field->dynamicFormPage->dynamicForm->subCategory->id,
                                'name' => $field->dynamicFormPage->dynamicForm->subCategory->name,
                                'category' => [
                                    'id' => $field->dynamicFormPage->dynamicForm->subCategory->category->id,
                                    'name' => $field->dynamicFormPage->dynamicForm->subCategory->category->name
                                ]
                            ]
                        ]
                    ],
                    'created_by' => [
                        'id' => $field->user->id,
                        'name' => $field->user->first_name . ' ' . $field->user->last_name
                    ],
                    'created_at' => $field->created_at,
                    'updated_at' => $field->updated_at,
                    'deleted_at' => null
                ]
            ];

            if ($request->expectsJson()) {
                return response()->json($response, 200);
            }

            return redirect()->back()->with('success', $response['message']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            $error = 'Form field not found.';
            $code = 404;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $error = 'Database error while restoring form field.';
            $code = 500;
        } catch (\Exception $e) {
            DB::rollBack();
            $error = match ($e->getMessage()) {
                'Field is not deleted.' => 'Cannot restore field: field is not deleted.',
                'The parent page no longer exists or has been deleted.' => 'Cannot restore field: parent page does not exist or is deleted.',
                'The parent form no longer exists or has been deleted.' => 'Cannot restore field: parent form does not exist or is deleted.',
                default => 'Failed to restore form field.'
            };
            $code = match ($e->getMessage()) {
                'Field is not deleted.',
                'The parent page no longer exists or has been deleted.',
                'The parent form no longer exists or has been deleted.' => 422,
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
}
