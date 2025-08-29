<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        $roles = ['admin', 'operator', 'analyst', 'viewer'];

        return [
            'organization_id' => Organization::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement($roles),
            'permissions' => $this->generatePermissions(),
            'job_title' => fake()->jobTitle(),
            'phone' => fake()->phoneNumber(),
            'is_active' => fake()->boolean(90), // 90% chance of being active
            'timezone' => fake()->timezone(),
            'preferences' => [
                'theme' => fake()->randomElement(['light', 'dark', 'auto']),
                'dashboard_refresh_interval' => fake()->randomElement([30, 60, 300]),
                'default_date_range' => fake()->randomElement(['24h', '7d', '30d']),
                'notifications' => [
                    'browser' => true,
                    'email' => true,
                    'mobile' => false,
                ],
            ],
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'permissions' => [
                'manage_users',
                'manage_network',
                'view_analytics',
                'view_financial_data',
                'manage_billing',
                'access_api',
                'manage_integrations',
            ],
        ]);
    }

    public function operator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'operator',
            'permissions' => [
                'manage_network',
                'view_analytics',
                'acknowledge_alerts',
                'access_monitoring',
            ],
        ]);
    }

    public function analyst(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'analyst',
            'permissions' => [
                'view_analytics',
                'generate_reports',
                'access_api',
                'view_forecasts',
            ],
        ]);
    }

    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'viewer',
            'permissions' => [
                'view_dashboard',
                'view_reports',
            ],
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    private function generatePermissions(): array
    {
        $allPermissions = [
            'manage_users',
            'manage_network',
            'view_analytics',
            'view_financial_data',
            'manage_billing',
            'access_api',
            'manage_integrations',
            'acknowledge_alerts',
            'access_monitoring',
            'generate_reports',
            'view_forecasts',
            'view_dashboard',
            'view_reports',
        ];

        return fake()->randomElements($allPermissions, fake()->numberBetween(2, 6));
    }
}
