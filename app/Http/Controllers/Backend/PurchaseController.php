<?php

namespace App\Http\Controllers\Backend;

use App\Events\PurchaseUpdated;
use App\Http\Controllers\Controller;
use App\Models\BalanceTransaction;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserBalance;
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

    public function indexAll()
    {
        $user = auth()->user();

        $purchases = $user->purchases()
            ->with('creator', 'employees')
            ->latest()
            ->get();

        $canManage = $user->can('إدارة المشتريات');

        $userBalance = UserBalance::where('user_id', $user->id)->first();
        $balance = $userBalance ? $userBalance->balance : 0;

        $transactions = BalanceTransaction::where('user_id', $user->id)
            ->with('addedBy')
            ->latest()
            ->get();

        return Inertia::render('Purchases/Index', [
            'purchases' => $purchases,
            'canManage' => $canManage,
            'balance' => $balance,
            'transactions' => $transactions,
        ]);
    }

    public function create()
    {
        $employees = User::where('role', 'employee')
            ->whereHas('userBalance', fn ($q) => $q->where('balance', '>', 0))
            ->get(['id', 'name']);

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
            'employee_id' => 'required|exists:users,id',
        ]);

        $purchase = Purchase::create([
            'item_name' => $validated['item_name'],
            'amount' => $validated['amount'],
            'notes' => $validated['notes'],
            'requires_invoice' => $validated['requires_invoice'] ?? false,
            'created_by' => auth()->id(),
        ]);

        $purchase->employees()->attach([$validated['employee_id']]);

        $employee = User::find($validated['employee_id']);
        $employee->notify(new NewPurchaseAssignment($purchase));

        PurchaseUpdated::dispatch('created', $purchase);

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
