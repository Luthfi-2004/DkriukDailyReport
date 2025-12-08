<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\MasterItemController;
use App\Http\Controllers\UserController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});
Route::get('/csrf-token', function() { return response()->json(['token' => csrf_token()]);})->name('csrf.token.public');

Route::middleware(['auth'])->get('/auth/check-role', [DashboardController::class, 'redirectBasedOnRole'])->name('auth.check_role');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/super-dashboard', [DashboardController::class, 'superIndex'])->name('super.dashboard');
    Route::resource('master-items', MasterItemController::class)->except(['create', 'show', 'edit']);
    Route::resource('manage-users', UserController::class)->parameters(['manage-users' => 'user'])->except(['create', 'show', 'edit']);
});

Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
    Route::get('/admin-dashboard', [DashboardController::class, 'adminIndex'])->name('admin.dashboard');
    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/{id}', [AdminReportController::class, 'show'])->name('admin.reports.show');
    Route::get('/admin/reports/{id}/edit', [AdminReportController::class, 'edit'])->name('admin.reports.edit');
    Route::put('/admin/reports/{id}', [AdminReportController::class, 'update'])->name('admin.reports.update');
    Route::delete('/admin/reports/{id}', [AdminReportController::class, 'destroy'])->name('admin.reports.destroy');
    Route::post('/admin/reports/{id}/status', [App\Http\Controllers\AdminReportController::class, 'updateStatus'])->name('admin.reports.status');
});

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
});

Route::middleware(['auth', 'role:user,admin,super_admin'])->group(function () {
    // Route::get('/daily-report/create', [DailyReportController::class, 'create'])->name('reports.create');
    Route::post('/daily-report', [DailyReportController::class, 'store'])->name('reports.store');
    Route::get('/daily-report/history', [DailyReportController::class, 'index'])->name('reports.index');
    Route::get('/daily-report/{dailyReport}', [DailyReportController::class, 'show'])->name('reports.show');
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/daily-report/{dailyReport}/edit', [DailyReportController::class, 'edit'])->name('reports.edit');
    Route::put('/daily-report/{dailyReport}', [DailyReportController::class, 'update'])->name('reports.update');
    Route::delete('/daily-report/{dailyReport}', [DailyReportController::class, 'destroy'])->name('reports.destroy');
});