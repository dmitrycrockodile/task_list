<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class TaskControllerTest extends TestCase
{
    /**
     * Test case for verifying that authenticated users can retrieve tasks.
     * 
     * This test checks that:
     * - Authenticated users can retrieve all tasks from the database.
     * - The returned JSON response follows the correct structure, count and status.
     * 
     * @return void
     */
    public function test_can_get_tasks(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $anotherUser = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $anotherUser->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'description', 'is_completed', 'user_id']
            ],
            'success',
            'message'
        ]);
        $response->assertJsonCount(6, 'data');
    }

    /**
     * Test case for verifying that authenticated user can create the task.
     * 
     * This test checks that:
     * - Authenticated user can create the task.
     * - The returned JSON response follows the correct structure and status.
     * 
     * @return void
     */
    public function test_can_create_task()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $taskData = [
            'name' => 'New Task',
            'description' => 'Task description',
        ];

        $response = $this->postJson('/api/tasks', $taskData); 

        $response->assertStatus(201); 
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'is_completed', 'user_id'],
            'success',
            'message'
        ]);
    }

    /**
     * Test case for verifying that authenticated user can update his own task.
     * 
     * This test checks that:
     * - Authenticated user can update the task.
     * - The returned JSON response follows the correct structure and status, the 'name' field has an updated data.
     * 
     * @return void
     */
    public function test_can_update_task()
    {
        $user = User::factory()->create(); 
        $this->actingAs($user); 

        $task = Task::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'name' => 'Updated Task Name',
            'description' => 'Updated Task Description'
        ];

        $response = $this->putJson('/api/tasks/' . $task->id, $updatedData);

        $response->assertStatus(200); 
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'is_completed', 'user_id'],
            'success',
            'message'
        ]);
        $response->assertJsonFragment(['name' => 'Updated Task Name']);
    }

    /**
     * Test case for verifying that authenticated user can mark 'is_completed' field to true in his own task.
     * 
     * This test checks that:
     * - Authenticated user can update the 'is_completed' field in his own task.
     * - The returned JSON response follows the correct structure and status, the 'is_completed' field has an updated data.
     * 
     * @return void
     */
    public function test_user_can_mark_own_task_as_complete ()
    {
        $user = User::factory()->create(); 
        $this->actingAs($user); 

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson('/api/tasks/' . $task->id . '/complete');
        $response->assertStatus(200); 
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'is_completed', 'user_id'],
            'success',
            'message'
        ]);
        $response->assertJsonFragment(['is_completed' => true]);
    }

    /**
     * Test case for verifying that authenticated user can can mark 'is_completed' field to false in his own task.
     * 
     * This test checks that:
     * - Authenticated user can update the 'is_completed' field in his own task.
     * - The returned JSON response follows the correct structure and status, the 'is_completed' field has an updated data.
     * 
     * @return void
     */
    public function test_user_can_mark_own_task_as_incomplete ()
    {
        $user = User::factory()->create(); 
        $this->actingAs($user); 

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->putJson('/api/tasks/' . $task->id . '/incomplete');
        $response->assertStatus(200); 
        $response->assertJsonStructure([
            'data' => ['id', 'name', 'description', 'is_completed', 'user_id'],
            'success',
            'message'
        ]);
        $response->assertJsonFragment(['is_completed' => false]);
    }

    /**
     * Test case for verifying that authenticated user can delete his own task.
     * 
     * This test checks that:
     * - Authenticated user can delete his own task.
     * - The returned JSON response follows the correct structure and status.
     * 
     * @return void
     */
    public function test_can_delete_task() 
    {
        $user = User::factory()->create(); 
        $this->actingAs($user); 

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'success',
            'message',
        ]);
    }

    /**
     * Test case for verifying that authenticated user is only allowed to update his own tasks.
     * 
     * This test checks that:
     * - Authenticated user is only allowed to update his own tasks.
     * - The returned JSON response follows the status 403.
     * 
     * @return void
     */
    public function test_unauthorized_update_task()
    {
        $user = User::factory()->create(); 
        $anotherUser = User::factory()->create(); 
        $this->actingAs($user); 

        $task = Task::factory()->create(['user_id' => $anotherUser->id]); 

        $updatedData = [
            'name' => 'Unauthorized Task Update',
            'description' => 'Trying to update another user\'s task'
        ];

        $response = $this->putJson('/api/tasks/' . $task->id, $updatedData); 

        $response->assertStatus(403); 
    }

    /**
     * Test case for verifying that authenticated user is only allowed to delete his own tasks.
     * 
     * This test checks that:
     * - Authenticated user is only allowed to delete his own tasks.
     * - The returned JSON response follows the status 403.
     * 
     * @return void
     */
    public function test_unauthorized_delete_task()
    {
        $user = User::factory()->create(); 
        $anotherUser = User::factory()->create(); 
        $this->actingAs($user); 

        $task = Task::factory()->create(['user_id' => $anotherUser->id]); 

        $response = $this->deleteJson('/api/tasks/' . $task->id); 

        $response->assertStatus(403); 
    }
}
