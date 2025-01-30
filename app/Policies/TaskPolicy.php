<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     * 
     * @param User $user The user performing the action
     * @param Task $task The task that the user is trying to update
     * @return bool True if the user can update the task, false otherwise
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * 
     * @param User $user The user performing the action
     * @param Task $task The task that the user is trying to delete
     * @return bool True if the user can delete the task, false otherwise
     */
    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }
}
