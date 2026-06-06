<?php

use App\Http\Controllers\Backend\DashboardController as AdminDashboardController;
use App\Http\Controllers\Backend\LookupController;
use App\Http\Controllers\Backend\SchoolController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\TeacherController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Frontend\TeacherController as FrontendTeacherController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\School\EmploymentController as SchoolEmploymentController;
use App\Http\Controllers\SchoolProfileController;
use App\Http\Controllers\Teacher\EmploymentController as TeacherEmploymentController;
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

// Public teacher profile
Route::get('/teachers/{teacher}', [FrontendTeacherController::class, 'show'])
    ->name('teachers.show');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard - يختلف حسب نوع المستخدم
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->is_admin) {
            return app(AdminDashboardController::class)->index();
        }

        if ($user->isSchool()) {
            return Inertia::render('School/Dashboard');
        }

        if ($user->isTeacher()) {
            return Inertia::render('Dashboard');
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

    // School Profile
    Route::get('/school-profile/create', [SchoolProfileController::class, 'create'])
        ->name('school-profile.create');
    Route::post('/school-profile/store', [SchoolProfileController::class, 'store'])
        ->name('school-profile.store');
    Route::get('/school-profile/edit', [SchoolProfileController::class, 'edit'])
        ->name('school-profile.edit');
    Route::post('/school-profile/update', [SchoolProfileController::class, 'update'])
        ->name('school-profile.update');

    // School Employment
    Route::middleware('school')->prefix('school')->name('school.')->group(function () {
        Route::get('/teachers', [SchoolEmploymentController::class, 'teachers'])
            ->name('teachers');
        Route::post('/invite', [SchoolEmploymentController::class, 'invite'])
            ->name('invite');
        Route::get('/invitations', [SchoolEmploymentController::class, 'invitations'])
            ->name('invitations');
        Route::patch('/employments/{employment}/interviewed', [SchoolEmploymentController::class, 'markInterviewed'])
            ->name('employments.interviewed');
        Route::patch('/employments/{employment}/hire', [SchoolEmploymentController::class, 'hire'])
            ->name('employments.hire');
        Route::patch('/employments/{employment}/reject', [SchoolEmploymentController::class, 'reject'])
            ->name('employments.reject');
        Route::get('/employees', [SchoolEmploymentController::class, 'employees'])
            ->name('employees');
        Route::patch('/employments/{employment}/end', [SchoolEmploymentController::class, 'endEmployment'])
            ->name('employments.end');
    });

    // Teacher Employment
    Route::middleware('teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/invitations', [TeacherEmploymentController::class, 'invitations'])
            ->name('invitations');
        Route::patch('/employments/{employment}/accept', [TeacherEmploymentController::class, 'accept'])
            ->name('employments.accept');
        Route::patch('/employments/{employment}/decline', [TeacherEmploymentController::class, 'decline'])
            ->name('employments.decline');
        Route::get('/my-school', [TeacherEmploymentController::class, 'mySchool'])
            ->name('my-school');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])
            ->name('index');
        Route::get('/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])
            ->name('unread-count');
        Route::post('/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])
            ->name('mark-as-read');
        Route::post('/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])
            ->name('mark-all-read');
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Teachers management
        Route::get('/teachers/create', [TeacherController::class, 'create'])
            ->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])
            ->name('teachers.store');
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
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])
            ->name('teachers.destroy');

        // Users management
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');

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

        // Schools management
        Route::get('/schools/create', [SchoolController::class, 'create'])
            ->name('schools.create');
        Route::post('/schools', [SchoolController::class, 'store'])
            ->name('schools.store');
        Route::get('/schools', [SchoolController::class, 'index'])
            ->name('schools.index');
        Route::patch('/schools/batch/approve', [SchoolController::class, 'batchApprove'])
            ->name('schools.batch.approve');
        Route::patch('/schools/batch/reject', [SchoolController::class, 'batchReject'])
            ->name('schools.batch.reject');
        Route::patch('/schools/{school}/approve', [SchoolController::class, 'approve'])
            ->name('schools.approve');
        Route::patch('/schools/{school}/reject', [SchoolController::class, 'reject'])
            ->name('schools.reject');
        Route::delete('/schools/{school}', [SchoolController::class, 'destroy'])
            ->name('schools.destroy');
    });
});

require __DIR__ . '/auth.php';
