<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace Http\Controllers;


use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    public function testIndex()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $taskList = TaskList::create([
            'title' => 'Test Task List',
            'user_id' => $user->id,
        ]);

        Task::factory()->count(5)->create([
            'task_list_id' => $taskList->id,
        ]);

        Task::factory()->count(5)->create();

        $response = $this->get("/api/task-lists/{$taskList->uuid}/tasks");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'description',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $response->assertJsonCount(5, 'data');
    }

    public function testStore()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $taskList = TaskList::create([
            'title' => 'Test Task List',
            'user_id' => $user->id,
        ]);

        $response = $this->post("/api/task-lists/{$taskList->uuid}/tasks", [
            'description' => 'Test Task',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'description',
                'created_at',
                'updated_at',
            ],
        ]);

        // Fail with no description

        $response = $this->post("/api/task-lists/{$taskList->uuid}/tasks", [], [
            'accept' => 'application/json',
        ]);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'message',
            'errors' => [
                'description',
            ],
        ]);

        // Fail with invalid task list

        $response = $this->post("/api/task-lists/invalid-uuid/tasks", [
            'description' => 'Test Task',
        ]);

        $response->assertStatus(404);
    }

    public function testShow()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $taskList = TaskList::create([
            'title' => 'Test Task List',
            'user_id' => $user->id,
        ]);

        $task = Task::factory()->create([
            'task_list_id' => $taskList->id,
        ]);

        $response = $this->get("/api/task-lists/{$taskList->uuid}/tasks/{$task->uuid}");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'description',
                'created_at',
                'updated_at',
                'task_list',
            ],
        ]);

        // cannot view task from another user's task list

        $user2 = User::factory()->create();

        $this->actingAs($user2, 'sanctum');

        $response = $this->get("/api/tasks/{$task->uuid}");

        $response->assertStatus(404); // 404 As task will get filtered by global scope
    }

    public function testUpdate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $taskList = TaskList::create([
            'title' => 'Test Task List',
            'user_id' => $user->id,
        ]);

        $task = Task::factory()->create([
            'task_list_id' => $taskList->id,
        ]);

        $response = $this->put("/api/task-lists/{$taskList->uuid}/tasks/{$task->uuid}", [
            'description' => 'Updated Task',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'description',
                'created_at',
                'updated_at',
                'task_list',
            ],
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'description' => 'Updated Task',
        ]);

        // Fail with no description

        $response = $this->put("/api/task-lists/{$taskList->uuid}/tasks/{$task->uuid}", [], [
            'accept' => 'application/json',
        ]);

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'message',
            'errors' => [
                'description',
            ],
        ]);

        // cannot update task from another user's task list

        $user2 = User::factory()->create();

        $this->actingAs($user2, 'sanctum');

        $response = $this->put("/api/task-lists/{$taskList->uuid}/tasks/{$task->uuid}", [
            'description' => 'Updated Task',
        ]);

        $response->assertStatus(404); // 404 As task will get filtered by global scope
    }

    public function testDestroy()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $taskList = TaskList::create([
            'title' => 'Test Task List',
            'user_id' => $user->id,
        ]);

        $task = Task::factory()->create([
            'task_list_id' => $taskList->id,
        ]);

        $response = $this->delete("/api/task-lists/{$taskList->uuid}/tasks/{$task->uuid}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);

        // cannot delete task from another user's task list

        $user2 = User::factory()->create();

        $this->actingAs($user2, 'sanctum');

        $response = $this->delete("/api/task-lists/{$taskList->uuid}/tasks/{$task->uuid}");

        $response->assertStatus(404); // 404 As task will get filtered by global scope
    }
}
