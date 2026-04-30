<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(): View
    {
        $programs = Program::with('specialty')->get();

        return view('admin.campaign', compact('programs'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'programs' => ['required', 'array'],
            'programs.*.is_open' => ['nullable', 'boolean'],
            'programs.*.open_from' => ['nullable', 'date'],
            'programs.*.open_until' => ['nullable', 'date'],
        ]);

        foreach ($validated['programs'] as $programId => $data) {
            $program = Program::find($programId);

            if ($program) {
                $program->update([
                    'is_open' => ! empty($data['is_open']),
                    'open_from' => $data['open_from'] ?? $program->open_from,
                    'open_until' => $data['open_until'] ?? $program->open_until,
                ]);
            }
        }

        AuditLog::record($request, 'campaign.updated', 'Программы приема', [
            'programs_count' => count($validated['programs']),
        ]);

        return redirect()->route('admin.campaign')->with('success', 'Настройки кампании обновлены.');
    }

    public function massOpen(Request $request): RedirectResponse
    {
        $count = Program::query()->update(['is_open' => true]);

        AuditLog::record($request, 'campaign.opened_all', 'Все программы приема', [
            'programs_count' => $count,
        ]);

        return redirect()
            ->route('admin.campaign')
            ->with('success', 'Приём открыт на всех программах.');
    }

    public function massClose(Request $request): RedirectResponse
    {
        $count = Program::query()->update(['is_open' => false]);

        AuditLog::record($request, 'campaign.closed_all', 'Все программы приема', [
            'programs_count' => $count,
        ]);

        return redirect()
            ->route('admin.campaign')
            ->with('success', 'Приём закрыт на всех программах.');
    }
}
