<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\NotificationService;
use App\Models\Inventory;

class AuditLogController extends Controller
{
    public function storeInventoryUpdate(Inventory $inventory, array $changes)
    {
        // Create audit log
        AuditLog::create([
            'entity_type' => 'Inventory',
            'entity_id'   => $inventory->id,
            'action'      => 'update',
            'changed_by'  => auth()->id(),
            'changed_at'  => now(),
            'changes'     => json_encode($changes),
        ]);

        // Notify admins
        NotificationService::notify(
            User::role('admin')->pluck('id'),
            'audit',
            "Inventory {$inventory->material_name} updated"
        );

        return response()->json([
            'message' => 'Audit log created and admins notified',
        ]);
    }
}
