<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ClientServiceController extends Controller
{
    public function store(Request $request, Client $client)
    {
        $actor = Auth::user();

        if (! $actor || ! $actor->can('client_services.manage')) {
            abort(403);
        }

        $validated = $request->validate([
            'service_id' => [
                'required',
                'exists:services,id',
                Rule::unique('client_service', 'service_id')->where(
                    fn ($query) => $query->where('client_id', $client->id)
                ),
            ],
        ]);

        $service = Service::query()
            ->where('id', $validated['service_id'])
            ->where('is_active', true)
            ->first();

        if (! $service) {
            return back()->with('error', 'Only active services can be contracted.');
        }

        $client->services()->attach($service->id);

        return back()->with('success', 'Service contracted for this client.');
    }

    public function destroy(Client $client, Service $service)
    {
        $actor = Auth::user();

        if (! $actor || ! $actor->can('client_services.manage')) {
            abort(403);
        }

        $client->services()->detach($service->id);

        return back()->with('success', 'Service removed from this client.');
    }
}