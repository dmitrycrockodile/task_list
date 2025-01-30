<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;

class TaskController extends Controller
{
    /**
     * Display a listing of the user tasks filtered by "is_completed" status.
     * 
     * @authenticated
     * 
     * @response 200 {
     *    "data": [
     *        {
     *            "id": 1,
     *            "name": "Task Name",
     *            "description": "Task Description",
     *            "is_completed": false,
     *            "user_id": 1
     *        },
     *        {
     *            "id": 2,
     *            "name": "Another Task",
     *            "description": "Another Task Description",
     *            "is_completed": true,
     *            "user_id": 2
     *        }
     *    ],
     *    "success": true,
     *    "message": "Tasks retrieved successfully."
     * }
     * 
     * @response 500 {
     *    "data": "Full error message",
     *    "success": false,
     *    "message": "Failed to retrieve tasks."
     * }
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $tasks = Task::with('user')
                         ->join('users', 'tasks.user_id', '=', 'users.id')
                         ->orderBy('users.id')
                         ->orderBy('tasks.is_completed')
                         ->select('tasks.*')
                         ->get();

            return $this->successResponse($tasks, 'Tasks retrieved successfully.');
        } catch (QueryException $e) {
            return $this->errorResponse('Database error.', 'Failed to retrieve tasks.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Failed to retrieve tasks.');
        }
    }

    /**
     * Store a newly created task in storage.
     * 
     * @authenticated
     * 
     * @bodyParam name string required The name of the task. Example: "Buy groceries"
     * @bodyParam description string required The description of the task. Example: "Buy milk, eggs, and bread"
     *
     * @response 201 {
     *    "data": {
     *        "id": 1,
     *        "name": "Buy groceries",
     *        "description": "Buy milk, eggs, and bread",
     *        "is_completed": false,
     *        "user_id": 1
     *    },
     *    "success": true,
     *    "message": "Task created successfully."
     * }
     * 
     * @response 400 {
     *    "success": false,
     *    "message": "Validation error: The 'name' field is required."
     * }
     *
     * @response 500 {
     *    "success": false,
     *    "message": "Failed to create the task."
     * }
     * 
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $task = Task::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'is_completed' => false,
                'user_id' => auth()->id()
            ]);

            return $this->successResponse($task, 'Task created successfully.', Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return $this->errorResponse('Database error.', 'Failed to create the task.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Failed to create the task.');
        }
    }

    /**
     * Update the specified task in storage.
     * 
     * @authenticated
     * 
     * @bodyParam name string The name of the task. Example: "Buy groceries"
     * @bodyParam description string The description of the task. Example: "Buy milk, eggs, and bread"
     * @bodyParam is_completed boolean The completion status of the task. Example: true
     *
     * @response 200 {
     *    "data": {
     *        "id": 1,
     *        "name": "Buy groceries",
     *        "description": "Buy milk, eggs, and bread",
     *        "is_completed": true,
     *        "user_id": 1
     *    },
     *    "success": true,
     *    "message": "Task updated successfully."
     * }
     *
     * @response 400 {
     *    "success": false,
     *    "message": "Validation error: The 'name' field is required."
     * }
     *
     * @response 404 {
     *    "success": false,
     *    "message": "Task not found."
     * }
     *
     * @response 500 {
     *    "success": false,
     *    "message": "Failed to update the task."
     * }
     * 
     * @param UpdateRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        try {
            $task->update($validated);

            return $this->successResponse($task, 'Task updated successfully.');
        } catch (QueryException $e) {
            return $this->errorResponse('Database error.', 'Failed to update the task.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Failed to update the task.');
        }
    }

    /**
     * Update the specified task's "is_completed" status to true in storage.
     * 
     * @authenticated
     *
     * @response 200 {
     *    "data": {
     *        "id": 1,
     *        "name": "Buy groceries",
     *        "description": "Buy milk, eggs, and bread",
     *        "is_completed": true,
     *        "user_id": 1
     *    },
     *    "success": true,
     *    "message": "Task marked as complete."
     * }
     *
     * @response 404 {
     *    "success": false,
     *    "message": "Task not found."
     * }
     *
     * @response 500 {
     *    "success": false,
     *    "message": "Failed to update the task."
     * }
     * 
     * @param Task $task
     * @return JsonResponse
     */
    public function markComplete(Task $task): JsonResponse 
    {
        $this->authorize('update', $task);

        try {
            $task->is_completed = true;
            $task->save();

            return $this->successResponse($task, 'Task marked as complete.');
        } catch (QueryException $e) {
            return $this->errorResponse('Database error.', 'Failed to update the task.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Failed to update the task.');
        }
    }

    /**
     * Update the specified task's "is_completed" status to false in storage.
     * 
     * @authenticated
     *
     * @response 200 {
     *    "data": {
     *        "id": 1,
     *        "name": "Buy groceries",
     *        "description": "Buy milk, eggs, and bread",
     *        "is_completed": false,
     *        "user_id": 1
     *    },
     *    "success": true,
     *    "message": "Task marked as incomplete."
     * }
     *
     * @response 404 {
     *    "success": false,
     *    "message": "Task not found."
     * }
     *
     * @response 500 {
     *    "success": false,
     *    "message": "Failed to update the task."
     * }
     * 
     * @param Task $task
     * @return JsonResponse
     */
    public function markIncomplete(Task $task): JsonResponse 
    {
        $this->authorize('update', $task);

        try {
            $task->is_completed = false;
            $task->save();

            return $this->successResponse($task, 'Task marked as incomplete.');
        } catch (QueryException $e) {
            return $this->errorResponse('Database error.', 'Failed to update the task.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Failed to update the task.');
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @authenticated
     *
     * @response 200 {
     *    "success": true,
     *    "message": "Task deleted successfully."
     * }
     *
     * @response 404 {
     *    "success": false,
     *    "message": "Task not found."
     * }
     *
     * @response 500 {
     *    "success": false,
     *    "message": "Failed to delete the task."
     * }
     * 
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task); 

        try {
            $task->delete();


            return $this->successResponse([], 'Task deleted successfully.');
        } catch (QueryException $e) {
            return $this->errorResponse('Database error.', 'Failed to delete the task.');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 'Failed to delete the task.');
        }
    }

    /**
     * Common method for successful responses
     * 
     * @param mixed $data The data to be included in the response
     * @param string $message The success message
     * @param int $statusCode The HTTP status code (default is 200 OK)
     * @return JsonResponse
     */
    protected function successResponse($data, string $message, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'data' => $data ?: null,
            'success' => true,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Common method for error responses
     * 
     * @param mixed $error The exception details
     * @param string $message The error message
     * @param int $statusCode The HTTP status code (default is 500 Internal Server Error)
     * @return JsonResponse
     */
    protected function errorResponse($error, string $message, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return response()->json([
            'error' => $error,
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}