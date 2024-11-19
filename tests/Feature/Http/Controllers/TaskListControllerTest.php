<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace Http\Controllers;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskListControllerTest extends \Tests\TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        TaskList::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);
        TaskList::factory()->count(5)->create([
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this->get('/api/task-lists');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'is_shared',
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

        $response = $this->post('/api/task-lists', [
            'title' => 'Test Task List',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertDatabaseHas('task_lists', [
            'title' => 'Test Task List',
        ]);

        // Fail with no title
        $response = $this->post('/api/task-lists', [], [
            'Accept' => 'application/json',
        ]); // Add Accept header to force JSON response
        $response->assertStatus(422);

        $response->assertJsonValidationErrors('title');
    }

    public function testShow()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $taskList = TaskList::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->get("/api/task-lists/{$taskList->uuid}");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'created_at',
                'updated_at',
            ],
        ]);

        $response->assertJson([
            'data' => [
                'id' => $taskList->uuid,
                'title' => $taskList->title,
            ],
        ]);

        // Fail with invalid UUID
        $response = $this->get('/api/task-lists/invalid-uuid');
        $response->assertStatus(404);

        // Fail with another user's task list
        $taskList = TaskList::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);
        $response = $this->get("/api/task-lists/{$taskList->uuid}");

        $response->assertStatus(404); // Should return 404 instead of 403 as global scope filters out the record
    }

    public function testUpdate()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $taskList = TaskList::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->put("/api/task-lists/{$taskList->uuid}", [
            'title' => 'Updated Task List',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertDatabaseHas('task_lists', [
            'uuid' => $taskList->uuid,
            'title' => 'Updated Task List',
        ]);

        // Fail with no title
        $response = $this->put("/api/task-lists/{$taskList->uuid}", [], [
            'Accept' => 'application/json',
        ]); // Add Accept header to force JSON response
        $response->assertStatus(422);

        $response->assertJsonValidationErrors('title');

        // Fail with another user's task list
        $taskList = TaskList::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);
        $response = $this->put("/api/task-lists/{$taskList->uuid}", [
            'title' => 'Updated Task List',
        ]);

        $response->assertStatus(404); // Should return 404 instead of 403 as global scope filters out the record
    }

    public function testDestroy()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $taskList = TaskList::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->delete("/api/task-lists/{$taskList->uuid}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('task_lists', [
            'uuid' => $taskList->uuid,
        ]);

        // Fail with another user's task list
        $taskList = TaskList::factory()->create([
            'user_id' => User::factory()->create()->id,
        ]);
        $response = $this->delete("/api/task-lists/{$taskList->uuid}");

        $response->assertStatus(404); // Should return 404 instead of 403 as global scope filters out the record
    }
}
