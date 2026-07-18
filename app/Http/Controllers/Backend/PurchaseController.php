<?php

namespace App\Http\Controllers\Backend;

use App\Events\PurchaseUpdated;
use App\Http\Controllers\Controller;
use App\Models\BalanceTransaction;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserBalance;
use App\Notifications\NewPurchaseAssignment;
use App\Notifications\PurchaseApproved;
use App\Notifications\PurchaseRejected;
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
        $employees = User::where('role', '!=', 'admin')
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
        $purchase->load('creator', 'employees', 'approver');

        $employee = $purchase->employees->first();
        $employeeBalance = null;
        if ($employee) {
            $balance = UserBalance::where('user_id', $employee->id)->first();
            $employeeBalance = $balance ? $balance->balance : 0;
        }

        return Inertia::render('Admin/Purchases/Show', [
            'purchase' => $purchase,
            'employeeBalance' => $employeeBalance,
        ]);
    }

    public function approve(Request $request, Purchase $purchase)
    {
        if ($purchase->status != Purchase::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'لا يمكن الموافقة على هذا الطلب');
        }

        $employee = $purchase->employees->first();
        if (!$employee) {
            return redirect()->back()->with('error', 'لا يوجد موظف مكلف بهذا الطلب');
        }

        $userBalance = UserBalance::where('user_id', $employee->id)->first();
        $currentBalance = $userBalance ? $userBalance->balance : 0;
        $deductAmount = $purchase->actual_amount ?? $purchase->amount;

        // if ($currentBalance < $deductAmount) {
        //     return redirect()->back()->with('error', "الرصيد غير كافٍ. الرصيد الحالي: {$currentBalance} ريال، المبلغ المطلوب: {$deductAmount} ريال");
        // }

        $newBalance = $currentBalance - $deductAmount;

        if ($userBalance) {
            $userBalance->update(['balance' => $newBalance]);
        } else {
            UserBalance::create([
                'user_id' => $employee->id,
                'balance' => $newBalance,
            ]);
        }

        BalanceTransaction::create([
            'user_id' => $employee->id,
            'amount' => -$deductAmount,
            'balance_after' => $newBalance,
            'description' => "خصم مشتريات - طلب #{$purchase->id}: {$purchase->item_name}",
            'added_by' => auth()->id(),
        ]);

        $purchase->update([
            'status' => Purchase::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        $employee->notify(new PurchaseApproved($purchase));
        PurchaseUpdated::dispatch('approved', $purchase);

        return redirect()->route('admin.purchases.show', $purchase)
            ->with('success', 'تم الموافقة على الطلب وخصم المبلغ من رصيد الموظف');
    }

    public function reject(Request $request, Purchase $purchase)
    {
        if ($purchase->status != Purchase::STATUS_COMPLETED) {
            return redirect()->back()->with('error', 'لا يمكن رفض هذا الطلب');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $purchase->update([
            'status' => Purchase::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $employee = $purchase->employees->first();
        if ($employee) {
            $employee->notify(new PurchaseRejected($purchase));
        }

        PurchaseUpdated::dispatch('rejected', $purchase);

        return redirect()->route('admin.purchases.show', $purchase)
            ->with('success', 'تم رفض الطلب');
    }
}
