<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $roles = ['admin', 'employee'];

        return [
            'email' => $this->faker->unique()->safeEmail(),
            'roles' => [$this->faker->randomElement($roles)],
            'is_active' => $this->faker->boolean,
            'last_activity' => now(),
            'password' => Hash::make('password'),
        ];
    }

    /**
     * State untuk admin.
     */
    public function admin(): static
    {
        return $this->state(fn() => [
            'roles' => ['admin'],
        ]);
    }

    /**
     * State untuk employee.
     */
    public function employee(): static
    {
        return $this->state(fn() => [
            'roles' => ['employee'],
        ]);
    }

    /**
     * State untuk multi-role (admin & employee).
     */
    public function adminEmployee(): static
    {
        return $this->state(fn() => [
            'roles' => ['admin', 'employee'],
        ]);
    }
}
