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
        Setting::set('hero_title', '');
        Setting::set('hero_subtitle', '');
        Setting::set('hero_bg_from', '#1e40af');
        Setting::set('hero_bg_to', '#7c3aed');
        Setting::set('show_stats', '1');
    }
}
