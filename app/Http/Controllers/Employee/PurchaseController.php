<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Notifications\PurchaseCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('creator')
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

        if ($purchase->status === 'completed') {
            return redirect()->back()->with('error', 'تم إكمال هذا الطلب مسبقاً');
        }

        $rules = [
            'actual_amount' => 'required|numeric|min:0',
        ];

        if ($purchase->requires_invoice) {
            $rules['invoice_image'] = 'required|image|mimes:jpg,jpeg,png|max:2048';
        } else {
            $rules['invoice_image'] = 'nullable|image|mimes:jpg,jpeg,png|max:2048';
        }

        $validated = $request->validate($rules);

        $data = [
            'actual_amount' => $validated['actual_amount'],
            'remaining_amount' => $purchase->amount - $validated['actual_amount'],
            'status' => 'completed',
            'completed_at' => now(),
        ];

        if ($request->hasFile('invoice_image')) {
            $data['invoice_image'] = $request->file('invoice_image')->store('invoices', 'public');
        }

        $purchase->update($data);

        $purchase->creator->notify(new PurchaseCompleted($purchase));

        return redirect()->back()->with('success', 'تم إكمال طلب الشراء بنجاح');
    }
}
