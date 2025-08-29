<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo organizations
        $organizations = [
            [
                'name' => 'Mountain View Wireless',
                'type' => 'wisp',
                'billing_tier' => 'professional',
                'contact_email' => 'admin@mountainview.net',
                'regulatory_region' => 'UK',
                'license_number' => 'OFCOM-12345',
            ],
            [
                'name' => 'Thames Valley Networks',
                'type' => 'isp',
                'billing_tier' => 'enterprise',
                'contact_email' => 'operations@thamesvalley.co.uk',
                'regulatory_region' => 'UK',
                'license_number' => 'OFCOM-67890',
            ],
            [
                'name' => 'City of Oxford Municipal Broadband',
                'type' => 'municipal',
                'billing_tier' => 'professional',
                'contact_email' => 'tech@oxford.gov.uk',
                'regulatory_region' => 'UK',
                'license_number' => 'MUN-OXF-001',
            ],
            [
                'name' => 'Enterprise Connect Solutions',
                'type' => 'enterprise',
                'billing_tier' => 'enterprise',
                'contact_email' => 'noc@enterpriseconnect.com',
                'regulatory_region' => 'UK',
                'license_number' => 'OFCOM-11111',
            ],
        ];

        foreach($organizations as $orgData){
            Organization::create($orgData);
        }

        // Create additional random organizations for testing
        Organization::factory()->count(6)->create();
    }
}
