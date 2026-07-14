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
use App\Http\Controllers\Backend\FrontendSettingController;
use App\Http\Controllers\Backend\SlideController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\StepController;
use App\Http\Controllers\Backend\TeacherController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Frontend\BusContractController;
use App\Http\Controllers\Frontend\TeacherController as FrontendTeacherController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Backend\BalanceController;
use App\Http\Controllers\Backend\PurchaseController as AdminPurchaseController;
use App\Http\Controllers\Employee\PurchaseController as EmployeePurchaseController;
use App\Http\Controllers\Employee\InvitationController as EmployeeInvitationController;
use App\Http\Controllers\School\EmploymentController as SchoolEmploymentController;
use App\Http\Controllers\School\EmployeeInvitationController as SchoolEmployeeInvitationController;
use App\Http\Controllers\School\EmployeeInterviewController as SchoolEmployeeInterviewController;
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

// Contact form
Route::post('/contact', [ContactController::class, 'store'])
    ->name('contact.store');

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

        // All other roles (teacher, employee, etc.) use the same provider dashboard
        return Inertia::render('Dashboard');
    })->middleware('verified')->name('dashboard');

    // Teacher Profile
    Route::get('/complete-profile/create', [TeacherProfileController::class, 'create'])
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

            // Employee browsing & invitations
            Route::get('/employees/browse', [SchoolEmployeeInvitationController::class, 'browse'])
                ->name('employees.browse');
            Route::post('/employee-invite', [SchoolEmployeeInvitationController::class, 'invite'])
                ->name('employee-invite');
            Route::get('/employee-invitations', [SchoolEmployeeInvitationController::class, 'invitations'])
                ->name('employee-invitations');
            Route::get('/employee-hired', [SchoolEmployeeInvitationController::class, 'hired'])
                ->name('employee-hired');
            Route::patch('/employee-invitations/{invitation}/hire', [SchoolEmployeeInvitationController::class, 'hire'])
                ->name('employee-invitations.hire');
            Route::patch('/employee-invitations/{invitation}/reject', [SchoolEmployeeInvitationController::class, 'reject'])
                ->name('employee-invitations.reject');
            Route::patch('/employee-invitations/{invitation}/end', [SchoolEmployeeInvitationController::class, 'endEmployment'])
                ->name('employee-invitations.end');

            // Employee Interviews
            Route::get('/employee-invitations/{invitation}/interview', [SchoolEmployeeInterviewController::class, 'create'])
                ->name('employee-interviews.create');
            Route::post('/employee-invitations/{invitation}/interview', [SchoolEmployeeInterviewController::class, 'store'])
                ->name('employee-interviews.store');
            Route::get('/employee-interviews/{interview}', [SchoolEmployeeInterviewController::class, 'show'])
                ->name('employee-interviews.show');
            Route::patch('/employee-interviews/{interview}/hire', [SchoolEmployeeInterviewController::class, 'hire'])
                ->name('employee-interviews.hire');
            Route::patch('/employee-interviews/{interview}/reject', [SchoolEmployeeInterviewController::class, 'reject'])
                ->name('employee-interviews.reject');
        });

    // Teacher Employment
    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/invitations', [TeacherEmploymentController::class, 'invitations'])
            ->middleware('permission:الرد على الدعوات')
            ->name('invitations');
        Route::patch('/employments/{employment}/accept', [TeacherEmploymentController::class, 'accept'])
            ->middleware('teacher')
            ->name('employments.accept');
        Route::patch('/employments/{employment}/decline', [TeacherEmploymentController::class, 'decline'])
            ->middleware('teacher')
            ->name('employments.decline');
        Route::get('/my-school', [TeacherEmploymentController::class, 'mySchool'])
            ->middleware('teacher')
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
        // محتوى إضافي - لا يوجد صلاحية محددة (دور admin فقط)
        Route::middleware('role:admin')->group(function () {
            // Contacts
            Route::get('/contacts', [ContactController::class, 'index'])
                ->name('contacts.index');
            Route::patch('/contacts/{contact}/read', [ContactController::class, 'markAsRead'])
                ->name('contacts.read');

            // Frontend Settings (consolidated page)
            Route::get('/frontend', [FrontendSettingController::class, 'index'])
                ->name('frontend.index');
            Route::post('/frontend/settings', [FrontendSettingController::class, 'updateSettings'])
                ->name('frontend.settings.update');
            Route::post('/frontend/slides', [FrontendSettingController::class, 'storeSlide'])
                ->name('frontend.slides.store');
            Route::post('/frontend/slides/reorder', [FrontendSettingController::class, 'reorderSlides'])
                ->name('frontend.slides.reorder');
            Route::post('/frontend/slides/{slide}', [FrontendSettingController::class, 'updateSlide'])
                ->name('frontend.slides.update');
            Route::delete('/frontend/slides/{slide}', [FrontendSettingController::class, 'destroySlide'])
                ->name('frontend.slides.destroy');
            Route::post('/frontend/testimonials', [FrontendSettingController::class, 'storeTestimonial'])
                ->name('frontend.testimonials.store');
            Route::post('/frontend/testimonials/{testimonial}', [FrontendSettingController::class, 'updateTestimonial'])
                ->name('frontend.testimonials.update');
            Route::delete('/frontend/testimonials/{testimonial}', [FrontendSettingController::class, 'destroyTestimonial'])
                ->name('frontend.testimonials.destroy');
            Route::post('/frontend/steps', [FrontendSettingController::class, 'storeStep'])
                ->name('frontend.steps.store');
            Route::post('/frontend/features', [FrontendSettingController::class, 'storeFeature'])
                ->name('frontend.features.store');
            Route::post('/frontend/features/{feature}', [FrontendSettingController::class, 'updateFeature'])
                ->name('frontend.features.update');
            Route::delete('/frontend/features/{feature}', [FrontendSettingController::class, 'destroyFeature'])
                ->name('frontend.features.destroy');
            Route::post('/frontend/steps/{step}', [FrontendSettingController::class, 'updateStep'])
                ->name('frontend.steps.update');
            Route::delete('/frontend/steps/{step}', [FrontendSettingController::class, 'destroyStep'])
                ->name('frontend.steps.destroy');

            // Slides
            Route::get('/slides', [SlideController::class, 'index'])
                ->name('slides.index');
            Route::post('/slides', [SlideController::class, 'store'])
                ->name('slides.store');
            Route::post('/slides/reorder', [SlideController::class, 'reorder'])
                ->name('slides.reorder');
            Route::post('/slides/{slide}', [SlideController::class, 'update'])
                ->name('slides.update');
            Route::delete('/slides/{slide}', [SlideController::class, 'destroy'])
                ->name('slides.destroy');

            // Testimonials
            Route::get('/testimonials', [TestimonialController::class, 'index'])
                ->name('testimonials.index');
            Route::post('/testimonials', [TestimonialController::class, 'store'])
                ->name('testimonials.store');
            Route::post('/testimonials/{testimonial}', [TestimonialController::class, 'update'])
                ->name('testimonials.update');
            Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])
                ->name('testimonials.destroy');

            // Steps
            Route::get('/steps', [StepController::class, 'index'])
                ->name('steps.index');
            Route::post('/steps', [StepController::class, 'store'])
                ->name('steps.store');
            Route::post('/steps/{step}', [StepController::class, 'update'])
                ->name('steps.update');
            Route::delete('/steps/{step}', [StepController::class, 'destroy'])
                ->name('steps.destroy');
        });

        // Teachers management (requires permission)
        Route::middleware('permission:إدارة المدرسين')->group(function () {
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
        });

        // Users management (requires permission)
        Route::middleware('permission:إدارة المستخدمين')->group(function () {
            Route::get('/users', [UserController::class, 'index'])
                ->name('users.index');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])
                ->name('users.destroy');
        });

        // Settings (requires permission)
        Route::middleware('permission:إدارة الإعدادات')->group(function () {
            Route::get('/settings', [SettingsController::class, 'edit'])
                ->name('settings.edit');
            Route::post('/settings', [SettingsController::class, 'update'])
                ->name('settings.update');
        });

        // Bus contracts (requires permission)
        Route::middleware('permission:إدارة عقود الباص')->group(function () {
            Route::get('/bus-contracts', [AdminBusContractController::class, 'index'])
                ->name('bus-contracts.index');
            Route::get('/bus-contracts/{contract}', [AdminBusContractController::class, 'show'])
                ->name('bus-contracts.show');
            Route::delete('/bus-contracts/{contract}', [AdminBusContractController::class, 'destroy'])
                ->name('bus-contracts.destroy');
        });

        // Subjects management (requires permission)
        Route::middleware('permission:إدارة المواد')->group(function () {
            Route::get('/subjects', [LookupController::class, 'subjects'])
                ->name('subjects.index');
            Route::post('/subjects', [LookupController::class, 'storeSubject'])
                ->name('subjects.store');
            Route::put('/subjects/{subject}', [LookupController::class, 'updateSubject'])
                ->name('subjects.update');
            Route::delete('/subjects/{subject}', [LookupController::class, 'destroySubject'])
                ->name('subjects.destroy');
        });

        // Grades management (requires permission)
        Route::middleware('permission:إدارة الصفوف')->group(function () {
            Route::get('/grades', [LookupController::class, 'grades'])
                ->name('grades.index');
            Route::post('/grades', [LookupController::class, 'storeGrade'])
                ->name('grades.store');
            Route::put('/grades/{grade}', [LookupController::class, 'updateGrade'])
                ->name('grades.update');
            Route::delete('/grades/{grade}', [LookupController::class, 'destroyGrade'])
                ->name('grades.destroy');
        });

        // Contract attachments (requires permission)
        Route::middleware('permission:إدارة مرفقات العقود')->group(function () {
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
        });

        // Schools management (requires permission)
        Route::middleware('permission:إدارة المدارس')->group(function () {
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

        // Roles & Permissions management (requires permission)
        Route::middleware('permission:إدارة الأدوار')->group(function () {
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
        });

        // Branches management (requires permission)
        Route::middleware('permission:إدارة الفروع')->group(function () {
            Route::get('/branches', [BranchController::class, 'index'])
                ->name('branches.index');
            Route::post('/branches', [BranchController::class, 'store'])
                ->name('branches.store');
            Route::put('/branches/{branch}', [BranchController::class, 'update'])
                ->name('branches.update');
            Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])
                ->name('branches.destroy');
        });

        // Employees management (requires permission)
        Route::middleware('permission:إدارة الموظفين')->group(function () {
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
        });

        // Fixed Tasks management (requires permission)
        Route::middleware('permission:إدارة المهام اليومية')->group(function () {
            Route::get('/fixed-tasks', [AdminFixedTaskController::class, 'index'])
                ->name('fixed-tasks.index');
            Route::post('/fixed-tasks', [AdminFixedTaskController::class, 'store'])
                ->name('fixed-tasks.store');
            Route::put('/fixed-tasks/{fixedTask}', [AdminFixedTaskController::class, 'update'])
                ->name('fixed-tasks.update');
            Route::delete('/fixed-tasks/{fixedTask}', [AdminFixedTaskController::class, 'destroy'])
                ->name('fixed-tasks.destroy');
        });

        // Progress report (requires separate permission)
        Route::middleware('permission:عرض تقارير المهام')->group(function () {
            Route::get('/fixed-tasks/progress', [AdminFixedTaskController::class, 'progress'])
                ->name('fixed-tasks.progress');
        });

        // General Tasks management (requires permission)
        Route::middleware('permission:إدارة المهام العامة')->group(function () {
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
        });

        // Interview Questions management (requires permission)
        Route::middleware('permission:إدارة أسئلة المقابلات')->group(function () {
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
        });

        // Balance management (requires permission)
        Route::middleware('permission:إدارة الحسابات المالية')->prefix('balances')->name('balances.')->group(function () {
            Route::get('/', [BalanceController::class, 'index'])
                ->name('index');
            Route::get('/create', [BalanceController::class, 'create'])
                ->name('create');
            Route::post('/', [BalanceController::class, 'store'])
                ->name('store');
            Route::get('/user/{user}', [BalanceController::class, 'getUserBalance'])
                ->name('user-balance');
        });

        // Purchases management (requires permission)
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

    // Universal purchases view (for all authenticated users)
    Route::get('/purchases', [AdminPurchaseController::class, 'indexAll'])
        ->name('purchases.all');

    // Employee purchases & invitations
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('/purchases', [EmployeePurchaseController::class, 'index'])
            ->name('purchases.index');
        Route::post('/purchases/{purchase}/complete', [EmployeePurchaseController::class, 'complete'])
            ->name('purchases.complete');

        // Employee invitations
        Route::get('/invitations', [EmployeeInvitationController::class, 'index'])
            ->name('invitations.index');
        Route::patch('/invitations/{invitation}/accept', [EmployeeInvitationController::class, 'accept'])
            ->name('invitations.accept');
        Route::patch('/invitations/{invitation}/decline', [EmployeeInvitationController::class, 'decline'])
            ->name('invitations.decline');
    });
});

require __DIR__ . '/auth.php';
