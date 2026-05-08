<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->can('services.view'), 403);

        $search = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', ''));
        $subCategory = trim((string) $request->query('sub_category', ''));
        $status = trim((string) $request->query('is_active', ''));

        $services = Service::query()
            ->withCount(['clients', 'projects'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($category !== '', function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($subCategory !== '', function ($query) use ($subCategory) {
                $query->where('sub_category', $subCategory);
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('is_active', $status === '1');
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = self::getCategories();
        $subCategories = self::getSubCategories();

        return view('services.index', [
            'services' => $services,
            'search' => $search,
            'category' => $category,
            'subCategory' => $subCategory,
            'status' => $status,
            'categories' => $categories,
            'subCategories' => $subCategories,
        ]);
    }

    public function show(Service $service)
    {
        abort_unless(Auth::user()?->can('services.view'), 403);

        $service->loadCount(['clients', 'projects']);

        return view('services.show', compact('service'));
    }

    public function create()
    {
        abort_unless(Auth::user()?->can('services.create'), 403);

        $categories = self::getCategories();
        $subCategories = self::getSubCategories();

        return view('services.create', compact('categories', 'subCategories'));
    }

    public function store(Request $request)
    {
        abort_unless(Auth::user()?->can('services.create'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:services,name'],
            'category' => ['nullable', 'string', 'max:100'],
            'sub_category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        Service::create([
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'sub_category' => $validated['sub_category'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('services.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Service $service)
    {
        abort_unless(Auth::user()?->can('services.edit'), 403);

        $categories = self::getCategories();
        $subCategories = self::getSubCategories();

        return view('services.edit', compact('service', 'categories', 'subCategories'));
    }

    public function update(Request $request, Service $service)
    {
        abort_unless(Auth::user()?->can('services.edit'), 403);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services', 'name')->ignore($service->id),
            ],
            'category' => ['nullable', 'string', 'max:100'],
            'sub_category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $service->update([
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'sub_category' => $validated['sub_category'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('services.show', $service)
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Service $service)
    {
        abort_unless(Auth::user()?->can('services.deactivate'), 403);

        if (! $service->is_active) {
            return back()->with('error', 'El servicio ya está inactivo.');
        }

        $service->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('services.index')
            ->with('success', 'Servicio desactivado correctamente.');
    }

    public function restore(Service $service)
    {
        abort_unless(Auth::user()?->can('services.deactivate'), 403);

        if ($service->is_active) {
            return back()->with('error', 'El servicio ya está activo.');
        }

        $service->update([
            'is_active' => true,
        ]);

        return back()->with('success', 'Servicio activado correctamente.');
    }

    private static function getCategories()
    {
        return Service::query()
            ->select('category')
            ->distinct()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->orderBy('category')
            ->pluck('category');
    }

    private static function getSubCategories()
    {
        return Service::query()
            ->select('sub_category')
            ->distinct()
            ->whereNotNull('sub_category')
            ->where('sub_category', '!=', '')
            ->orderBy('sub_category')
            ->pluck('sub_category');
    }
}