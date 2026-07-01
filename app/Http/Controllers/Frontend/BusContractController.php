<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BusContract;
use App\Models\ContractAttachment;
use App\Models\User;
use App\Notifications\NewBusContract;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BusContractController extends Controller
{
    public function create()
    {
        $attachments = ContractAttachment::where('is_enabled', true)->orderBy('name')->get();

        return Inertia::render('BusContracts/Create', [
            'attachments' => $attachments,
        ]);
    }

    public function store(Request $request)
    {
        $requiredAttachments = ContractAttachment::where('is_enabled', true)
            ->where('is_required', true)
            ->pluck('id')
            ->toArray();

        $rules = [
            'owner_name' => 'required|string|max:255',
            'same_driver' => 'boolean',
            'owner_phone' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:255',
            'driver_phone' => 'nullable|string|max:50',
            'license' => 'required|string|max:255',
            'van_color' => 'required|string|max:100',
            'seats_count' => 'required|integer|min:1',
            'location' => 'required|string|max:500',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
        ];

        foreach ($requiredAttachments as $reqId) {
            $rules["attachments.$reqId"] = 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120';
        }

        $validated = $request->validate($rules);

        $contract = BusContract::create($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachmentId => $file) {
                $attachment = ContractAttachment::find($attachmentId);
                if ($attachment && $attachment->is_enabled) {
                    $path = $file->store('contract-attachments', 'public');
                    $contract->attachments()->attach($attachmentId, ['file_path' => $path]);
                }
            }
        }

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewBusContract($contract));
        }

        return redirect()->back()->with('message', 'تم إضافة عقد الباص بنجاح');
    }
}
