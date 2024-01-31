<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ImageUpload;

class ImageUploadController extends BaseApiController
{
    public function upload(Request $request)
    {
        $userId = $request->user()->id; // Use authenticated user ID to view their submissions

        // Define the validation rules for the files
        $validationRules = [
            'images' => 'required', // Ensure that files are provided
            'images.*' => 'mimes:jpg,jpeg,png,gif|max:2048', // Allow only specific file types and size limit (2MB here)
            'user_id' => 'required|exists:users,id', 
            'submission_id' => 'required|exists:dynamic_form_submissions,id', 
        ];

        $request->merge(['user_id' => $userId]);

        // Create a validator instance
        $validator = Validator::make($request->all(), $validationRules);

        // Check if validation fails
        if ($validator->fails()) {
            // Return a response with the validation errors
            return response()->json([
                'message' => 'failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Get uploaded files and ensure it's an array
        $uploadedFiles = $request->file('images');
        if (!is_array($uploadedFiles)) {
            $uploadedFiles = [$uploadedFiles];
        }

        $paths = [];
        $errors = [];

        foreach ($uploadedFiles as $file) {
            try {
                // Generate a unique file name
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

                // Define raw path for the file
                $rawPath = 'uploads/' . $userId .'/'. $request->submission_id .'/';

                // Define the path within the S3 bucket
                $filePath = $rawPath . $fileName; // No trailing slash here, it's the file path

                // Upload to S3
                $result = Storage::disk('s3')->put($filePath, file_get_contents($file));

                if ($result) {
                    // Retrieve the full URL to the uploaded file
                    $paths[] = Storage::disk('s3')->url($filePath);
                } else {
                    // Collect the error message if upload fails
                    $errors[] = "Failed to upload file: {$fileName}";
                }

                // Retrieve the full URL to the uploaded file
                $fileUrl = Storage::disk('s3')->url($filePath);
        
                ImageUpload::create([
                    'user_id' => $userId,
                    'file_name' => $fileName,
                    'file_path' => $rawPath,
                    'file_url' => $fileUrl,
                    'dynamic_form_submission_id' => $request->submission_id
                ]);

            } catch (\Exception $e) {
                // Log the error and collect the exception message
                \Log::error("File upload failed: " . $e->getMessage());
                $errors[] = $e->getMessage();
            }
        }

        // If there were any errors during the upload process, return an error response
        if (!empty($errors)) {
            return $this->sendErrorResponse(['errors' => $errors], 500);
        }

        // If all files were uploaded successfully, return a success response
        return $this->sendSuccessResponse($paths, 'Files uploaded successfully', 200);
    }
}
