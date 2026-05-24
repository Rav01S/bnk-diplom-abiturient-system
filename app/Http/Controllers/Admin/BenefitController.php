<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Benefit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BenefitController extends Controller
{
    public function index(): View
    {
        $benefits = Benefit::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get()
            ->map(function (Benefit $benefit): Benefit {
                $benefit->setAttribute(
                    'applications_count',
                    Application::where('benefit_type', $benefit->key)->count()
                );

                return $benefit;
            });

        return view('admin.benefits', compact('benefits'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);
        $validated['key'] = $this->generateKey($validated['label']);

        $benefit = Benefit::create($validated);

        AuditLog::record($request, 'benefit.created', $benefit->label, [
            'key' => $benefit->key,
            'gives_priority' => $benefit->gives_priority,
            'boost_mode' => $benefit->boost_mode,
            'boost_value' => $benefit->boost_value,
        ]);

        return redirect()->route('admin.benefits')->with('success', 'Льгота добавлена.');
    }

    public function update(Request $request, Benefit $benefit): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        $benefit->update($validated);

        AuditLog::record($request, 'benefit.updated', $benefit->label, [
            'key' => $benefit->key,
            'gives_priority' => $benefit->gives_priority,
            'boost_mode' => $benefit->boost_mode,
            'boost_value' => $benefit->boost_value,
        ]);

        return redirect()->route('admin.benefits')->with('success', 'Льгота обновлена.');
    }

    public function toggle(Request $request, Benefit $benefit): RedirectResponse
    {
        $benefit->update(['is_active' => ! $benefit->is_active]);

        AuditLog::record($request, 'benefit.toggled', $benefit->label, [
            'key' => $benefit->key,
            'is_active' => $benefit->is_active,
        ]);

        $status = $benefit->is_active ? 'включена' : 'отключена';

        return redirect()->route('admin.benefits')->with('success', "Льгота {$status}.");
    }

    public function destroy(Request $request, Benefit $benefit): RedirectResponse
    {
        $inUse = Application::where('benefit_type', $benefit->key)->count();

        if ($inUse > 0) {
            return redirect()->route('admin.benefits')
                ->with('error', "Нельзя удалить льготу: к ней привязано заявлений — {$inUse}. Отключите её вместо удаления.");
        }

        $label = $benefit->label;
        $key = $benefit->key;
        $benefit->delete();

        AuditLog::record($request, 'benefit.deleted', $label, [
            'key' => $key,
        ]);

        return redirect()->route('admin.benefits')->with('success', 'Льгота удалена.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request): array
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'gives_priority' => ['nullable', 'boolean'],
            'boost_mode' => ['nullable', 'in:replace,add'],
            'boost_value' => ['nullable', 'numeric', 'min:0', 'max:6'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'boost_value.max' => 'Значение балла не может быть выше 6.',
            'sort_order.max' => 'Порядок не может быть больше 100.',
        ]);

        $validated['gives_priority'] = $request->boolean('gives_priority');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['boost_mode'] = $validated['boost_mode'] ?? null;

        if ($validated['boost_mode'] === null) {
            $validated['boost_value'] = null;
        } elseif (empty($validated['boost_value'])) {
            $validated['boost_value'] = $validated['boost_mode'] === Benefit::BoostReplace ? 6.00 : 0.50;
        }

        return $validated;
    }

    private function generateKey(string $label): string
    {
        $base = Str::slug($label, '_');

        if ($base === '') {
            $base = 'benefit_'.Str::lower(Str::random(6));
        }

        $base = mb_substr($base, 0, 50);
        $candidate = $base;
        $suffix = 2;

        while (Benefit::where('key', $candidate)->exists()) {
            $candidate = $base.'_'.$suffix;
            $suffix++;
        }

        return $candidate;
    }
}
