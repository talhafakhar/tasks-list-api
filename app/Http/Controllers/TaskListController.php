<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Controllers;

use App\Http\Requests\TaskListRequest;
use App\Http\Resources\TaskListResource;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskListController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', TaskList::class);

        $query = TaskList::query();

        if (request()->query('shared')) {
            $query->whereHas('sharedWith', function ($query) {
                $query->where('id', auth()->id());
            });
        }

        return TaskListResource::collection($query->paginate(10));
    }

    public function store(TaskListRequest $request)
    {
        $this->authorize('create', TaskList::class);

        return response()->json([
            'status' => 'success',
            'message' => 'Task list created successfully',
            'data' => new TaskListResource(TaskList::create([
                'title' => $request->validated('title'),
                'user_id' => auth()->id()
            ]))
        ]);
    }

    public function show(TaskList $taskList)
    {
        $this->authorize('view', $taskList);

        return new TaskListResource($taskList);
    }

    public function update(TaskListRequest $request, TaskList $taskList)
    {
        $this->authorize('update', $taskList);

        $taskList->update($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Task list updated successfully',
            'data' => new TaskListResource($taskList)
        ]);
    }

    public function destroy(TaskList $taskList)
    {
        $this->authorize('delete', $taskList);

        $taskList->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Task list deleted successfully'
        ]);
    }

    public function checkUsername($username)
    {
        return User::where('username', $username)->exists();
    }
}
