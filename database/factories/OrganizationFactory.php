<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $types = ['isp', 'wisp', 'enterprise', 'municipal'];
        $tiers = ['basic', 'professional', 'enterprise'];

        return [
            'name' => fake()->company() . ' ' . fake()->randomElement(['Networks', 'Communications', 'Broadband', 'Internet']),
            'type' => fake()->randomElement($types),
            'license_number' => 'LIC-' . fake()->numerify('######'),
            'regulatory_region' => fake()->randomElement(['UK', 'EU', 'US-CA', 'US-TX', 'US-NY']),
            'billing_tier' => fake()->randomElement($tiers),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'website' => fake()->domainName(),
            'settings' => [
                'notification_preferences' => [
                    'email_alerts' => true,
                    'sms_alerts' => false,
                    'webhook_alerts' => false,
                ],
                'monitoring_interval' => 300, // 5 minutes
                'data_retention_days' => 365,
            ],
            'is_active' => fake()->boolean(95), // 95% chance of being active
        ];
    }

    public function isp(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'isp',
        ]);
    }

    public function wisp(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'wisp',
        ]);
    }

    public function enterprise(): static
    {
        return $this->state(fn (array $attributes) => [
            'billing_tier' => 'enterprise',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
