<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = Organization::all();

        foreach($organizations as $organization){
            // Create admin user for each organization
            User::factory()
                ->admin()
                ->create([
                    'organization_id' => $organization->id,
                    'name' => 'Admin User',
                    'email' => 'admin@' . strtolower(str_replace([' ', '.'], ['', ''], $organization->name)) . '.test',
                ]);

            // Create operator user
            User::factory()
                ->operator()
                ->create([
                    'organization_id' => $organization->id,
                    'name' => 'Network Operator',
                    'email' => 'operator@' . strtolower(str_replace([' ', '.'], ['', ''], $organization->name)) . '.test',
                ]);

            // Create analyst user for professional/enterprise tiers
            if(in_array($organization->billing_tier, ['professional', 'enterprise'])){
                User::factory()
                    ->analyst()
                    ->create([
                        'organization_id' => $organization->id,
                        'name' => 'Data Analyst',
                        'email' => 'analyst@' . strtolower(str_replace([' ', '.'], ['', ''], $organization->name)) . '.test',
                    ]);
            }

            // Create additional random users
            User::factory()
                ->count(fake()->numberBetween(1, 3))
                ->create(['organization_id' => $organization->id]);
        }

        User::factory()
            ->admin()
            ->create([
                'organization_id' => Organization::first()->id,
                'name' => 'Demo Admin',
                'email' => 'demo@example.com',
                'password' => bcrypt('password'),
            ]);
    }
}
