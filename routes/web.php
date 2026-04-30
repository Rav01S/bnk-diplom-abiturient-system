<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SpecialtyController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Applicant\ApplicationController;
use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboardController;
use App\Http\Controllers\Applicant\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Commission\DashboardController as CommissionDashboardController;
use App\Http\Controllers\Commission\QueueController;
use App\Http\Controllers\Commission\RankingController;
use App\Http\Controllers\Commission\ReviewController;
use App\Http\Controllers\Commission\StatsController;
use Illuminate\Support\Facades\Route;

// === Главная — редирект по роли ===
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'applicant' => redirect()->route('applicant.dashboard'),
            'commission' => redirect()->route('commission.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
        };
    }

    return redirect()->route('login');
})->name('home');

// === Авторизация ===
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// === Абитуриент ===
Route::prefix('applicant')->middleware(['auth', 'role:applicant'])->name('applicant.')->group(function (): void {
    Route::get('/dashboard', [ApplicantDashboardController::class, 'index'])->name('dashboard');

    // Профиль
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo/{field}', [ProfileController::class, 'deletePhoto'])->name('profile.deletePhoto');

    // Заявления
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications');
    Route::get('/applications/create', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::get('/applications/{application}/edit', [ApplicationController::class, 'edit'])->name('applications.edit');
    Route::put('/applications/{application}', [ApplicationController::class, 'update'])->name('applications.update');
    Route::patch('/applications/{application}/cancel', [ApplicationController::class, 'cancel'])->name('applications.cancel');
    Route::get('/applications/{application}/template', [ApplicationController::class, 'downloadTemplate'])->name('applications.template');
});

// === Комиссия ===
Route::prefix('commission')->middleware(['auth', 'role:commission'])->name('commission.')->group(function (): void {
    Route::get('/dashboard', [CommissionDashboardController::class, 'index'])->name('dashboard');
    Route::get('/queue', [QueueController::class, 'index'])->name('queue');
    Route::get('/review', [QueueController::class, 'reviewNext'])->name('review.next');
    Route::get('/review/{application}', [ReviewController::class, 'show'])->name('review');
    Route::post('/review/{application}', [ReviewController::class, 'review'])->name('review.submit');
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking');
    Route::get('/ranking/export', [RankingController::class, 'export'])->name('ranking.export');
    Route::get('/stats', [StatsController::class, 'index'])->name('stats');
    Route::get('/stats/export', [StatsController::class, 'export'])->name('stats.export');
});

// === Администратор ===
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function (): void {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Сотрудники
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Специальности и программы
    Route::get('/specialties', [SpecialtyController::class, 'index'])->name('specialties');
    Route::post('/specialties', [SpecialtyController::class, 'store'])->name('specialties.store');
    Route::put('/specialties/{specialty}', [SpecialtyController::class, 'update'])->name('specialties.update');
    Route::delete('/specialties/{specialty}', [SpecialtyController::class, 'destroy'])->name('specialties.destroy');
    Route::post('/specialties/{specialty}/programs', [SpecialtyController::class, 'storeProgram'])->name('programs.store');
    Route::put('/programs/{program}', [SpecialtyController::class, 'updateProgram'])->name('programs.update');
    Route::delete('/programs/{program}', [SpecialtyController::class, 'destroyProgram'])->name('programs.destroy');
    Route::patch('/programs/{program}/toggle', [SpecialtyController::class, 'toggleProgram'])->name('programs.toggle');

    // Кампания
    Route::get('/campaign', [CampaignController::class, 'index'])->name('campaign');
    Route::put('/campaign', [CampaignController::class, 'update'])->name('campaign.update');
    Route::post('/campaign/open-all', [CampaignController::class, 'massOpen'])->name('campaign.open-all');
    Route::post('/campaign/close-all', [CampaignController::class, 'massClose'])->name('campaign.close-all');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');
});
