<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectTeamController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $actor = Auth::user();

        if (!$actor || !$actor->canManageProjectTeamInstance($project)) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('project_user', 'user_id')->where(
                    fn ($query) => $query->where('project_id', $project->id)
                ),
            ],
            'project_role' => ['required', Rule::in(Project::TEAM_ROLES)],
        ]);

        $project->users()->attach($validated['user_id'], [
            'project_role' => $validated['project_role'],
        ]);

        return back()->with('success', 'User assigned to project.');
    }

    public function update(Request $request, Project $project, User $user)
    {
        $actor = Auth::user();

        if (!$actor || !$actor->canManageProjectTeamInstance($project)) {
            abort(403);
        }

        if (!$project->users()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'That user is not assigned to this project.');
        }

        $validated = $request->validate([
            'project_role' => ['required', Rule::in(Project::TEAM_ROLES)],
        ]);

        $project->users()->updateExistingPivot($user->id, [
            'project_role' => $validated['project_role'],
        ]);

        return back()->with('success', 'Project team member updated.');
    }

    public function destroy(Project $project, User $user)
    {
        $actor = Auth::user();

        if (!$actor || !$actor->canManageProjectTeamInstance($project)) {
            abort(403);
        }

        $project->users()->detach($user->id);

        return back()->with('success', 'User removed from project.');
    }
}