<?php

use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\FixedTaskController as AdminFixedTaskController;
use App\Http\Controllers\Admin\GeneralTaskController;
use App\Http\Controllers\Admin\InterviewQuestionController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\FixedTaskController;
use App\Http\Controllers\GeneralTaskController as UserGeneralTaskController;
use App\Http\Controllers\Backend\BusContractController as AdminBusContractController;
use App\Http\Controllers\Backend\ContractAttachmentController;
use App\Http\Controllers\Backend\DashboardController as AdminDashboardController;
use App\Http\Controllers\Backend\LookupController;
use App\Http\Controllers\Backend\SchoolController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\TeacherController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Frontend\BusContractController;
use App\Http\Controllers\Frontend\TeacherController as FrontendTeacherController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\PurchaseController as AdminPurchaseController;
use App\Http\Controllers\Employee\PurchaseController as EmployeePurchaseController;
use App\Http\Controllers\School\EmploymentController as SchoolEmploymentController;
use App\Http\Controllers\School\InterviewController as SchoolInterviewController;
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

// Bus contracts
Route::get('/bus-contracts/create', [BusContractController::class, 'create'])
    ->name('bus-contracts.create');
Route::post('/bus-contracts', [BusContractController::class, 'store'])
    ->name('bus-contracts.store');

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

        if ($user->isEmployee()) {
            return Inertia::render('Employee/Dashboard');
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

        // Employment & Interviews (requires permission)
        Route::middleware('permission:إجراء المقابلات')->prefix('school')->name('school.')->group(function () {
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

            // Interviews
            Route::get('/employments/{employment}/interview', [SchoolInterviewController::class, 'create'])
                ->name('interviews.create');
            Route::post('/employments/{employment}/interview', [SchoolInterviewController::class, 'store'])
                ->name('interviews.store');
            Route::get('/interviews/{interview}', [SchoolInterviewController::class, 'show'])
                ->name('interviews.show');
            Route::patch('/interviews/{interview}/hire', [SchoolInterviewController::class, 'hire'])
                ->name('interviews.hire');
            Route::patch('/interviews/{interview}/reject', [SchoolInterviewController::class, 'reject'])
                ->name('interviews.reject');
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

        // Bus contracts
        Route::get('/bus-contracts', [AdminBusContractController::class, 'index'])
            ->name('bus-contracts.index');
        Route::get('/bus-contracts/{contract}', [AdminBusContractController::class, 'show'])
            ->name('bus-contracts.show');
        Route::delete('/bus-contracts/{contract}', [AdminBusContractController::class, 'destroy'])
            ->name('bus-contracts.destroy');

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

        // Contract attachments
        Route::get('/contract-attachments', [ContractAttachmentController::class, 'index'])
            ->name('contract-attachments.index');
        Route::post('/contract-attachments', [ContractAttachmentController::class, 'store'])
            ->name('contract-attachments.store');
        Route::put('/contract-attachments/{contractAttachment}', [ContractAttachmentController::class, 'update'])
            ->name('contract-attachments.update');
        Route::post('/contract-attachments/{contractAttachment}/toggle', [ContractAttachmentController::class, 'toggle'])
            ->name('contract-attachments.toggle');
        Route::delete('/contract-attachments/{contractAttachment}', [ContractAttachmentController::class, 'destroy'])
            ->name('contract-attachments.destroy');

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

        // Roles & Permissions management
        Route::get('/roles', [RolePermissionController::class, 'roles'])
            ->name('roles.index');
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])
            ->name('roles.store');
        Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])
            ->name('roles.update');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])
            ->name('roles.destroy');
        Route::get('/permissions', [RolePermissionController::class, 'permissions'])
            ->name('permissions.index');
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])
            ->name('permissions.store');
        Route::delete('/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])
            ->name('permissions.destroy');
        Route::get('/users/{user}/roles', [RolePermissionController::class, 'editUserRoles'])
            ->name('users.roles.edit');
        Route::put('/users/{user}/roles', [RolePermissionController::class, 'updateUserRoles'])
            ->name('users.roles.update');

        // Branches management
        Route::get('/branches', [BranchController::class, 'index'])
            ->name('branches.index');
        Route::post('/branches', [BranchController::class, 'store'])
            ->name('branches.store');
        Route::put('/branches/{branch}', [BranchController::class, 'update'])
            ->name('branches.update');
        Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])
            ->name('branches.destroy');

        // Employees management
        Route::get('/employees', [EmployeeController::class, 'index'])
            ->name('employees.index');
        Route::post('/employees', [EmployeeController::class, 'store'])
            ->name('employees.store');
        Route::put('/employees/{user}', [EmployeeController::class, 'update'])
            ->name('employees.update');
        Route::delete('/employees/{user}', [EmployeeController::class, 'destroy'])
            ->name('employees.destroy');
        Route::get('/employees/{user}/tasks-report', [EmployeeController::class, 'tasksReport'])
            ->name('employees.tasks-report');

        // Fixed Tasks management
        Route::get('/fixed-tasks', [AdminFixedTaskController::class, 'index'])
            ->name('fixed-tasks.index');
        Route::get('/fixed-tasks/progress', [AdminFixedTaskController::class, 'progress'])
            ->name('fixed-tasks.progress');
        Route::post('/fixed-tasks', [AdminFixedTaskController::class, 'store'])
            ->name('fixed-tasks.store');
        Route::put('/fixed-tasks/{fixedTask}', [AdminFixedTaskController::class, 'update'])
            ->name('fixed-tasks.update');
        Route::delete('/fixed-tasks/{fixedTask}', [AdminFixedTaskController::class, 'destroy'])
            ->name('fixed-tasks.destroy');

        // General Tasks management
        Route::get('/general-tasks', [GeneralTaskController::class, 'index'])
            ->name('general-tasks.index');
        Route::get('/general-tasks/{generalTask}', [GeneralTaskController::class, 'show'])
            ->name('general-tasks.show');
        Route::post('/general-tasks', [GeneralTaskController::class, 'store'])
            ->name('general-tasks.store');
        Route::put('/general-tasks/{generalTask}', [GeneralTaskController::class, 'update'])
            ->name('general-tasks.update');
        Route::delete('/general-tasks/{generalTask}', [GeneralTaskController::class, 'destroy'])
            ->name('general-tasks.destroy');

        // Interview Questions management
        Route::get('/interview-questions', [InterviewQuestionController::class, 'index'])
            ->name('interview-questions.index');
        Route::post('/interview-questions', [InterviewQuestionController::class, 'store'])
            ->name('interview-questions.store');
        Route::put('/interview-questions/reorder', [InterviewQuestionController::class, 'reorder'])
            ->name('interview-questions.reorder');
        Route::put('/interview-questions/{interviewQuestion}', [InterviewQuestionController::class, 'update'])
            ->name('interview-questions.update');
        Route::delete('/interview-questions/{interviewQuestion}', [InterviewQuestionController::class, 'destroy'])
            ->name('interview-questions.destroy');

        // Purchases management
        Route::middleware('permission:إدارة المشتريات')->group(function () {
            Route::get('/purchases', [AdminPurchaseController::class, 'index'])
                ->name('purchases.index');
            Route::get('/purchases/create', [AdminPurchaseController::class, 'create'])
                ->name('purchases.create');
            Route::post('/purchases', [AdminPurchaseController::class, 'store'])
                ->name('purchases.store');
            Route::get('/purchases/{purchase}', [AdminPurchaseController::class, 'show'])
                ->name('purchases.show');
        });
    });

    // Daily Tasks (for all authenticated users)
    Route::get('/tasks', [FixedTaskController::class, 'index'])
        ->name('tasks.index');
    Route::post('/tasks/{fixedTask}/complete', [FixedTaskController::class, 'complete'])
        ->name('tasks.complete');
    Route::post('/tasks/{fixedTask}/undo', [FixedTaskController::class, 'undo'])
        ->name('tasks.undo');

    // General Tasks (for all authenticated users)
    Route::get('/general-tasks', [UserGeneralTaskController::class, 'index'])
        ->name('general-tasks.user.index');
    Route::post('/general-tasks/{generalTask}/complete', [UserGeneralTaskController::class, 'complete'])
        ->name('general-tasks.user.complete');
    Route::post('/general-tasks/{generalTask}/undo', [UserGeneralTaskController::class, 'undo'])
        ->name('general-tasks.user.undo');

    // Employee purchases
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('/purchases', [EmployeePurchaseController::class, 'index'])
            ->name('purchases.index');
        Route::post('/purchases/{purchase}/complete', [EmployeePurchaseController::class, 'complete'])
            ->name('purchases.complete');
    });
});

require __DIR__ . '/auth.php';
