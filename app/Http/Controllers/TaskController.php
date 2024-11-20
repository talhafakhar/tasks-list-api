<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function index(TaskList $taskList)
    {
        return TaskResource::collection($taskList->tasks()->latest('created_at')->get());
    }

    public function store(TaskRequest $request, TaskList $taskList)
    {
        $this->authorize('create', [Task::class, $taskList]);

        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully',
            'data' => new TaskResource(Task::create([
                'description' => $request->validated('description'),
                'status' => $request->validated('status') ?? TaskStatus::PENDING,
                'task_list_id' => $taskList->id
            ]))
        ]);
    }

    public function show(TaskList $taskList, Task $task)
    {
        $this->authorize('view', $task);

        return new TaskResource($task);
    }

    public function update(TaskRequest $request, TaskList $taskList, Task $task)
    {
        $this->authorize('update', $task);

        $task->update([
            'description' => $request->validated('description'),
            'status' => $request->validated('status') ?? $task->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Task updated successfully',
            'data' => new TaskResource($task)
        ]);
    }

    public function destroy(TaskList $taskList, Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully'
        ]);
    }
}
