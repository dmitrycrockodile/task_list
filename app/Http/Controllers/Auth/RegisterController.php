<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    /**
     * Register the user
     * 
     * @bodyParam name string required The name of the user. Example: "John Doe"
     * @bodyParam email string required The email of the user. Example: "john@example.com"
     * @bodyParam password string required The password for the user. Example: "secretpassword"
     *
     * @response 201 {
     *    "data": "john@example.com",
     *    "success": true,
     *    "message": "User registered successfully."
     * }
     *
     * @response 400 {
     *    "success": false,
     *    "message": "Validation failed."
     * }
     *
     * @response 500 {
     *    "error":  "Error message"
     *    "success": false,
     *    "message": "Failed to register the user."
     * }
     * 
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'data' => $user['email'],
                'success' => true,
                'message' => 'User registered successfully.',
            ], Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Database error.',
                'success' => false,
                'message' => 'Failed to register the user.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'success' => false,
                'message' => 'Failed to register the user.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
