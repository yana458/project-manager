<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectService extends Model
{
    public const STATUSES = [
        'pending',
        'in_progress',
        'completed',
        'blocked',
    ];

    protected $table = 'project_service';

    protected $fillable = [
        'project_id',
        'service_id',
        'start_date',
        'due_date',
        'status',
        'notes',
        'assigned_user_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}