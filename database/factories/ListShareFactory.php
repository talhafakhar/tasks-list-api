<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace Database\Factories;

use App\Models\ListShare;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ListShareFactory extends Factory
{
    protected $model = ListShare::class;

    public function definition(): array
    {
        return [
            'permission_type' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'task_list_id' => TaskList::factory(),
            'user_id' => User::factory(),
        ];
    }
}
