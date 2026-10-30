<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'timestamp', 'actor', 'role', 'action', 'doc_type', 'doc_id', 'details'
    ];

    public static function record($actor, $role, $action, $docType, $docId = null, $details = null)
    {
        // Mendukung pemanggilan 5 argumen: ($actor, $role, $action, $docId, $details)
        if ($details === null && $docId !== null) {
            $details = $docId;
            $docId = $docType;
            $docType = 'SALES_ORDER';
        }

        return self::create([
            'timestamp' => now(),
            'actor' => $actor ?? 'System',
            'role' => $role ?? 'Sales',
            'action' => $action,
            'doc_type' => $docType ?? 'SALES_ORDER',
            'doc_id' => $docId ?? 'N/A',
            'details' => $details ?? ''
        ]);
    }
}
