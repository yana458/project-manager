<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectServiceController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $actor = Auth::user();

        if (! $actor || ! $actor->canManageProjectServicesInstance($project)) {
            abort(403);
        }

        $validated = $request->validate([
            'service_id' => [
                'required',
                'exists:services,id',
                Rule::unique('project_service', 'service_id')->where(
                    fn ($query) => $query->where('project_id', $project->id)
                ),
            ],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(ProjectService::STATUSES)],
            'notes' => ['nullable', 'string'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $serviceAllowed = $project->client
            ->services()
            ->where('services.id', $validated['service_id'])
            ->exists();

        if (! $serviceAllowed) {
            return back()->with('error', 'This service is not contracted by the client.');
        }

        if (! empty($validated['assigned_user_id'])) {
            $userAssignedToProject = $project->users()
                ->where('users.id', $validated['assigned_user_id'])
                ->exists();

            if (! $userAssignedToProject) {
                return back()->with('error', 'Assigned user must belong to the project team.');
            }
        }

        $project->services()->attach($validated['service_id'], [
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'assigned_user_id' => $validated['assigned_user_id'] ?? null,
        ]);

        return back()->with('success', 'Service added to project.');
    }

    public function update(Request $request, Project $project, ProjectService $projectService)
    {
        $actor = Auth::user();

        if (! $actor || ! $actor->canManageProjectServicesInstance($project)) {
            abort(403);
        }

        if ($projectService->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(ProjectService::STATUSES)],
            'notes' => ['nullable', 'string'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ]);

        if (! empty($validated['assigned_user_id'])) {
            $userAssignedToProject = $project->users()
                ->where('users.id', $validated['assigned_user_id'])
                ->exists();

            if (! $userAssignedToProject) {
                return back()->with('error', 'Assigned user must belong to the project team.');
            }
        }

        $projectService->update([
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'assigned_user_id' => $validated['assigned_user_id'] ?? null,
        ]);

        return back()->with('success', 'Project service updated.');
    }

    public function destroy(Project $project, ProjectService $projectService)
    {
        $actor = Auth::user();

        if (! $actor || ! $actor->canManageProjectServicesInstance($project)) {
            abort(403);
        }

        if ($projectService->project_id !== $project->id) {
            abort(404);
        }

        $projectService->delete();

        return back()->with('success', 'Service removed from project.');
    }
}