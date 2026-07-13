<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BalanceTransaction;
use App\Models\User;
use App\Models\UserBalance;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BalanceController extends Controller
{
    public function index()
    {
        $balances = UserBalance::with('user')
            ->latest()
            ->get();

        return Inertia::render('Admin/Balances/Index', [
            'balances' => $balances,
        ]);
    }

    public function create()
    {
        $users = User::select('id', 'name', 'email', 'role')
            ->get();

        return Inertia::render('Admin/Balances/Create', [
            'users' => $users,
        ]);
    }

    public function getUserBalance(User $user)
    {
        $balance = UserBalance::where('user_id', $user->id)->first();

        return response()->json([
            'user_id' => $user->id,
            'name' => $user->name,
            'balance' => $balance ? $balance->balance : 0,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:500',
        ]);

        $userBalance = UserBalance::firstOrCreate(
            ['user_id' => $validated['user_id']],
            ['balance' => 0]
        );

        $oldBalance = $userBalance->balance;
        $newBalance = $oldBalance + $validated['amount'];

        $userBalance->update(['balance' => $newBalance]);

        BalanceTransaction::create([
            'user_id' => $validated['user_id'],
            'amount' => $validated['amount'],
            'balance_after' => $newBalance,
            'description' => $validated['description'],
            'added_by' => auth()->id(),
        ]);

        return redirect()->route('admin.balances.index')
            ->with('success', 'تم إضافة الرصيد بنجاح');
    }
}
