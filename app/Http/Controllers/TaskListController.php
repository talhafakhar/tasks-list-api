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
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskListController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', TaskList::class);

        return TaskListResource::collection(TaskList::all());
    }

    public function store(TaskListRequest $request)
    {
        $this->authorize('create', TaskList::class);

        return new TaskListResource(TaskList::create($request->validated()));
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

        return new TaskListResource($taskList);
    }

    public function destroy(TaskList $taskList)
    {
        $this->authorize('delete', $taskList);

        $taskList->delete();

        return response()->json();
    }
}
