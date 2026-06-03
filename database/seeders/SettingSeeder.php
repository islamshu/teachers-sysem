<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::set('site_name', 'معلمي');
        Setting::set('primary_color', '#10b981');
        Setting::set('description', 'منصة تربوية تجمع المدرسين وأولياء الأمور');
        Setting::set('contact_email', '');
        Setting::set('contact_phone', '');
        Setting::set('address', '');
        Setting::set('about', '');
    }
}
