<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

   /**
     * Test case for verifying that the user can login.
     * 
     * This test checks that:
     * - User can login himself.
     * - The returned JSON response follows the correct structure and status.
     * 
     * @return void
     */
    public function test_user_can_login()
    {
        $userData = [
            'name' => 'Test name',
            'email' => 'test@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ];
        $this->postJson('/api/register', $userData);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com', 
            'password' => 'SecurePass123!'
         ]);

        $response->assertStatus(200); 
        $response->assertJsonStructure([
            'user',
            'success',
            'message'
        ]);
        $response->assertJsonFragment(['message' => 'User logged in.']);
    }

    /**
     * Test case for verifying that the user can not login with invalid data.
     * 
     * This test checks that:
     * - User can not login with invalid data.
     * - The returned JSON response follows the correct structure and status.
     * 
     * @return void
     */
    public function test_user_login_fails_with_invalid_data()
    {
        $userData = [
            'email' => 'invalid-email',
            'password' => '123',
        ];

        $response = $this->postJson('/api/login', $userData);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors',
            'message'
        ]);
    }

    /**
     * Test case for verifying that the user can not login with non-existent email.
     * 
     * This test checks that:
     * - User can not login with non-existent email.
     * - The returned JSON response follows the correct structure and status.
     * 
     * @return void
     */
    public function test_user_login_fails_with_not_existent_email()
    {
        $userData = [
            'email' => 'non-existent@email.com',
            'password' => '123asdasd',
        ];

        $response = $this->postJson('/api/login', $userData);
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message',
            'success',
        ]);
        $response->assertJsonFragment(['message' => 'No user with this email found.']);
    }

    /**
     * Test case for verifying that the user can not login with invalid password.
     * 
     * This test checks that:
     * - User can not login with invalid password.
     * - The returned JSON response follows the correct structure and status.
     * 
     * @return void
     */
    public function test_user_login_fails_with_invalid_password()
    {
         $userData = [
            'name' => 'Test name',
            'email' => 'test@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
         ];
         $this->postJson('/api/register', $userData);

         $response = $this->postJson('/api/login', [
            'email' => 'test@example.com', 
            'password' => 'InvalidPass!'
         ]);
         $response->assertStatus(400);
         $response->assertJsonStructure([
               'message',
               'success',
         ]);
         $response->assertJsonFragment(['message' => 'Incorrect password.']);
    }
}