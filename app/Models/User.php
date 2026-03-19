<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Project;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    public const DEPARTMENTS = ['development', 'marketing', 'design'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user')
            ->withPivot(['specialty', 'project_role'])
            ->withTimestamps();
    }

    public function hasProjectRole(Project $project, string $role): bool
    {
        return $this->projects()
            ->where('projects.id', $project->id)
            ->wherePivot('project_role', $role)
            ->exists();
    }

    public function isAssignedToProject(Project $project): bool
    {
        return $this->projects()
            ->where('projects.id', $project->id)
            ->exists();
    }

    public function canViewProjectInstance(Project $project): bool
    {
        return $this->can('projects.view') || $this->isAssignedToProject($project);
    }

    public function canEditProjectInstance(Project $project): bool
    {
        return $this->can('projects.edit')
            || $this->hasProjectRole($project, 'manager')
            || $this->hasProjectRole($project, 'editor');
    }

    public function canManageProjectTeamInstance(Project $project): bool
    {
        return $this->can('project_team.manage')
            || $this->hasProjectRole($project, 'manager');
    }

    public function canChangeProjectStatusInstance(Project $project): bool
    {
        return $this->can('projects.status.change')
            || $this->hasProjectRole($project, 'manager');
    }
}