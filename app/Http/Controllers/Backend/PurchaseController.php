<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\User;
use App\Notifications\NewPurchaseAssignment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::with('creator', 'employees')
            ->latest()
            ->get();

        return Inertia::render('Admin/Purchases/Index', [
            'purchases' => $purchases,
        ]);
    }

    public function create()
    {
        $employees = User::where('role', 'employee')->get(['id', 'name']);

        return Inertia::render('Admin/Purchases/Create', [
            'employees' => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'requires_invoice' => 'boolean',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:users,id',
        ]);

        $purchase = Purchase::create([
            'item_name' => $validated['item_name'],
            'amount' => $validated['amount'],
            'notes' => $validated['notes'],
            'requires_invoice' => $validated['requires_invoice'] ?? false,
            'created_by' => auth()->id(),
        ]);

        $purchase->employees()->attach($validated['employee_ids']);

        $employees = User::whereIn('id', $validated['employee_ids'])->get();
        foreach ($employees as $employee) {
            $employee->notify(new NewPurchaseAssignment($purchase));
        }

        return redirect()->route('admin.purchases.index')
            ->with('success', 'تم إضافة طلب الشراء بنجاح');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('creator', 'employees');

        return Inertia::render('Admin/Purchases/Show', [
            'purchase' => $purchase,
        ]);
    }
}
