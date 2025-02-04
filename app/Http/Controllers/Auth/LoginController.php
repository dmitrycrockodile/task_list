<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class LoginController extends Controller
{
    /** 
     * Login the user
     * 
     * @bodyParam email string required The email of the user. Example: "john@example.com"
     * @bodyParam password string required The password for the user. Example: "secretpassword"
     * 
     * @response 200 {
     *    "user": ["id" => "1", "email" => "john@example.com"],
     *    "success": true,
     *    "message": "User logged in."
     * }
     * 
     * @response 404 {
     *    "success": false,
     *    "message": "No user with this email found."
     * }
     * 
     * @response 400 {
     *    "success": false,
     *    "message": "Incorrect password."
     * }
     * 
     * @response 500 {
     *    "error":  "Error message"
     *    "success": false,
     *    "message": "Failed to login the user."
     * }
     * 
     * @param LoginRequest $request
     * @return JsonResponse 
    */
    public function login(LoginRequest $request): JsonResponse {
        $validated = $request->validated();

        try {
            $user = User::whereEmail($validated['email'])->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No user with this email found.'
                ], Response::HTTP_NOT_FOUND);
            }

            if (Hash::check($validated['password'], $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                $user->remember_token = $token;
                $user->save();

                Auth::login($user);

                return response()->json([
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email
                    ],
                    'success' => true,
                    'message' => 'User logged in.'
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect password.',
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (QueryException $e) {
            return response()->json([
                'error' => 'Database error.',
                'success' => false,
                'message' => 'Failed to login the user.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'success' => false,
                'message' => 'Failed to login the user.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
