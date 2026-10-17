<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /** index page */
    public function invoiceList()
    {
        $invoiceList = Invoice::with('items')->latest()->get();

        return view('invoices.list_invoices', compact('invoiceList'));
    }

    /** invoice paid page */
    public function invoicePaid()
    {
        return view('invoices.tab.paid_invoices');
    }

    /** incoice overdue page*/
    public function invoiceOverdue()
    {
        return view('invoices.tab.overdue_invoices');
    }

    /** invoice draft */
    public function invoiceDraft()
    {
        return view('invoices.tab.draft_invoices');
    }

    /** recurring invoices.blade */
    public function invoiceRecurring()
    {
        return view('invoices.tab.recurring_invoices');
    }

    /** invoice cancelled */
    public function invoiceCancelled()
    {
        return view('invoices.tab.cancelled_invoices');
    }

    /** invoice grid */
    public function invoiceGrid()
    {
        $invoiceList = Invoice::latest()->get();

        return view('invoices.grid_invoice', compact('invoiceList'));
    }

    /** invoice add page */
    public function invoiceAdd()
    {
        $users = $this->customers();

        return view('invoices.invoice_add', compact('users'));
    }

    /** save record incoice */
    public function saveRecord(Request $request)
    {
        $validated = $this->validateInvoice($request);

        DB::beginTransaction();
        try {
            $invoice = Invoice::create($this->invoiceAttributes($request, $validated) + [
                'signature_path' => $this->storeSignature($request),
            ]);
            $invoice->items()->createMany($this->itemRows($validated));

            DB::commit();

            return redirect()->back()->with('success', 'Invoice created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Invoice creation failed', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Failed to create invoice. Please try again.');
        }
    }

    /** invoice edit */
    public function invoiceEdit(Invoice $invoice)
    {
        $invoice->load('items');
        $users = $this->customers();

        return view('invoices.invoice_edit', compact('invoice', 'users'));
    }

    /** Update Record */
    public function updateRecord(Request $request)
    {
        $invoice = Invoice::where('invoice_number', $request->invoice_number)->firstOrFail();
        $validated = $this->validateInvoice($request);

        DB::beginTransaction();
        try {
            $attributes = $this->invoiceAttributes($request, $validated);

            if ($request->hasFile('signature')) {
                $this->deleteSignature($invoice->signature_path);
                $attributes['signature_path'] = $this->storeSignature($request);
            }

            $invoice->update($attributes);

            // Replace the line items so added and removed rows are both saved
            $invoice->items()->delete();
            $invoice->items()->createMany($this->itemRows($validated));

            DB::commit();

            return redirect()->back()->with('success', 'Invoice updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Invoice update failed', ['error' => $e->getMessage(), 'invoice' => $invoice->invoice_number]);

            return redirect()->back()->with('error', 'Failed to update invoice. Please try again.');
        }
    }

    /** Delete Record */
    public function deleteRecord(Request $request)
    {
        $invoice = Invoice::where('invoice_number', $request->invoice_number)->firstOrFail();

        try {
            $signaturePath = $invoice->signature_path;
            $invoice->delete(); // items are removed by the foreign key cascade
            $this->deleteSignature($signaturePath);

            return redirect()->route('invoice/list/page')->with('success', 'Record deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Invoice delete failed', ['error' => $e->getMessage(), 'invoice' => $invoice->invoice_number]);

            return redirect()->back()->with('error', 'Failed to delete record. Please try again.');
        }
    }

    /** invoice view */
    public function invoiceView(Invoice $invoice)
    {
        $invoice->load('items');

        return view('invoices.invoice_view', compact('invoice'));
    }

    /** invoice settings */
    public function invoiceSettings()
    {
        return view('invoices.settings.settings_invoices');
    }

    /** invoice settingst tax */
    public function invoiceSettingsTax()
    {
        return view('invoices.settings.settings_tax');
    }

    /** invoice settings bank */
    public function invoiceSettingsBank()
    {
        return view('invoices.settings.settings_bank');
    }

    /** users who can be billed */
    private function customers()
    {
        return User::whereIn('role_name', [Role::Student, Role::Client])->orderBy('name')->get();
    }

    private function validateInvoice(Request $request): array
    {
        return $request->validate([
            'customer_id' => 'required|exists:users,id',
            'po_number' => 'required|string|max:50',
            'date' => 'nullable|date',
            'due_date' => 'required|date',
            'by_month' => 'nullable|string|max:20',
            'month' => 'nullable|string|max:50',
            'invoice_from' => 'nullable|string',
            'invoice_to' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*' => 'required|string|max:255',
            'category.*' => 'required|string|max:255',
            'quantity.*' => 'required|numeric|min:0',
            'price.*' => 'required|numeric|min:0',
            'amount.*' => 'required|numeric|min:0',
            'discount.*' => 'nullable|numeric|min:0|max:100',
            'taxable_amount' => 'nullable|numeric',
            'round_off' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
            'service_charge.*' => 'numeric',
            'offer_new.*' => 'numeric',
            'account_holder_name' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:20',
            'account_number' => 'nullable|string|max:50',
            'terms_and_conditions' => 'nullable|string',
            'notes' => 'nullable|string',
            'signature' => 'nullable|image|max:2048',
            'signatory_name' => 'required|string|max:255',
        ]);
    }

    /** map validated form input to invoice columns */
    private function invoiceAttributes(Request $request, array $validated): array
    {
        return [
            'customer_id' => $validated['customer_id'],
            'customer_name' => User::findOrFail($validated['customer_id'])->name,
            'po_number' => $validated['po_number'],
            'invoice_date' => $validated['date'] ?? now(),
            'due_date' => $validated['due_date'],
            'enable_tax' => $request->boolean('enable_tax'),
            'is_recurring' => $request->boolean('is_recurring'),
            'recurring_by_month' => $validated['by_month'] ?? null,
            'recurring_month' => $validated['month'] ?? null,
            'invoice_from' => $validated['invoice_from'] ?? null,
            'invoice_to' => $validated['invoice_to'] ?? null,
            'taxable_amount' => $validated['taxable_amount'] ?? 0,
            'round_off' => $validated['round_off'] ?? 0,
            'additional_charges' => array_values($validated['service_charge'] ?? []),
            'discounts' => array_values($validated['offer_new'] ?? []),
            'total_amount' => $validated['total_amount'] ?? 0,
            'account_holder_name' => $validated['account_holder_name'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'ifsc_code' => $validated['ifsc_code'] ?? null,
            'account_number' => $validated['account_number'] ?? null,
            'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'signatory_name' => $validated['signatory_name'],
        ];
    }

    /** build invoice_items rows from the parallel form arrays */
    private function itemRows(array $validated): array
    {
        return collect($validated['items'])->map(fn ($item, $key) => [
            'item' => $item,
            'category' => $validated['category'][$key],
            'quantity' => $validated['quantity'][$key],
            'price' => $validated['price'][$key],
            'amount' => $validated['amount'][$key],
            'discount' => $validated['discount'][$key] ?? 0,
        ])->values()->all();
    }

    private function storeSignature(Request $request): ?string
    {
        if (! $request->hasFile('signature')) {
            return null;
        }

        $file = $request->file('signature');
        $filename = uniqid('sign_', true).'.'.$file->extension();
        $file->move(public_path('upload_sign'), $filename);

        return 'upload_sign/'.$filename;
    }

    private function deleteSignature(?string $path): void
    {
        if ($path && File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
