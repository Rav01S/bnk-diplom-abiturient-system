<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $applicant = auth()->user()->applicant;

        return view('applicant.profile', compact('applicant'));
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $applicant = auth()->user()->applicant;
        $data = $request->validated();

        // Обработка загрузки фото
        $photoFields = ['photo_passport', 'photo_snils', 'photo_edu_1', 'photo_edu_2', 'photo_edu_3'];
        foreach ($photoFields as $field) {
            if ($request->hasFile($field)) {
                // Удаляем старый файл
                if ($applicant->$field) {
                    Storage::disk('public')->delete($applicant->$field);
                }
                $data[$field] = $request->file($field)->store('uploads/profiles', 'public');
            } else {
                unset($data[$field]);
            }
        }

        $applicant->update($data);

        return redirect()->route('applicant.profile')->with('success', 'Профиль успешно сохранён!');
    }

    /**
     * Удаление конкретного фото из профиля.
     */
    public function deletePhoto(string $field): RedirectResponse
    {
        $allowed = ['photo_passport', 'photo_snils', 'photo_edu_1', 'photo_edu_2', 'photo_edu_3'];
        if (! in_array($field, $allowed)) {
            abort(404);
        }

        $applicant = auth()->user()->applicant;
        if ($applicant->$field) {
            Storage::disk('public')->delete($applicant->$field);
            $applicant->update([$field => null]);
        }

        return back()->with('success', 'Фото удалено.');
    }
}
