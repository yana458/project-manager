<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        if ($subCategory = $request->input('sub_category')) {
            $query->where('sub_category', $subCategory);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $services      = $query->orderBy('name')->paginate(10)->withQueryString();
        $categories    = self::getCategories();
        $subCategories = self::getSubCategories();

        return view('services.index', compact('services', 'categories', 'subCategories'));
    }

    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    public function create()
    {
        $categories    = self::getCategories();
        $subCategories = self::getSubCategories();

        return view('services.create', compact('categories', 'subCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255', 'unique:services,name'],
            'category'     => ['nullable', 'string', 'max:100'],
            'sub_category' => ['nullable', 'string', 'max:100'],
            'description'  => ['nullable', 'string', 'max:5000'],
        ]);

        Service::create([
            'name'         => $validated['name'],
            'category'     => $validated['category'] ?? null,
            'sub_category' => $validated['sub_category'] ?? null,
            'description'  => $validated['description'] ?? null,
            'is_active'    => true,
        ]);

        return redirect()->route('services.index')->with('success', 'Service created.');
    }

    public function edit(Service $service)
    {
        $categories    = self::getCategories();
        $subCategories = self::getSubCategories();

        return view('services.edit', compact('service', 'categories', 'subCategories'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255', Rule::unique('services', 'name')->ignore($service->id)],
            'category'     => ['nullable', 'string', 'max:100'],
            'sub_category' => ['nullable', 'string', 'max:100'],
            'description'  => ['nullable', 'string', 'max:5000'],
        ]);

        $service->update([
            'name'         => $validated['name'],
            'category'     => $validated['category'] ?? null,
            'sub_category' => $validated['sub_category'] ?? null,
            'description'  => $validated['description'] ?? null,
        ]);

        return redirect()->route('services.show', $service)->with('success', 'Service updated.');
    }

    public function destroy(Service $service)
    {
        if (! $service->is_active) {
            return back()->with('error', 'The service is already inactive.');
        }

        $service->update(['is_active' => false]);

        return redirect()->route('services.index')->with('success', 'Service deactivated.');
    }

    public function restore(Service $service)
    {
        if ($service->is_active) {
            return back()->with('error', 'The service is already active.');
        }

        $service->update(['is_active' => true]);

        return back()->with('success', 'Service activated.');
    }

    // ── Helpers ───────────────────────────────────────────────────

    private static function getCategories()
    {
        return Service::select('category')
                      ->distinct()
                      ->whereNotNull('category')
                      ->orderBy('category')
                      ->pluck('category');
    }

    private static function getSubCategories()
    {
        return Service::select('sub_category')
                      ->distinct()
                      ->whereNotNull('sub_category')
                      ->orderBy('sub_category')
                      ->pluck('sub_category');
    }
}