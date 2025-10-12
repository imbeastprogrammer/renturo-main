<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Renturo API Documentation",
 *     description="Comprehensive API documentation for Renturo - Multi-tenant Property Rental Platform. This API serves three main applications: Client App (Property Owners), User App (Renters), and Admin Web Dashboard.",
 *     @OA\Contact(
 *         email="support@renturo.com"
 *     ),
 *     @OA\License(
 *         name="Proprietary",
 *         url="https://renturo.com/license"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://main.renturo.test",
 *     description="Local Development Server (Tenant)"
 * )
 *
 * @OA\Server(
 *     url="http://renturo.test",
 *     description="Local Development Server (Central)"
 * )
 *
 * @OA\Server(
 *     url="https://renturo.ngrok.app",
 *     description="Local Development Server (Mobile via ngrok)"
 * )
 *
 * @OA\Server(
 *     url="https://api.renturo.com",
 *     description="Production API Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="passport",
 *     type="oauth2",
 *     description="Laravel Passport OAuth2 security",
 *     @OA\Flow(
 *         flow="password",
 *         authorizationUrl="/oauth/authorize",
 *         tokenUrl="/oauth/token",
 *         refreshUrl="/oauth/token/refresh",
 *         scopes={}
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     description="Enter your Bearer token in the format: Bearer {token}",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication endpoints (Login, Register, Verify, Password Reset)"
 * )
 *
 * @OA\Tag(
 *     name="Client - Stores",
 *     description="Store/Listing management for Property Owners"
 * )
 *
 * @OA\Tag(
 *     name="Client - Forms",
 *     description="Dynamic form submission for property listings"
 * )
 *
 * @OA\Tag(
 *     name="Client - Categories",
 *     description="Property categories and sub-categories"
 * )
 *
 * @OA\Tag(
 *     name="Client - Banks",
 *     description="Bank account management for owners"
 * )
 *
 * @OA\Tag(
 *     name="Client - Chat",
 *     description="Real-time messaging between users"
 * )
 *
 * @OA\Tag(
 *     name="User - Browse",
 *     description="Browse and search available listings (Renters)"
 * )
 *
 * @OA\Tag(
 *     name="User - Bookings",
 *     description="Booking management for renters"
 * )
 *
 * @OA\Tag(
 *     name="User Management",
 *     description="User profile and settings management"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
