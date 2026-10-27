<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class SuperAdminBusinessController extends Controller
{
    /**
     * Muestra la lista de todos los comercios en el sistema.
     */
    public function index()
    {
        // Traemos todos los comercios de Aiven contando sus servicios anexados
        $businesses = Business::withCount('services')->get();
        return view('admin.master.businesses.index', compact('businesses'));
    }

    /**
     * Muestra el formulario para registrar un nuevo comercio de manera centralizada.
     */
    public function create()
    {
        return view('admin.master.businesses.create');
    }

    /**
     * Guarda el negocio y le genera su primer usuario Administrador automáticamente.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'image_url'     => ['nullable', 'url', 'max:255'],
            'admin_name'    => ['required', 'string', 'max:255'],
            'admin_email'   => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8'],
        ]);

        // 1. Crear el negocio (Inquilino SaaS)
        $business = Business::create([
            'name'        => $request->business_name,
            'description' => $request->description,
            'image_url'   => $request->image_url,
            'slug'        => Str::slug($request->business_name) . '-' . time(),
        ]);

        // 2. Crear el administrador asociado directamente a ese nuevo negocio
        User::create([
            'name'        => $request->admin_name,
            'email'       => $request->admin_email,
            'password'    => Hash::make($request->admin_password),
            'role'        => 'admin_business',
            'business_id' => $business->id, // Vinculación automática mediante la ID autogenerada
        ]);

        return redirect()->route('master.businesses.index')
            ->with('success', "¡El comercio '{$business->name}' y su administrador fueron creados con éxito desde la consola maestra!");
    }

    /**
     * Muestra el formulario de edición de un comercio específico.
     */
    public function edit($id)
    {
        $business = Business::with('owner')->findOrFail($id);
        return view('admin.master.businesses.edit', compact('business'));
    }

    /**
     * Aplica los cambios a cualquier negocio desde la cuenta maestra.
     */
    public function update(Request $request, $id)
    {
        $business = Business::findOrFail($id);

        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url'   => ['nullable', 'url', 'max:255'],
            'admin_name'  => ['required', 'string', 'max:255'],
        ]);

        $oldName = $business->name;

        // 1. Actualizar datos físicos del comercio
        $business->update([
            'name'        => $request->name,
            'description' => $request->description,
            'image_url'   => $request->image_url,
            'slug'        => $oldName !== $request->name ? Str::slug($request->name) . '-' . time() : $business->slug,
        ]);

        // 2. Actualizar dinámicamente el nombre del usuario administrador vinculado
        if ($business->owner) {
            $business->owner->update([
                'name' => $request->admin_name
            ]);
        }

        return redirect()->route('master.businesses.index')
            ->with('success', "¡El comercio '{$business->name}' y la firma de su administrador fueron actualizados con éxito!");
    }
}
