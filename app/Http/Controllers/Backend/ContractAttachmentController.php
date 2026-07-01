<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ContractAttachment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContractAttachmentController extends Controller
{
    public function index()
    {
        $attachments = ContractAttachment::orderBy('name')->get();

        return Inertia::render('Admin/ContractAttachments', [
            'attachments' => $attachments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contract_attachments,name',
            'is_required' => 'boolean',
        ]);

        ContractAttachment::create($validated);

        return redirect()->back()->with('message', 'تم إضافة المرفق بنجاح');
    }

    public function update(Request $request, ContractAttachment $contractAttachment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contract_attachments,name,' . $contractAttachment->id,
            'is_enabled' => 'boolean',
            'is_required' => 'boolean',
        ]);

        $contractAttachment->update($validated);

        return redirect()->back()->with('message', 'تم تحديث المرفق بنجاح');
    }

    public function toggle(ContractAttachment $contractAttachment)
    {
        $contractAttachment->update([
            'is_enabled' => !$contractAttachment->is_enabled,
        ]);

        return redirect()->back()->with('message', 'تم تغيير حالة المرفق بنجاح');
    }

    public function destroy(ContractAttachment $contractAttachment)
    {
        $contractAttachment->delete();

        return redirect()->back()->with('message', 'تم حذف المرفق بنجاح');
    }
}
