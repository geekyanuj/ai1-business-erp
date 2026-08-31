// export function calculateItem(item) {
//     const qty = parseFloat(item.quantity || 0);
//     const price = parseFloat(item.unit_price || 0);

//     const gross = qty * price;

//     let discount = 0;

//     if (item.discount_percent) {
//         discount = gross * (parseFloat(item.discount_percent) / 100);
//     } else if (item.discount_amount) {
//         discount = parseFloat(item.discount_amount);
//     }

//     const taxable = Math.max(gross - discount, 0);
//     const taxRate = parseFloat(item.tax_rate || 0);
//     const tax = taxable * taxRate / 100;

//     return {
//         gross_amount: gross.toFixed(2),
//         discount_amount: discount.toFixed(2),
//         taxable_amount: taxable.toFixed(2),
//         tax_amount: tax.toFixed(2),
//         total_with_tax: (taxable + tax).toFixed(2)
//     };
// }

export function calculateItem(item) {
    const qty = parseFloat(item.quantity || 0);
    const price = parseFloat(item.unit_price || 0);

    const gross = qty * price;

    const discountPercent = parseFloat(item.discount_percent || 0);
    const discountAmount = gross * discountPercent / 100;

    const taxable = Math.max(gross - discountAmount, 0);

    const taxRate = parseFloat(item.tax_rate || 0);
    const taxAmount = taxable * taxRate / 100;

    return {
        gross_amount: gross.toFixed(2),

        // UI only
        discount_percent: discountPercent.toFixed(2),
        discount_amount: discountAmount.toFixed(2),

        taxable_amount: taxable.toFixed(2),
        tax_amount: taxAmount.toFixed(2),
        total_with_tax: (taxable + taxAmount).toFixed(2)
    };
}

export function calculateTotals(items, taxType) {
    let subtotal = 0;
    let taxTotal = 0;

    items.forEach(item => {
        subtotal += parseFloat(item.taxable_amount || 0);
        taxTotal += parseFloat(item.tax_amount || 0);
    });

    let totals = {
        subtotal: subtotal.toFixed(2),
        cgst_amount: '0.00',
        sgst_amount: '0.00',
        igst_amount: '0.00',
    };

    if (taxType === 'cgst_sgst') {
        totals.cgst_amount = (taxTotal / 2).toFixed(2);
        totals.sgst_amount = (taxTotal / 2).toFixed(2);
    } else {
        totals.igst_amount = taxTotal.toFixed(2);
    }

    totals.grand_total = (
        subtotal +
        parseFloat(totals.cgst_amount) +
        parseFloat(totals.sgst_amount) +
        parseFloat(totals.igst_amount)
    ).toFixed(2);

    return totals;
}
