<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SlideController extends Controller
{
    public function index()
    {
        $slides = Slide::ordered()->get();

        return Inertia::render('Admin/Slides', [
            'slides' => $slides,
        ]);
    }

    public function store(Request $request)
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

    public function update(Request $request, Slide $slide)
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

    public function destroy(Slide $slide)
    {
        $slide->delete();

        return redirect()->back()->with('success', 'تم حذف الشريحة بنجاح');
    }

    public function reorder(Request $request)
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
}
