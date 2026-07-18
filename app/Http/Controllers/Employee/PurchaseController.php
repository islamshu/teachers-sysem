<?php

namespace App\Http\Controllers\Employee;

use App\Events\PurchaseUpdated;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\User;
use App\Notifications\PurchaseCompleted;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('creator', 'approver')
            ->whereHas('employees', fn($q) => $q->where('user_id', auth()->id()))
            ->latest()
            ->get();

        return Inertia::render('Employee/Purchases/Index', [
            'purchases' => $purchases,
        ]);
    }

    public function complete(Request $request, Purchase $purchase)
    {
        $user = auth()->user();

        $isAssigned = $purchase->employees()->where('user_id', $user->id)->exists();
        if (!$isAssigned) {
            abort(403);
        }

        if ($purchase->status != Purchase::STATUS_PENDING) {
            return redirect()->back()->with('error', 'لا يمكن إتمام هذا الطلب');
        }

        $rules = [
            'actual_amount' => 'required|numeric|min:0',
        ];

        if ($purchase->requires_invoice) {
            $rules['invoice_image'] = 'required|image|mimes:jpg,jpeg,png';
        } else {
            $rules['invoice_image'] = 'nullable|image|mimes:jpg,jpeg,png';
        }

        $validated = $request->validate($rules);

        $data = [
            'actual_amount' => $validated['actual_amount'],
            'remaining_amount' => $purchase->amount - $validated['actual_amount'],
            'status' => Purchase::STATUS_COMPLETED,
            'completed_at' => now(),
        ];

        if ($request->hasFile('invoice_image')) {
            $data['invoice_image'] = $request->file('invoice_image')->store('invoices', 'public');
        }

        $purchase->update($data);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            $admin->notify(new PurchaseCompleted($purchase));
        }

        PurchaseUpdated::dispatch('completed', $purchase);

        return redirect()->back()->with('success', 'تم إرسال طلب الشراء للمراجعة');
    }
}
