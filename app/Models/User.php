<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'job_title',
        'phone',
        'is_active',
        'timezone',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'preferences' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    // Scopes
    #[Scope]
    protected function active($query)
    {
        return $query->where('is_active', true);
    }

    #[Scope]
    protected function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    #[Scope]
    protected function byOrganization($query, string $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    // Role checking methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isAnalyst(): bool
    {
        return $this->role === 'analyst';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canManageNetwork(): bool
    {
        return in_array($this->role, ['admin', 'operator']);
    }

    public function canViewAnalytics(): bool
    {
        return in_array($this->role, ['admin', 'operator', 'analyst']);
    }

    public function canViewFinancialData(): bool
    {
        return $this->isAdmin();
    }

    // Permission checking
    public function hasPermission(string $permission): bool
    {
        if(!$this->permissions) return false;

        return in_array($permission, $this->permissions);
    }

    public function givePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];

        if(!in_array($permission, $permissions)){
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    public function revokePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_diff($permissions, [$permission]);

        $this->update(['permissions' => array_values($permissions)]);
    }

    // Accessors
    public function displayName(): Attribute
    {
        return new Attribute(
            get: fn ($value) => $this->name . ' (' . ucfirst($this->role) . ')'
        );
    }

    public function initials(): Attribute
    {
        return new Attribute(
            get: function ($value){
                $words = explode(' ', $this->name);
                $initials = '';

                foreach($words as $word){
                    $initials .= strtoupper(substr($word, 0, 1));
                }

                return substr($initials, 0, 2);
            }
        );
    }

    // Update last login timestamp
    public function updateLastLogin(): void
    {
        $this->update(['last_login' => now()]);
    }
}
