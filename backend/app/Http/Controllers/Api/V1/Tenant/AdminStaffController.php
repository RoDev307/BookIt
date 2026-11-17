<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminStaffController extends BaseApiController
{
    private function businessId()
    {
        return Auth::user()->business_id;
    }

    private function record($id)
    {
        return Staff::where('business_id', $this->businessId())->findOrFail($id);
    }

    private function validated(Request $request)
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);
    }

    public function index()
    {
        if (!$this->businessId()) {
            return $this->sendError('Tu cuenta no tiene un comercio asignado.', [], 403);
        }

        $staff = Staff::where('business_id', $this->businessId())
            ->orderBy('name')
            ->get();

        return $this->sendResponse($staff);
    }

    public function store(Request $request)
    {
        if (!$this->businessId()) {
            return $this->sendError('Tu cuenta no tiene un comercio asignado.', [], 403);
        }

        $data = $this->validated($request);
        $data['business_id'] = $this->businessId();

        return $this->sendResponse(Staff::create($data), 'Empleado registrado.', 201);
    }

    public function show($id)
    {
        return $this->sendResponse($this->record($id));
    }

    public function update(Request $request, $id)
    {
        $staff = $this->record($id);
        $staff->update($this->validated($request));

        return $this->sendResponse($staff, 'Empleado actualizado.');
    }

    public function destroy($id)
    {
        $this->record($id)->delete();

        return $this->sendResponse(null, 'Empleado eliminado.');
    }
}
