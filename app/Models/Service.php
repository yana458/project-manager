<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'sub_category',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_service')
            ->withTimestamps();
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_service')
            ->withPivot([
                'start_date',
                'due_date',
                'status',
                'notes',
                'assigned_user_id',
            ])
            ->withTimestamps();
    }

    public function clientServices(): HasMany
    {
        return $this->hasMany(ClientService::class);
    }

    public function projectServices(): HasMany
    {
        return $this->hasMany(ProjectService::class);
    }
}