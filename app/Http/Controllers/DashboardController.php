<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectService;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $soon = Carbon::today()->addDays(7);

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return $this->managementDashboard($today, $soon);
        }

        return $this->memberDashboard($user, $today, $soon);
    }

    private function managementDashboard(Carbon $today, Carbon $soon)
    {
        $stats = [
            'clients_total' => Client::count(),
            'clients_active' => Client::where('is_active', true)->count(),
            'projects_active' => Project::where('status', 'active')->count(),
            'projects_paused' => Project::where('status', 'paused')->count(),
            'projects_finished' => Project::where('status', 'finished')->count(),
            'users_active' => User::where('is_active', true)->count(),
            'services_total' => Service::where('is_active', true)->count(),
            'project_services_pending' => ProjectService::where('status', 'pending')->count(),
        ];

        $recentProjects = Project::with('client')
            ->latest('id')
            ->take(8)
            ->get();

        $upcomingDeadlines = Project::with('client')
            ->whereNotNull('end_date')
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $soon)
            ->orderBy('end_date')
            ->take(8)
            ->get();

        $clientsWithoutServices = Client::withCount('services')
            ->having('services_count', '=', 0)
            ->orderBy('name')
            ->take(8)
            ->get();

        $projectsWithoutTeam = Project::withCount('users')
            ->with('client')
            ->having('users_count', '=', 0)
            ->orderByDesc('id')
            ->take(8)
            ->get();

        $actions = [
            ['label' => 'Ver clientes', 'url' => route('clients.index')],
            ['label' => 'Ver proyectos', 'url' => route('projects.index')],
            ['label' => 'Ver usuarios', 'url' => route('users.index')],
            ['label' => 'Ver servicios', 'url' => route('services.index')],
        ];

        return view('dashboards.management', compact(
            'stats',
            'recentProjects',
            'upcomingDeadlines',
            'clientsWithoutServices',
            'projectsWithoutTeam',
            'actions'
        ));
    }

    private function memberDashboard(User $user, Carbon $today, Carbon $soon)
    {
        $baseProjectsQuery = $user->projects()->with('client');

        $myProjects = (clone $baseProjectsQuery)
            ->orderByDesc('projects.id')
            ->get();

        $myProjectsActive = (clone $baseProjectsQuery)
            ->where('projects.status', 'active')
            ->count();

        $myProjectsPaused = (clone $baseProjectsQuery)
            ->where('projects.status', 'paused')
            ->count();

        $myUpcomingDeadlines = (clone $baseProjectsQuery)
            ->whereNotNull('projects.end_date')
            ->whereDate('projects.end_date', '>=', $today)
            ->whereDate('projects.end_date', '<=', $soon)
            ->orderBy('projects.end_date')
            ->get();

        $myOverdueProjects = (clone $baseProjectsQuery)
            ->whereNotNull('projects.end_date')
            ->whereDate('projects.end_date', '<', $today)
            ->whereIn('projects.status', ['active', 'paused'])
            ->orderBy('projects.end_date')
            ->get();

        $myAssignedServicesQuery = method_exists($user, 'assignedProjectServices')
            ? $user->assignedProjectServices()->with(['project.client', 'service'])
            : ProjectService::query()->whereRaw('1 = 0');

        $myAssignedServices = (clone $myAssignedServicesQuery)
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->take(8)
            ->get();

        $myPendingServicesCount = (clone $myAssignedServicesQuery)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        $myBlockedServicesCount = (clone $myAssignedServicesQuery)
            ->where('status', 'blocked')
            ->count();

        $stats = [
            'my_projects_total' => $myProjects->count(),
            'my_projects_active' => $myProjectsActive,
            'my_projects_paused' => $myProjectsPaused,
            'my_pending_services' => $myPendingServicesCount,
            'my_blocked_services' => $myBlockedServicesCount,
            'upcoming_deadlines' => $myUpcomingDeadlines->count(),
            'overdue_projects' => $myOverdueProjects->count(),
        ];

        $actions = [
            ['label' => 'Ver mis proyectos', 'url' => route('projects.index', ['my_projects' => 1])],
        ];

        return view('dashboards.member', compact(
            'user',
            'stats',
            'myProjects',
            'myUpcomingDeadlines',
            'myOverdueProjects',
            'myAssignedServices',
            'actions'
        ));
    }
}