<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $this->denyUnlessCan('projects.view');

        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $myOnly = $request->boolean('my_projects');

        $projects = Project::query()
            ->with('client')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($clientQuery) use ($search) {
                            $clientQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('company', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($myOnly, fn ($query) => $query->where('created_by', Auth::id()))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('projects.index', [
            'projects' => $projects,
            'search' => $search,
            'status' => $status,
            'myOnly' => $myOnly,
            'statuses' => Project::STATUSES,
        ]);
    }

    public function create()
    {
        $this->denyUnlessCan('projects.create');

        $clients = Client::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('projects.create', [
            'clients' => $clients,
            'statuses' => Project::STATUSES,
        ]);
    }

    public function store(Request $request)
    {
        $this->denyUnlessCan('projects.create');

        $validated = $this->validateProject($request);

        Project::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project created.');
    }

    public function show(Project $project)
    {
        $actor = Auth::user();

        if (!$actor instanceof User) {
            abort(403);
        }

        if (!$actor->canViewProjectInstance($project)) {
            abort(403);
        }

        $project->load(['client', 'creator', 'users']);

        $assignableUsers = collect();

        if ($actor->canManageProjectTeamInstance($project)) {
            $assignableUsers = User::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('projects.show', [
            'project' => $project,
            'assignableUsers' => $assignableUsers,
            'teamRoles' => Project::TEAM_ROLES,
        ]);
    }

    public function edit(Project $project)
    {
        $actor = Auth::user();

        if (!$actor instanceof User) {
            abort(403);
        }

        if (!$actor->canEditProjectInstance($project)) {
            abort(403);
        }

        $clients = Client::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('projects.edit', [
            'project' => $project,
            'clients' => $clients,
            'statuses' => Project::STATUSES,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $actor = Auth::user();

        if (!$actor instanceof User) {
            abort(403);
        }

        if (!$actor->canEditProjectInstance($project)) {
            abort(403);
        }

        $validated = $this->validateProject($request);

        $project->update($validated);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated.');
    }

    public function pause(Project $project)
    {
        $actor = Auth::user();

        if (!$actor instanceof User) {
            abort(403);
        }

        if (!$actor->canChangeProjectStatusInstance($project)) {
            abort(403);
        }

        if ($project->status === 'paused') {
            return back()->with('success', 'Project is already paused.');
        }

        if ($project->status === 'finished') {
            return back()->with('error', 'Finished projects cannot be paused.');
        }

        $project->update(['status' => 'paused']);

        return back()->with('success', 'Project paused.');
    }

    public function activate(Project $project)
    {
        $actor = Auth::user();

        if (!$actor instanceof User) {
            abort(403);
        }

        if (!$actor->canChangeProjectStatusInstance($project)) {
            abort(403);
        }

        if ($project->status === 'active') {
            return back()->with('success', 'Project is already active.');
        }

        if ($project->status === 'finished') {
            return back()->with('error', 'Finished projects cannot be reactivated.');
        }

        $project->update(['status' => 'active']);

        return back()->with('success', 'Project reactivated.');
    }

    public function finish(Project $project)
    {
        $actor = Auth::user();

        if (!$actor instanceof User) {
            abort(403);
        }

        if (!$actor->canChangeProjectStatusInstance($project)) {
            abort(403);
        }

        if ($project->status === 'finished') {
            return back()->with('success', 'Project is already finished.');
        }

        $project->update(['status' => 'finished']);

        return back()->with('success', 'Project finished.');
    }

    private function denyUnlessCan(string $permission): void
    {
        $actor = Auth::user();

        if (!$actor || !$actor->can($permission)) {
            abort(403);
        }
    }

    private function validateProject(Request $request): array
    {
        $this->normalizeUrlInputs($request);

        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(Project::STATUSES)],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'staging_url' => ['nullable', 'url', 'max:255'],
            'production_url' => ['nullable', 'url', 'max:255'],
            'docs_url' => ['nullable', 'url', 'max:255'],
        ]);
    }

    private function normalizeUrlInputs(Request $request): void
    {
        $urlFields = ['repo_url', 'staging_url', 'production_url', 'docs_url'];

        foreach ($urlFields as $field) {
            $value = trim((string) $request->input($field, ''));

            if ($value === '') {
                continue;
            }

            if (!preg_match('~^https?://~i', $value)) {
                $value = 'https://' . ltrim($value, '/');
            }

            $request->merge([$field => $value]);
        }
    }
}