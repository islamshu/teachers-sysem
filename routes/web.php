<?php

use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\DashboardController as AdminDashboardController;
use App\Http\Controllers\Backend\LookupController;
use App\Http\Controllers\Backend\TeacherController;
use App\Http\Controllers\Frontend\TeacherController as FrontendTeacherController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeacherProfileController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

// Public routes - الصفحة الرئيسية تعرض المدرسين المقبولين
Route::get('/', [FrontendTeacherController::class, 'index'])
    ->name('home');

// تحويل /teachers إلى الصفحة الرئيسية
Route::get('/teachers', function () {
    return redirect('/');
})->name('teachers.index');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard - يختلف حسب نوع المستخدم
    Route::get('/dashboard', function () {
        if (auth()->user()->is_admin) {
            return app(AdminDashboardController::class)->index();
        }
        return Inertia::render('Dashboard');
    })->middleware('verified')->name('dashboard');

    // Teacher Profile
    Route::get('/teacher-profile/create', [TeacherProfileController::class, 'create'])
        ->name('teacher-profile.create');
    Route::post('/teacher-profile/store', [TeacherProfileController::class, 'store'])
        ->name('teacher-profile.store');
    Route::get('/teacher-profile/edit', [TeacherProfileController::class, 'edit'])
        ->name('teacher-profile.edit');
    Route::put('/teacher-profile/update', [TeacherProfileController::class, 'update'])
        ->name('teacher-profile.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Teachers management
        Route::get('/teachers', [TeacherController::class, 'index'])
            ->name('teachers.index');
        Route::patch('/teachers/batch/approve', [TeacherController::class, 'batchApprove'])
            ->name('teachers.batch.approve');
        Route::patch('/teachers/batch/reject', [TeacherController::class, 'batchReject'])
            ->name('teachers.batch.reject');
        Route::patch('/teachers/{teacher}/approve', [TeacherController::class, 'approve'])
            ->name('teachers.approve');
        Route::patch('/teachers/{teacher}/reject', [TeacherController::class, 'reject'])
            ->name('teachers.reject');
        Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])
            ->name('teachers.show');

        // Settings
        Route::get('/settings', [SettingsController::class, 'edit'])
            ->name('settings.edit');
        Route::post('/settings', [SettingsController::class, 'update'])
            ->name('settings.update');

        // Subjects management
        Route::get('/subjects', [LookupController::class, 'subjects'])
            ->name('subjects.index');
        Route::post('/subjects', [LookupController::class, 'storeSubject'])
            ->name('subjects.store');
        Route::put('/subjects/{subject}', [LookupController::class, 'updateSubject'])
            ->name('subjects.update');
        Route::delete('/subjects/{subject}', [LookupController::class, 'destroySubject'])
            ->name('subjects.destroy');

        // Grades management
        Route::get('/grades', [LookupController::class, 'grades'])
            ->name('grades.index');
        Route::post('/grades', [LookupController::class, 'storeGrade'])
            ->name('grades.store');
        Route::put('/grades/{grade}', [LookupController::class, 'updateGrade'])
            ->name('grades.update');
        Route::delete('/grades/{grade}', [LookupController::class, 'destroyGrade'])
            ->name('grades.destroy');
    });
});

require __DIR__ . '/auth.php';
