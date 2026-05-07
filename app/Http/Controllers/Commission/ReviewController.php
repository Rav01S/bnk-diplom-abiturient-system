<?php

namespace App\Http\Controllers\Commission;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationReviewRequest;
use App\Models\Application;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function show(Application $application): View
    {
        $application->load('program.specialty', 'scores', 'applicant');

        return view('commission.review', compact('application'));
    }

    /**
     * Решение комиссии: approve/reject/rework.
     */
    public function review(ApplicationReviewRequest $request, Application $application): RedirectResponse
    {
        $validated = $request->validated();

        $application->status = $validated['decision'];

        if (in_array($validated['decision'], ['rejected', 'rework_needed'])) {
            $application->rejection_reason = $validated['rejection_reason'];
        } else {
            $application->rejection_reason = null;
        }

        if ($request->filled('avg_cert_score')) {
            // Также сохраняем в профиль абитуриента, чтобы данные подтягивались в будущие заявления
            $application->applicant->update([
                'avg_cert_score' => $validated['avg_cert_score'],
            ]);
        }

        $application->save();

        AuditLog::record($request, 'application.reviewed', 'Заявление #'.$application->id, [
            'application_id' => $application->id,
            'decision' => $validated['decision'],
        ]);

        $messages = [
            'approved' => 'Заявление одобрено.',
            'rejected' => 'Заявление отклонено.',
            'rework_needed' => 'Заявление отправлено на доработку.',
        ];

        return redirect()->route('commission.queue', $request->query())
            ->with('success', $messages[$validated['decision']]);
    }
}
