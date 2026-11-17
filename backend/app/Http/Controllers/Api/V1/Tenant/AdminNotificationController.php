<?php

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\BusinessNotification;
use Illuminate\Support\Facades\Auth;

class AdminNotificationController extends BaseApiController
{
    public function index()
    {
        $businessId = Auth::user()->business_id;

        if (!$businessId) {
            return $this->sendError('Tu cuenta no tiene un comercio asignado.', [], 403);
        }

        $notifications = BusinessNotification::where('business_id', $businessId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return $this->sendResponse($notifications);
    }

    public function read(int $id)
    {
        $businessId = Auth::user()->business_id;

        if (!$businessId) {
            return $this->sendError('Tu cuenta no tiene un comercio asignado.', [], 403);
        }

        $notification = BusinessNotification::where('business_id', $businessId)->findOrFail($id);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return $this->sendResponse($notification);
    }
}
