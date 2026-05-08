<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->can('clients.view'), 403);

        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $projectsFilter = trim((string) $request->query('projects', ''));
        $servicesFilter = trim((string) $request->query('services', ''));

        $clients = Client::query()
            ->withCount(['projects', 'services'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('tax_id', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('whatsapp', 'like', "%{$search}%")
                        ->orWhere('website_url', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->when($projectsFilter === 'with', function ($query) {
                $query->has('projects');
            })
            ->when($projectsFilter === 'without', function ($query) {
                $query->doesntHave('projects');
            })
            ->when($servicesFilter === 'with', function ($query) {
                $query->has('services');
            })
            ->when($servicesFilter === 'without', function ($query) {
                $query->doesntHave('services');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', [
            'clients' => $clients,
            'search' => $search,
            'status' => $status,
            'projectsFilter' => $projectsFilter,
            'servicesFilter' => $servicesFilter,
        ]);
    }

    public function create()
    {
        abort_unless(Auth::user()?->can('clients.create'), 403);

        $servicesCatalog = Service::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('clients.create', [
            'servicesCatalog' => $servicesCatalog,
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()?->can('clients.create'), 403);

        $validated = $this->validateClient($request);

        $client = Client::create($this->buildClientData($validated));

        $client->services()->sync($validated['service_ids'] ?? []);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(Client $client)
    {
        abort_unless(Auth::user()?->can('clients.view'), 403);

        $client->load([
            'projects' => fn ($query) => $query->orderByDesc('id'),
            'services' => fn ($query) => $query->orderBy('name'),
        ]);

        $availableServices = Service::query()
            ->where('is_active', true)
            ->whereDoesntHave('clients', function ($query) use ($client) {
                $query->where('clients.id', $client->id);
            })
            ->orderBy('name')
            ->get();

        return view('clients.show', [
            'client' => $client,
            'availableServices' => $availableServices,
        ]);
    }

    public function edit(Client $client)
    {
        abort_unless(Auth::user()?->can('clients.edit'), 403);

        $client->load('services');

        $servicesCatalog = Service::query()
            ->where(function ($query) use ($client) {
                $query->where('is_active', true)
                    ->orWhereHas('clients', function ($clientQuery) use ($client) {
                        $clientQuery->where('clients.id', $client->id);
                    });
            })
            ->orderBy('name')
            ->get();

        return view('clients.edit', [
            'client' => $client,
            'servicesCatalog' => $servicesCatalog,
        ]);
    }

    public function update(Request $request, Client $client)
    {
        abort_unless(Auth::user()?->can('clients.edit'), 403);

        $validated = $this->validateClient($request, $client);

        $client->update($this->buildClientData($validated));

        $client->services()->sync($validated['service_ids'] ?? []);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function deactivate(Client $client)
    {
        abort_unless(Auth::user()?->can('clients.deactivate'), 403);

        if (! $client->is_active) {
            return back()->with('success', 'El cliente ya estaba inactivo.');
        }

        $client->update(['is_active' => false]);

        return back()->with('success', 'Cliente desactivado correctamente.');
    }

    public function activate(Client $client)
    {
        abort_unless(Auth::user()?->can('clients.deactivate'), 403);

        if ($client->is_active) {
            return back()->with('success', 'El cliente ya estaba activo.');
        }

        $client->update(['is_active' => true]);

        return back()->with('success', 'Cliente activado correctamente.');
    }

    private function validateClient(Request $request, ?Client $client = null): array
    {
        $this->normalizeUrlInputs($request);

        $clientId = $client?->id;

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('clients', 'email')->ignore($clientId),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'tax_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('clients', 'tax_id')->ignore($clientId),
            ],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'website_url' => ['nullable', 'url', 'max:255'],

            'instagram' => ['nullable', 'url', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'tiktok' => ['nullable', 'url', 'max:255'],
            'x' => ['nullable', 'url', 'max:255'],

            'custom_socials' => ['nullable', 'array'],
            'custom_socials.*.name' => ['nullable', 'string', 'max:100'],
            'custom_socials.*.url' => ['nullable', 'url', 'max:255'],

            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],

            'notes' => ['nullable', 'string'],
        ]);
    }

    private function buildClientData(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'company' => $validated['company'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'tax_id' => $validated['tax_id'],
            'whatsapp' => $validated['whatsapp'] ?? null,
            'website_url' => $validated['website_url'] ?? null,
            'social_links' => $this->buildSocialLinks($validated),
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ];
    }

    private function buildSocialLinks(array $validated): array
    {
        $socialLinks = array_filter([
            'instagram' => $validated['instagram'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'linkedin' => $validated['linkedin'] ?? null,
            'tiktok' => $validated['tiktok'] ?? null,
            'x' => $validated['x'] ?? null,
        ], fn ($value) => filled($value));

        foreach ($validated['custom_socials'] ?? [] as $customSocial) {
            $name = trim((string) ($customSocial['name'] ?? ''));
            $url = trim((string) ($customSocial['url'] ?? ''));

            if ($name !== '' && $url !== '') {
                $key = Str::slug($name, '_');
                $socialLinks[$key] = $url;
            }
        }

        return $socialLinks;
    }

    private function normalizeUrlInputs(Request $request): void
    {
        $urlFields = [
            'website_url',
            'instagram',
            'facebook',
            'linkedin',
            'tiktok',
            'x',
        ];

        foreach ($urlFields as $field) {
            $value = trim((string) $request->input($field, ''));

            if ($value === '') {
                continue;
            }

            if (! preg_match('~^https?://~i', $value)) {
                $value = 'https://' . ltrim($value, '/');
            }

            $request->merge([
                $field => $value,
            ]);
        }

        $customSocials = $request->input('custom_socials', []);

        foreach ($customSocials as $index => $customSocial) {
            $url = trim((string) ($customSocial['url'] ?? ''));

            if ($url === '') {
                continue;
            }

            if (! preg_match('~^https?://~i', $url)) {
                $url = 'https://' . ltrim($url, '/');
            }

            $customSocials[$index]['url'] = $url;
        }

        $request->merge([
            'custom_socials' => $customSocials,
        ]);
    }
}