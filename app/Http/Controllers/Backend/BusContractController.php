<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BusContract;
use Inertia\Inertia;

class BusContractController extends Controller
{
    public function index()
    {
        $contracts = BusContract::with('attachments')->latest()->get();

        return Inertia::render('Admin/BusContracts/Index', [
            'contracts' => $contracts,
        ]);
    }

    public function show(BusContract $contract)
    {
        $contract->load('attachments');

        return Inertia::render('Admin/BusContracts/Show', [
            'contract' => $contract,
        ]);
    }

    public function destroy(BusContract $contract)
    {
        $contract->delete();

        return redirect()->back()->with('success', 'تم حذف العقد بنجاح');
    }
}
