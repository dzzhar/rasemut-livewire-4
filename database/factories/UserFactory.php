
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        $roles = ['admin', 'employee'];

        return [
            'email' => $this->faker->unique()->email(),
            'role' => $this->faker->randomElement($roles),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State helper to create an admin user.
     */
    public function admin()
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
        ]);
    }

    public function user()
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'employee',
        ]);
    }
}
