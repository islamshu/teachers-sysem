<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LookupController extends Controller
{
    public function subjects()
    {
        $subjects = Subject::orderBy('name')->get();
        return Inertia::render('Admin/Subjects', [
            'subjects' => $subjects,
        ]);
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
        ]);

        Subject::create($validated);

        return redirect()->back()->with('message', 'تم إضافة المادة بنجاح');
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
        ]);

        $subject->update($validated);

        return redirect()->back()->with('message', 'تم تحديث المادة بنجاح');
    }

    public function destroySubject(Subject $subject)
    {
        if ($subject->teacherProfiles()->exists()) {
            return redirect()->back()->with('message', 'لا يمكن حذف المادة لأنها مرتبطة بمدرسين');
        }

        $subject->delete();

        return redirect()->back()->with('message', 'تم حذف المادة بنجاح');
    }

    public function grades()
    {
        $grades = Grade::orderBy('name')->get();
        return Inertia::render('Admin/Grades', [
            'grades' => $grades,
        ]);
    }

    public function storeGrade(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:grades,name',
        ]);

        Grade::create($validated);

        return redirect()->back()->with('message', 'تم إضافة الصف بنجاح');
    }

    public function updateGrade(Request $request, Grade $grade)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:grades,name,' . $grade->id,
        ]);

        $grade->update($validated);

        return redirect()->back()->with('message', 'تم تحديث الصف بنجاح');
    }

    public function destroyGrade(Grade $grade)
    {
        $grade->teacherProfiles()->detach();
        $grade->delete();

        return redirect()->back()->with('message', 'تم حذف الصف بنجاح');
    }
}
