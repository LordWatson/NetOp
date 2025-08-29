<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'type',
        'license_number',
        'regulatory_region',
        'billing_tier',
        'contact_email',
        'contact_phone',
        'address',
        'website',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function activeUsers(): HasMany
    {
        return $this->hasMany(User::class)->where('is_active', true);
    }

    // Scopes
    #[Scope]
    protected function active($query)
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function byType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Accessors & Mutators
    public function displayName(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->name . ' (' . strtoupper($this->type) . ')'
        );
    }

    public function isBasicTier(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->billing_tier === 'basic'
        );
    }

    public function isProfessionalTier(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->billing_tier === 'professional'
        );
    }

    public function isEnterpriseTier(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->billing_tier === 'enterprise'
        );
    }

    // Helper methods
    public function canAccessFeature(string $feature): bool
    {
        $featureMap = [
            'basic' => ['monitoring', 'basic_alerts', 'basic_reporting'],
            'professional' => ['monitoring', 'basic_alerts', 'basic_reporting', 'analytics', 'forecasting', 'api_access'],
            'enterprise' => ['monitoring', 'basic_alerts', 'basic_reporting', 'analytics', 'forecasting', 'api_access', 'marketplace', 'advanced_integrations', 'custom_reports'],
        ];

        return in_array($feature, $featureMap[$this->billing_tier] ?? []);
    }

    public function getMaxNetworkNodes(): int
    {
        return match($this->billing_tier) {
            'basic' => 50,
            'professional' => 200,
            'enterprise' => 999999,
            default => 50
        };
    }
}
