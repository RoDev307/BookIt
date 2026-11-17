<?php
namespace App\Http\Controllers\Api\V1\Tenant;
use App\Http\Controllers\Api\BaseApiController;
use App\Models\Client;
use Illuminate\Http\Request;
class AdminClientController extends BaseApiController {
    private function businessId(Request $request): int {
        abort_unless($request->user()->business_id, 403, 'Tu cuenta no tiene un negocio asignado.');
        return $request->user()->business_id;
    }
    public function index(Request $request) {
        $data = $request->validate(['search' => 'nullable|string|max:255', 'page' => 'nullable|integer|min:1']);
        $query = Client::where('business_id', $this->businessId($request));
        $search = trim($data['search'] ?? '');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%');
            });
        }
        return $this->sendResponse($query->withCount('appointments')->orderBy('name')->paginate(20));
    }
    private function fields(Request $request): array {
        $data = $request->validate([
            'name' => 'required|string|max:255', 'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
        ]);
        $data['name'] = trim($data['name']);
        abort_if($data['name'] === '', 422, 'Ingresa el nombre del cliente.');
        $data['phone'] = isset($data['phone']) ? (trim($data['phone']) ?: null) : null;
        $data['email'] = isset($data['email']) ? (trim($data['email']) ?: null) : null;
        return $data;
    }
    public function store(Request $request) {
        $id = $this->businessId($request);
        $client = Client::create([...$this->fields($request), 'business_id' => $id]);
        return $this->sendResponse($client, 'Cliente registrado.', 201);
    }
    public function show(Request $request, int $id) {
        return $this->sendResponse(Client::where('business_id', $this->businessId($request))->withCount('appointments')->findOrFail($id));
    }
    public function update(Request $request, int $id) {
        $client = Client::where('business_id', $this->businessId($request))->findOrFail($id);
        $client->update($this->fields($request));
        return $this->sendResponse($client, 'Ficha actualizada.');
    }
    public function appointments(Request $request, int $id) {
        $client = Client::where('business_id', $this->businessId($request))->findOrFail($id);
        return $this->sendResponse($client->appointments()->where('business_id', $client->business_id)
            ->with('service:id,name,price,duration_minutes')->orderByDesc('appointment_time')->paginate(20));
    }
}
