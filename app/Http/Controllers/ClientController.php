<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $clients = Client::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('tax_id', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', compact('clients', 'search'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateClient($request);

        Client::create($this->buildClientData($validated));

        return redirect()
            ->route('clients.index')
            ->with('success', 'Client created.');
    }

    public function show(Client $client)
    {
        $client->load([
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
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $this->validateClient($request, $client);

        $client->update($this->buildClientData($validated));

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Client updated.');
    }

    public function deactivate(Client $client)
    {
        if (!$client->is_active) {
            return back()->with('success', 'Client is already inactive.');
        }

        $client->update(['is_active' => false]);

        return back()->with('success', 'Client deactivated.');
    }

    public function activate(Client $client)
    {
        if ($client->is_active) {
            return back()->with('success', 'Client is already active.');
        }

        $client->update(['is_active' => true]);

        return back()->with('success', 'Client activated.');
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
            'social_links' => array_filter([
                'instagram' => $validated['instagram'] ?? null,
                'facebook' => $validated['facebook'] ?? null,
                'linkedin' => $validated['linkedin'] ?? null,
                'tiktok' => $validated['tiktok'] ?? null,
                'x' => $validated['x'] ?? null,
            ]),
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ];
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

            if (!preg_match('~^https?://~i', $value)) {
                $value = 'https://' . ltrim($value, '/');
            }

            $request->merge([
                $field => $value,
            ]);
        }
    }
}