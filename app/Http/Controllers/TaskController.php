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
     */
    public function index()
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
     * Update the specified task's "is_completed" status to true in storage.
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