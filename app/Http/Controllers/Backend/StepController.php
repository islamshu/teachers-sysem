<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Step;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StepController extends Controller
{
    public function index()
    {
        $steps = Step::ordered()->get();

        return Inertia::render('Admin/Steps', [
            'steps' => $steps,
        ]);
    }

    public function store(Request $request)
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

    public function update(Request $request, Step $step)
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

    public function destroy(Step $step)
    {
        $step->delete();

        return redirect()->back()->with('success', 'تم حذف الخطوة بنجاح');
    }
}
