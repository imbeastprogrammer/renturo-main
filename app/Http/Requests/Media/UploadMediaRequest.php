<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Media;

class UploadMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,gif,webp,mp4,mov,avi,pdf,doc,docx',
                'max:51200', // 50MB max
            ],
            'mediable_type' => [
                'required',
                'string',
                'in:User,Listing,Store,Post,DynamicFormSubmission,Comment',
            ],
            'mediable_id' => [
                'required',
                'integer',
                'min:1',
            ],
            'category' => [
                'required',
                'string',
                'in:profile,cover,post,story,comment,listing,logo,banner,document,attachment,other',
            ],
            'metadata' => [
                'nullable',
                'array',
            ],
            'metadata.caption' => [
                'nullable',
                'string',
                'max:500',
            ],
            'metadata.alt_text' => [
                'nullable',
                'string',
                'max:255',
            ],
            'is_primary' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.mimes' => 'File type not supported. Allowed: images (jpg, png, gif), videos (mp4, mov), documents (pdf, doc).',
            'file.max' => 'File size must not exceed 50MB.',
            'mediable_type.required' => 'Entity type is required.',
            'mediable_type.in' => 'Invalid entity type.',
            'mediable_id.required' => 'Entity ID is required.',
            'category.required' => 'Media category is required.',
            'category.in' => 'Invalid media category.',
        ];
    }
}
