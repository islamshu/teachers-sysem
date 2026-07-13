<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'عرض لوحة التحكم',
            'إدارة المدرسين',
            'إدارة المدارس',
            'إدارة المستخدمين',
            'إدارة الإعدادات',
            'إدارة المواد',
            'إدارة الصفوف',
            'إدارة عقود الباص',
            'إدارة مرفقات العقود',
            'إدارة الملف الشخصي',
            'إدارة التوظيف',
            'الرد على الدعوات',
            'إدارة الأدوار',
            'إدارة الفروع',
            'إدارة الموظفين',
            'إدارة المهام اليومية',
            'عرض تقارير المهام',
            'إدارة المهام العامة',
            'عرض التوظيف',
            'إجراء المقابلات',
            'إدارة أسئلة المقابلات',
            'إدارة المشتريات',
            'إدارة الحسابات المالية',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all()->pluck('name')->toArray());

        $school = Role::firstOrCreate(['name' => 'school', 'guard_name' => 'web']);
        $school->syncPermissions([
            'عرض لوحة التحكم',
            'إدارة الملف الشخصي',
            'إدارة التوظيف',
            'عرض التوظيف',
            'إجراء المقابلات',
        ]);

        $teacher = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $teacher->syncPermissions([
            'عرض لوحة التحكم',
            'إدارة الملف الشخصي',
            'الرد على الدعوات',
            'عرض التوظيف',
        ]);

        \App\Models\User::where('role', 'admin')
            ->each(fn ($user) => $user->assignRole('admin'));

        \App\Models\User::where('role', 'school')
            ->each(fn ($user) => $user->assignRole('school'));

        \App\Models\User::where('role', 'teacher')
            ->each(fn ($user) => $user->assignRole('teacher'));
    }
}
