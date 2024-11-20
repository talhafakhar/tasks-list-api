<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Controllers;

use App\Http\Requests\ListShareRequest;
use App\Http\Requests\ListUnShareRequest;
use App\Http\Requests\UpdateShareRequest;
use App\Http\Resources\ListShareResource;
use App\Http\Resources\TaskListResource;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ListShareController extends Controller
{
    use AuthorizesRequests;

    public function share(ListShareRequest $request, TaskList $taskList)
    {
        $this->authorize('share', $taskList);

        foreach ($request->validated('users') as $userId) {

            $taskList->sharedWith()->attach(User::findByUuid($userId)->id, ['permission_type' => $request->validated('permission')]);
        }

        return response()->json([
            'message' => 'Task list shared successfully',
            'data' => new TaskListResource($taskList->load('sharedWith'))
        ]);
    }

    public function unShare(ListUnShareRequest $request, TaskList $taskList)
    {
        $this->authorize('unShare', $taskList);

        foreach ($request->validated('users') as $userId) {
            $taskList->sharedWith()->detach(User::findByUuid($userId)->id);
        }

        return response()->json([
            'message' => 'Task list unshared successfully',
            'data' => new TaskListResource($taskList->load('sharedWith'))
        ]);
    }

    public function shared(TaskList $taskList)
    {
        $this->authorize('shared', $taskList);

        return ListShareResource::collection($taskList->sharedWith->sortByDesc('created_at'));
    }

    public function update(UpdateShareRequest $request, TaskList $taskList)
    {
        $this->authorize('updatePermission', $taskList);

        $taskList->sharedWith()->updateExistingPivot(User::findByUuid($request->validated('user_id'))->id, ['permission_type' => $request->validated('permission')]);

        return response()->json([
            'message' => 'Permission updated successfully',
            'data' => new TaskListResource($taskList->load('sharedWith'))
        ]);
    }
}
