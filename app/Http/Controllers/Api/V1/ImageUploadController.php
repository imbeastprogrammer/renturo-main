<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\BaseApiController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ImageUploadController extends BaseApiController
{
    public function upload(Request $request)
    {
        // Check if the 'images' key is present and if files are uploaded
        if (!$request->hasFile('images')) {
            return $this->sendErrorResponse('No files provided.', 400);
        }

        // Get uploaded files and ensure it's an array
        $uploadedFiles = $request->file('images');
        if (!is_array($uploadedFiles)) {
            $uploadedFiles = [$uploadedFiles];
        }

        $paths = [];

        foreach ($uploadedFiles as $file) {
            try {
                // Generate a unique file name
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

                // Define the path within the S3 bucket
                $filePath = 'uploads/' . $fileName; // No trailing slash here, it's the file path

                // Upload to S3
                Storage::disk('s3')->put($filePath, file_get_contents($file));

                // Retrieve the full URL to the uploaded file
                $paths[] = Storage::disk('s3')->url($filePath);

            } catch (\Exception $e) {
                // Log the error
                \Log::error("File upload failed: " . $e->getMessage());

                return $this->sendFailedResponse($e->getMessage(), 500);
            }
        }

        return $this->sendSuccessResponse($paths, 'Files uploaded successfully', 200);
    }
}
