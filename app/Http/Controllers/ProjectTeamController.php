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

        if (!$actor instanceof User || !$actor->canManageProjectTeamInstance($project)) {
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

        $targetUser = User::query()
            ->where('id', $validated['user_id'])
            ->where('is_active', true)
            ->first();

        if (!$targetUser) {
            return back()->with('error', 'Only active users can be assigned to a project.');
        }

        $finalRole = $this->normalizeProjectRole($targetUser, $validated['project_role']);

        $project->users()->attach($targetUser->id, [
            'project_role' => $finalRole,
        ]);

        if ($targetUser->hasProtectedGlobalRole()) {
            return back()->with('success', 'Admin/superadmin assigned to project as manager automatically.');
        }

        return back()->with('success', 'User assigned to project.');
    }

    public function update(Request $request, Project $project, User $user)
    {
        $actor = Auth::user();

        if (!$actor instanceof User || !$actor->canManageProjectTeamInstance($project)) {
            abort(403);
        }

        if (!$project->users()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'That user is not assigned to this project.');
        }

        $validated = $request->validate([
            'project_role' => ['required', Rule::in(Project::TEAM_ROLES)],
        ]);

        if ($user->hasProtectedGlobalRole() && $validated['project_role'] !== 'manager') {
            return back()->with('error', 'Admins and superadmins must always remain manager inside a project.');
        }

        $finalRole = $this->normalizeProjectRole($user, $validated['project_role']);

        $project->users()->updateExistingPivot($user->id, [
            'project_role' => $finalRole,
        ]);

        return back()->with('success', 'Project team member updated.');
    }

    public function destroy(Project $project, User $user)
    {
        $actor = Auth::user();

        if (!$actor instanceof User || !$actor->canManageProjectTeamInstance($project)) {
            abort(403);
        }

        if (!$project->users()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'That user is not assigned to this project.');
        }

        $project->users()->detach($user->id);

        return back()->with('success', 'User removed from project.');
    }

    private function normalizeProjectRole(User $targetUser, string $requestedRole): string
    {
        if ($targetUser->hasProtectedGlobalRole()) {
            return 'manager';
        }

        return $requestedRole;
    }
}