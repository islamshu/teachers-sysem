<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $map = [
            'view dashboard' => 'عرض لوحة التحكم',
            'manage teachers' => 'إدارة المدرسين',
            'manage schools' => 'إدارة المدارس',
            'manage users' => 'إدارة المستخدمين',
            'manage settings' => 'إدارة الإعدادات',
            'manage subjects' => 'إدارة المواد',
            'manage grades' => 'إدارة الصفوف',
            'manage bus-contracts' => 'إدارة عقود الباص',
            'manage contract-attachments' => 'إدارة مرفقات العقود',
            'manage own profile' => 'إدارة الملف الشخصي',
            'manage employments' => 'إدارة التوظيف',
            'respond to invitations' => 'الرد على الدعوات',
            'manage roles' => 'إدارة الأدوار',
            'manage branches' => 'إدارة الفروع',
            'manage employees' => 'إدارة الموظفين',
            'manage fixed tasks' => 'إدارة المهام اليومية',
            'view task progress' => 'عرض تقارير المهام',
            'manage general tasks' => 'إدارة المهام العامة',
            'view employments' => 'عرض التوظيف',
            'conduct interviews' => 'إجراء المقابلات',
            'manage interview questions' => 'إدارة أسئلة المقابلات',
        ];

        foreach ($map as $old => $new) {
            Permission::where('name', $old)->update(['name' => $new]);
        }

        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->syncPermissions(Permission::all()->pluck('name')->toArray());
        }

        Cache::forget('spatie.permission.cache');
    }

    public function down(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $reverseMap = [
            'عرض لوحة التحكم' => 'view dashboard',
            'إدارة المدرسين' => 'manage teachers',
            'إدارة المدارس' => 'manage schools',
            'إدارة المستخدمين' => 'manage users',
            'إدارة الإعدادات' => 'manage settings',
            'إدارة المواد' => 'manage subjects',
            'إدارة الصفوف' => 'manage grades',
            'إدارة عقود الباص' => 'manage bus-contracts',
            'إدارة مرفقات العقود' => 'manage contract-attachments',
            'إدارة الملف الشخصي' => 'manage own profile',
            'إدارة التوظيف' => 'manage employments',
            'الرد على الدعوات' => 'respond to invitations',
            'إدارة الأدوار' => 'manage roles',
            'إدارة الفروع' => 'manage branches',
            'إدارة الموظفين' => 'manage employees',
            'إدارة المهام اليومية' => 'manage fixed tasks',
            'عرض تقارير المهام' => 'view task progress',
            'إدارة المهام العامة' => 'manage general tasks',
            'عرض التوظيف' => 'view employments',
            'إجراء المقابلات' => 'conduct interviews',
            'إدارة أسئلة المقابلات' => 'manage interview questions',
        ];

        foreach ($reverseMap as $arabic => $english) {
            Permission::where('name', $arabic)->update(['name' => $english]);
        }

        Cache::forget('spatie.permission.cache');
    }
};
