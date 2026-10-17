<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\FeeCollection;
use App\Models\FeeType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AccountsController extends Controller
{
    /** index page */
    public function index()
    {
        $feeCollections = FeeCollection::with(['student', 'feeType'])->latest('paid_date')->get();
        return view('accounts.feescollections',compact('feeCollections'));
    }

    /** add Fees Collection */
    public function addFeesCollection()
    {
        $users    = User::where('role_name', Role::Student)->orderBy('name')->get();
        $feeTypes = FeeType::orderBy('name')->get();
        return view('accounts.add-fees-collection',compact('users','feeTypes'));
    }

    /** Save Record */
    public function saveRecord(Request $request)
    {
        $validated = $request->validate([
            'student_id'  => 'required|exists:users,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'amount'      => 'required|numeric|min:0',
            'paid_date'   => 'required|date',
        ]);

        try {
            $feeCollection = FeeCollection::create($validated);

            Log::info('New fees record saved successfully', ['fee_collection_id' => $feeCollection->id]);
            return redirect()->back()->with('success', 'Fees record saved successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to save fees record', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to save fees record. Please try again.');
        }
    }
}
