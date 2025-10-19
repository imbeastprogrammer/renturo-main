<?php

namespace App\Http\Requests\Tenants\Admin\Listings;

use App\Models\Listing;
use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Core Information
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:50',
            
            // Category & Type
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'listing_type' => 'required|string|in:' . implode(',', [
                Listing::TYPE_SPORTS,
                Listing::TYPE_ACCOMMODATION,
                Listing::TYPE_TRANSPORT,
                Listing::TYPE_EVENT_SPACE,
                Listing::TYPE_EQUIPMENT,
                Listing::TYPE_OTHER,
            ]),
            
            // Location
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            
            // Pricing
            'price_per_hour' => 'nullable|numeric|min:0|max:999999.99',
            'price_per_day' => 'nullable|numeric|min:0|max:999999.99',
            'price_per_week' => 'nullable|numeric|min:0|max:999999.99',
            'price_per_month' => 'nullable|numeric|min:0|max:999999.99',
            
            // Capacity & Amenities
            'max_capacity' => 'nullable|integer|min:1|max:10000',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:100',
            
            // Status & Visibility
            'status' => 'nullable|in:' . implode(',', [
                Listing::STATUS_DRAFT,
                Listing::STATUS_ACTIVE,
                Listing::STATUS_INACTIVE,
            ]),
            'visibility' => 'nullable|in:' . implode(',', [
                Listing::VISIBILITY_PUBLIC,
                Listing::VISIBILITY_PRIVATE,
                Listing::VISIBILITY_UNLISTED,
            ]),
            
            // Booking Settings
            'instant_booking' => 'nullable|boolean',
            'minimum_booking_hours' => 'nullable|integer|min:1|max:168', // Max 1 week
            'maximum_booking_hours' => 'nullable|integer|min:1|max:720', // Max 30 days
            'advance_booking_days' => 'nullable|integer|min:1|max:365',
            'cancellation_hours' => 'nullable|integer|min:0|max:168',
            
            // Photos
            'photos' => 'nullable|array|max:10',
            'photos.*' => 'image|mimes:jpeg,jpg,png,webp|max:5120', // Max 5MB per image
            
            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a title for the listing.',
            'description.required' => 'Please provide a description for the listing.',
            'description.min' => 'The description must be at least 50 characters.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'sub_category_id.exists' => 'The selected sub-category is invalid.',
            'address.required' => 'Please provide an address.',
            'city.required' => 'Please provide a city.',
            'province.required' => 'Please provide a province.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'price_per_hour.min' => 'Price cannot be negative.',
            'max_capacity.min' => 'Capacity must be at least 1.',
            'photos.*.image' => 'All files must be images.',
            'photos.*.mimes' => 'Images must be in JPEG, JPG, PNG, or WEBP format.',
            'photos.*.max' => 'Each image must not exceed 5MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'category',
            'sub_category_id' => 'sub-category',
            'price_per_hour' => 'hourly price',
            'price_per_day' => 'daily price',
            'price_per_week' => 'weekly price',
            'price_per_month' => 'monthly price',
            'max_capacity' => 'maximum capacity',
            'minimum_booking_hours' => 'minimum booking hours',
            'maximum_booking_hours' => 'maximum booking hours',
            'advance_booking_days' => 'advance booking days',
            'cancellation_hours' => 'cancellation hours',
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Ensure at least one price is provided
        if (!$this->price_per_hour && !$this->price_per_day && !$this->price_per_week && !$this->price_per_month) {
            $this->validator->errors()->add('price_per_hour', 'At least one pricing option must be provided.');
        }

        // Ensure maximum_booking_hours is greater than minimum_booking_hours
        if ($this->maximum_booking_hours && $this->minimum_booking_hours) {
            if ($this->maximum_booking_hours < $this->minimum_booking_hours) {
                $this->validator->errors()->add('maximum_booking_hours', 'Maximum booking hours must be greater than or equal to minimum booking hours.');
            }
        }
    }
}
