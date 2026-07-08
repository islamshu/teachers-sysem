<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->paginate(10);

        if (request()->wantsJson()) {
            return response()->json($branches);
        }

        return Inertia::render('Admin/Branches/Index', [
            'branches' => $branches->items(),
            'nextPage' => $branches->currentPage() < $branches->lastPage() ? $branches->currentPage() + 1 : null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches,name'],
        ]);

        Branch::create($validated);

        return redirect()->back()->with('success', 'تم إنشاء الفرع بنجاح');
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches,name,' . $branch->id],
        ]);

        $branch->update($validated);

        return redirect()->back()->with('success', 'تم تحديث الفرع بنجاح');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->back()->with('success', 'تم حذف الفرع بنجاح');
    }
}
