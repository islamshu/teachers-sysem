<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\Step;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class FrontendSettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $features = Feature::ordered()->get();
        $slides = Slide::ordered()->get();
        $testimonials = Testimonial::ordered()->get();
        $steps = Step::ordered()->get();

        return Inertia::render('Admin/Frontend', [
            'settings' => $settings,
            'features' => $features,
            'slides' => $slides,
            'testimonials' => $testimonials,
            'steps' => $steps,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'site_name' => ['nullable', 'string', 'max:255'],
            'site_logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'description' => ['nullable', 'string', 'max:1000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'about' => ['nullable', 'string', 'max:5000'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'hero_bg_from' => ['nullable', 'string', 'max:7'],
            'hero_bg_to' => ['nullable', 'string', 'max:7'],
            'show_stats' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('site_logo')) {
            $path = $request->file('site_logo')->store('logos', 'public');
            Setting::updateOrCreate(['key' => 'site_logo'], ['value' => $path]);
        }

        foreach (['site_name', 'primary_color', 'description', 'contact_email', 'contact_phone', 'address', 'about', 'hero_title', 'hero_subtitle', 'hero_bg_from', 'hero_bg_to', 'show_stats'] as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(['key' => $key], ['value' => $validated[$key] ?? '']);
            }
        }

        return redirect()->back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }

    public function storeSlide(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'link' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['image'] = $request->file('image')->store('slides', 'public');

        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = Slide::max('sort_order') + 1;
        }

        Slide::create($validated);

        return redirect()->back()->with('success', 'تم إضافة الشريحة بنجاح');
    }

    public function updateSlide(Request $request, Slide $slide)
    {
        $validated = $request->validate([
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'link' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('slides', 'public');
        } else {
            unset($validated['image']);
        }

        $slide->update($validated);

        return redirect()->back()->with('success', 'تم تحديث الشريحة بنجاح');
    }

    public function destroySlide(Slide $slide)
    {
        $slide->delete();

        return redirect()->back()->with('success', 'تم حذف الشريحة بنجاح');
    }

    public function reorderSlides(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:slides,id'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($request->items as $item) {
            Slide::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return redirect()->back()->with('success', 'تم إعادة ترتيب الشرائح بنجاح');
    }

    public function storeTestimonial(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:2000'],
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = Testimonial::max('sort_order') + 1;
        }

        Testimonial::create($validated);

        return redirect()->back()->with('success', 'تم إضافة الشهادة بنجاح');
    }

    public function updateTestimonial(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:2000'],
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        } else {
            unset($validated['avatar']);
        }

        $testimonial->update($validated);

        return redirect()->back()->with('success', 'تم تحديث الشهادة بنجاح');
    }

    public function destroyTestimonial(Testimonial $testimonial)
    {
        $testimonial->delete();

        return redirect()->back()->with('success', 'تم حذف الشهادة بنجاح');
    }

    public function storeStep(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = Step::max('sort_order') + 1;
        }

        Step::create($validated);

        return redirect()->back()->with('success', 'تم إضافة الخطوة بنجاح');
    }

    public function updateStep(Request $request, Step $step)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $step->update($validated);

        return redirect()->back()->with('success', 'تم تحديث الخطوة بنجاح');
    }

    public function destroyStep(Step $step)
    {
        $step->delete();

        return redirect()->back()->with('success', 'تم حذف الخطوة بنجاح');
    }

    public function storeFeature(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = Feature::max('sort_order') + 1;
        }

        Feature::create($validated);

        return redirect()->back()->with('success', 'تم إضافة الميزة بنجاح');
    }

    public function updateFeature(Request $request, Feature $feature)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $feature->update($validated);

        return redirect()->back()->with('success', 'تم تحديث الميزة بنجاح');
    }

    public function destroyFeature(Feature $feature)
    {
        $feature->delete();

        return redirect()->back()->with('success', 'تم حذف الميزة بنجاح');
    }
}
