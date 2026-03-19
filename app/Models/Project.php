<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    public const STATUSES = [
        'active',
        'paused',
        'finished',
    ];

        public const TEAM_ROLES = [
        'manager',
        'editor',
        'viewer',
    ];

    protected $fillable = [
        'client_id',
        'created_by',
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'repo_url',
        'staging_url',
        'production_url',
        'docs_url',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot(['project_role'])
            ->withTimestamps()
            ->orderBy('name');
    }
}