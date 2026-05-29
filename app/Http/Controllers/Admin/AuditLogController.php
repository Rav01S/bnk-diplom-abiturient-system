<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()
            ->with('user')
            ->latest();

        if ($request->filled('search')) {
            $search = (string) $request->string('search');

            $query->where(function ($query) use ($search): void {
                $query->where('action', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search): void {
                        $query->where('email', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('action')) {
            $query->where('action', (string) $request->string('action'));
        }

        $actions = AuditLog::query()
            ->select('action')
            ->distinct()
            ->pluck('action')
            ->mapWithKeys(fn (string $action): array => [$action => AuditLog::actionLabel($action)])
            ->sort()
            ->all();

        return view('admin.audit-logs', [
            'logs' => $query->paginate(12)->withQueryString(),
            'actions' => $actions,
        ]);
    }
}
