<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace Http\Controllers;

use App\Enums\ListSharePermission;
use App\Models\TaskList;
use App\Models\User;
use Faker\Provider\Uuid;
use Tests\TestCase;

class ListShareControllerTest extends TestCase
{
    public function test_can_share_task_list()
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner, 'sanctum');
        $response = $this->postJson("/api/task-lists/{$taskList->uuid}/share", [
            'task_list_id' => $taskList->uuid,
            'users' => [$sharedUser->uuid],
            'permission' => ListSharePermission::EDIT
        ]);

        $response->assertStatus(200);

        $response->assertJson(['message' => 'Task list shared successfully']);

        $this->assertDatabaseHas('list_shares', [
            'task_list_id' => $taskList->id,
            'user_id' => $sharedUser->id,
            'permission_type' => 'edit'
        ]);
    }

    public function test_can_view_users_shared_with()
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);
        $taskList->sharedWith()->attach($sharedUser->id, ['permission_type' => ListSharePermission::VIEW]);

        $this->actingAs($owner, 'sanctum');
        $response = $this->getJson("/api/task-lists/{$taskList->uuid}/shared-with");

        $response->assertStatus(200);
        $sharedUser->refresh();
        $response->assertJson(['data' => [
            [
                'id' => $taskList->sharedWith->first()->pivot->uuid,
                'task_list' => [
                    'id' => $taskList->uuid,
                    'title' => $taskList->title,
                    'created_at' => $taskList->created_at,
                ],
                'user' => [
                    'id' => $sharedUser->uuid,
                    'name' => $sharedUser->name,
                    'email' => $sharedUser->email,
                ],
                'permission' => 'view'
            ]
        ]]);
    }

    public function test_cannot_share_other_users_list()
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($sharedUser, 'sanctum');
        $response = $this->postJson("/api/task-lists/{$taskList->uuid}/share", [
            'task_list_id' => $taskList->uuid,
            'users' => [$sharedUser->uuid],
            'permission' => ListSharePermission::EDIT
        ]);

        $response->assertStatus(404); // 404 As global scope will filter out the task list
    }

    public function test_cannot_share_task_list_with_invalid_permission()
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner, 'sanctum');
        $response = $this->postJson("/api/task-lists/{$taskList->uuid}/share", [
            'task_list_id' => $taskList->uuid,
            'users' => [$sharedUser->uuid],
            'permission' => 'invalid-permission'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('permission');
    }

    public function test_cannot_share_task_list_with_invalid_user()
    {
        $owner = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner, 'sanctum');
        $response = $this->postJson("/api/task-lists/{$taskList->uuid}/share", [
            'task_list_id' => $taskList->uuid,
            'users' => [Uuid::uuid()],
            'permission' => ListSharePermission::EDIT
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('users.0');
    }

    public function test_can_access_task_shared_with_user()
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);
        $taskList->sharedWith()->attach($sharedUser->id, ['permission_type' => ListSharePermission::VIEW]);

        $this->actingAs($sharedUser, 'sanctum');
        $response = $this->getJson("/api/task-lists/{$taskList->uuid}");

        $response->assertStatus(200);
        $response->assertJson(['data' => [
            'id' => $taskList->uuid,
            'title' => $taskList->title,
            'created_at' => $taskList->created_at,
        ]]);
    }

    public function test_cannot_edit_task_shared_with_user_as_view_only()
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);
        $taskList->sharedWith()->attach($sharedUser->id, ['permission_type' => ListSharePermission::VIEW]);

        $this->actingAs($sharedUser, 'sanctum');
        $response = $this->putJson("/api/task-lists/{$taskList->uuid}", [
            'title' => 'Updated Title'
        ]);

        $response->assertStatus(403);
    }

    public function test_can_edit_task_shared_with_user_as_edit()
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);
        $taskList->sharedWith()->attach($sharedUser->id, ['permission_type' => ListSharePermission::EDIT]);

        $this->actingAs($sharedUser, 'sanctum');
        $response = $this->putJson("/api/task-lists/{$taskList->uuid}", [
            'title' => 'Updated Title'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Task list updated successfully']);
    }

    public function test_un_share_shared_task_list()
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);
        $taskList->sharedWith()->attach($sharedUser->id, ['permission_type' => ListSharePermission::VIEW]);

        $this->actingAs($owner, 'sanctum');
        $response = $this->postJson("/api/task-lists/{$taskList->uuid}/un-share", [
            'users' => [$sharedUser->uuid],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Task list unshared successfully']);

        $this->assertDatabaseMissing('list_shares', [
            'task_list_id' => $taskList->id,
            'user_id' => $sharedUser->id,
        ]);

        // task should not be accessible to shared user now
        $this->actingAs($sharedUser, 'sanctum');

        $response = $this->getJson("/api/task-lists/{$taskList->uuid}");
        $response->assertStatus(404);
    }

    public function test_can_update_permission_of_shared_task_with_user()
    {
        $owner = User::factory()->create();
        $sharedUser = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);
        $taskList->sharedWith()->attach($sharedUser->id, ['permission_type' => ListSharePermission::EDIT]);

        $this->actingAs($owner, 'sanctum');
        $response = $this->putJson("/api/task-lists/{$taskList->uuid}/update-permission", [
            'user_id' => $sharedUser->uuid,
            'permission' => ListSharePermission::VIEW
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Permission updated successfully']);

        $this->assertDatabaseHas('list_shares', [
            'task_list_id' => $taskList->id,
            'user_id' => $sharedUser->id,
            'permission_type' => 'view'
        ]);

        // shared user should not be able to edit the task list now
        $this->actingAs($sharedUser, 'sanctum');

        $response = $this->putJson("/api/task-lists/{$taskList->uuid}", [
            'title' => 'Updated Title'
        ]);

        $response->assertStatus(403);
    }
}
