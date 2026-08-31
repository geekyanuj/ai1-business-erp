<?php
namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesInvoicePaymentController extends Controller
{
    public function store(Request $request, SalesInvoice $invoice)
    {
        // Calculate remaining balance
        $alreadyPaid = $invoice->payments()->sum('amount');
        $remaining = $invoice->grand_total - $alreadyPaid;

        if ($remaining <= 0) {
            return back()->with('error', 'Invoice is already fully paid.');
        }

        $validated = $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) use ($remaining) {
                    if ($value > $remaining) {
                        $fail("Payment amount cannot exceed remaining balance of ₹{$remaining}.");
                    }
                },
            ],
            'payment_mode' => 'required|string',
            'reference_no' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255|unique:payments',
            'paid_at' => 'required|date',
        ]);

        DB::transaction(function () use ($invoice, $validated) {

            // Create payment
            $invoice->payments()->create([
                ...$validated,
                'created_by' => auth()->id(),
            ]);

            // Recalculate totals (inside transaction = SAFE)
            $paidAmount = $invoice->payments()->sum('amount');
            $balance = max($invoice->grand_total - $paidAmount, 0);

            $status = match (true) {
                $balance == 0 => 'paid',
                $paidAmount > 0 => 'partially_paid',
                default => 'issued',
            };

            $invoice->update([
                'paid_amount' => $paidAmount,
                'balance_amount' => $balance,
                'status' => $status,
            ]);

            activity()
                ->causedBy(auth()->user())
                ->performedOn($invoice)
                ->log("Payment of ₹{$validated['amount']} received");
        });

        return back()->with('success', 'Payment recorded successfully.');
    }

}
