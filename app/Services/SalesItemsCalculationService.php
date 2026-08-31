<?php

namespace App\Services;

use Illuminate\Support\Collection;

class SalesItemsCalculationService
{
    /**
     * Calculate a single item
     */
    // public function calculateItem(array $item): array
    // {
    //     $qty = (float) $item['quantity'];
    //     $price = (float) $item['unit_price'];

    //     $gross = $qty * $price;

    //     // Discount
    //     $discountAmount = 0;

    //     if (!empty($item['discount_percent'])) {
    //         $discountAmount = $gross * ($item['discount_percent'] / 100);
    //     } elseif (!empty($item['discount_amount'])) {
    //         $discountAmount = (float) $item['discount_amount'];
    //     }

    //     $taxableAmount = max($gross - $discountAmount, 0);

    //     $taxRate = (float) ($item['tax_rate'] ?? 0);
    //     $taxAmount = $taxableAmount * ($taxRate / 100);

    //     return [
    //         'gross_amount' => round($gross, 2),
    //         'discount_amount' => round($discountAmount, 2),
    //         'taxable_amount' => round($taxableAmount, 2),
    //         'tax_amount' => round($taxAmount, 2),
    //         'total_with_tax' => round($taxableAmount + $taxAmount, 2),
    //     ];
    // }

    public function calculateItem(array $item): array
    {
        $qty = (float) ($item['quantity'] ?? 0);
        $price = (float) ($item['unit_price'] ?? 0);

        $gross = $qty * $price;

        // ✅ ALWAYS normalize discount percent
        $discountPercent = isset($item['discount_percent'])
            ? (float) $item['discount_percent']
            : 0;

        // ✅ Calculate discount ONLY from percent
        $discountAmount = ($gross * $discountPercent) / 100;

        $taxableAmount = max($gross - $discountAmount, 0);

        $taxRate = (float) ($item['tax_rate'] ?? 0);
        $taxAmount = ($taxableAmount * $taxRate) / 100;

        return [
            'gross_amount' => round($gross, 2),

            // 🔥 THIS WAS MISSING
            'discount_percent' => round($discountPercent, 2),
            'discount_amount' => round($discountAmount, 2),

            'taxable_amount' => round($taxableAmount, 2),
            'tax_amount' => round($taxAmount, 2),
            'total_with_tax' => round($taxableAmount + $taxAmount, 2),
        ];
    }

    /**
     * Calculate totals for multiple items
     */
    public function calculateTotals(Collection $items, string $taxType): array
    {
        $subtotal = 0;
        $taxTotal = 0;

        foreach ($items as $item) {
            $subtotal += $item['taxable_amount'];
            $taxTotal += $item['tax_amount'];
        }

        $totals = [
            'subtotal' => round($subtotal, 2),
            'cgst_amount' => 0,
            'sgst_amount' => 0,
            'igst_amount' => 0,
        ];

        if ($taxType === 'cgst_sgst') {
            $cgstPercent = (float) \App\Models\Setting::get('cgst_division_percentage', 50);
            $sgstPercent = (float) \App\Models\Setting::get('sgst_division_percentage', 50);
            
            // Normalize to 100 if they don't add up, or just use as ratio
            $totalDivision = $cgstPercent + $sgstPercent;
            if ($totalDivision > 0) {
                $totals['cgst_amount'] = round($taxTotal * ($cgstPercent / $totalDivision), 2);
                $totals['sgst_amount'] = round($taxTotal * ($sgstPercent / $totalDivision), 2);
            }
        } else {
            $totals['igst_amount'] = round($taxTotal, 2);
        }

        $totals['grand_total'] =
            $totals['subtotal'] +
            $totals['cgst_amount'] +
            $totals['sgst_amount'] +
            $totals['igst_amount'];

        return $totals;
    }

    /**
     * Determine tax type based on company branch and client address
     */
    public function determineTaxType($branchState, $clientState): string
    {
        if (!$branchState || !$clientState) {
            return 'cgst_sgst'; // Default fallback
        }

        $branchState = strtolower(trim($branchState));
        $clientState = strtolower(trim($clientState));

        if ($branchState === $clientState) {
            return 'cgst_sgst';
        }

        return 'igst';
    }
}
