<?php



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SuperAdminBusinessController extends Controller
{
    // Listar todos los comercios
    public function index()
    {
        $businesses = Business::withCount('services')
            ->with('owner:id,name,email,business_id')
            ->get();

        return response()->json([
            'businesses' => $businesses->map(fn ($business) => [
                'id' => $business->id,
                'name' => $business->name,
                'business_name' => $business->name,
                'slug' => $business->slug,
                'email' => $business->email,
                'description' => $business->description,
                'image_url' => $business->image_url,
                'services_count' => $business->services_count,
                'admin_name' => $business->owner?->name,
                'admin_email' => $business->owner?->email,
            ]),
        ]);
    }

    // Consultar un comercio específico
    public function show($id)
    {
        $business = Business::with('owner:id,name,email,business_id')
            ->findOrFail($id);

        return response()->json([
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'business_name' => $business->name,
                'slug' => $business->slug,
                'email' => $business->email,
                'description' => $business->description,
                'image_url' => $business->image_url,
                'admin_name' => $business->owner?->name,
                'admin_email' => $business->owner?->email,
            ],
        ]);
    }

    // Registrar un comercio y su administrador
    public function store(Request $request)
    {
        $data = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8'],
        ]);

        $business = DB::transaction(function () use ($data) {
            $business = Business::create([
                'name' => $data['business_name'],
                'email' => $data['email'] ?? null,
                'description' => $data['description'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'slug' => Str::slug($data['business_name']) . '-' . Str::random(6),
            ]);

            User::create([
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($data['admin_password']),
                'role' => 'admin_business',
                'business_id' => $business->id,
            ]);

            return $business;
        });

        return response()->json([
            'message' => 'Comercio registrado correctamente.',
            'business' => $business,
        ], 201);
    }

    // Actualizar un comercio y sus datos de administrador
    public function update(Request $request, $id)
    {
        $business = Business::with('owner')->findOrFail($id);
        $owner = $business->owner;

        // Aceptar tanto "name" como "business_name".
        $request->merge([
            'name' => $request->input('name', $request->input('business_name')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($owner?->id),
            ],
            'admin_password' => ['nullable', 'string', 'min:8'],
        ]);

        DB::transaction(function () use ($business, $owner, $data) {
            $business->update([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'description' => $data['description'] ?? null,
                'image_url' => $data['image_url'] ?? null,
                'slug' => $business->name !== $data['name']
                    ? Str::slug($data['name']) . '-' . Str::random(6)
                    : $business->slug,
            ]);

            if ($owner) {
                $owner->update([
                    'name' => $data['admin_name'],
                    'email' => $data['admin_email'] ?: $owner->email,
                    ...(!empty($data['admin_password'])
                        ? ['password' => Hash::make($data['admin_password'])]
                        : []),
                ]);
            }
        });

        return response()->json([
            'message' => 'Comercio actualizado correctamente.',
        ]);
    }
}
