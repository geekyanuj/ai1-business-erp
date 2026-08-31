import $ from "jquery";

export function handlePOItemsTable({ container }) {

    const $container = $(container);
    if (!$container.length) return;

    const $itemsTable = $container.find('#itemsTable');
    const $taxType = $container.find('#tax_type');
    const $subtotal = $container.find('#subtotal');
    const $totalTax = $container.find('#total_tax');
    const $grandTotal = $container.find('#grand_total');

    /* ------------------------------
       SERIAL NUMBERS
    ------------------------------ */
    function updateSerials() {
        $itemsTable.find('tbody tr').each((i, tr) => {
            $(tr).find('.serial').text(i + 1);
        });
    }

    /* ------------------------------
       ROW CALCULATION
    ------------------------------ */
    function calculateRow($row) {
        const qty = Number($row.find('.qty').val()) || 0;
        const rate = Number($row.find('.rate').val()) || 0;
        const taxRate = Number($row.find('[name="items[tax_rate][]"]').val()) || 0;

        const taxable = qty * rate;
        const taxAmount = taxable * taxRate / 100;
        const total = taxable + taxAmount;

        $row.find('[name="items[tax_amount][]"]').val(taxAmount.toFixed(2));
        $row.find('[name="items[total_with_tax][]"]').val(total.toFixed(2));

        return { taxable, taxAmount };
    }

    /* ------------------------------
       TOTAL CALCULATION
    ------------------------------ */
    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;

        $itemsTable.find('tbody tr').each(function () {
            const result = calculateRow($(this));
            subtotal += result.taxable;
            totalTax += result.taxAmount;
        });

        $subtotal.val(subtotal.toFixed(2));
        $totalTax.val(totalTax.toFixed(2));
        $grandTotal.val((subtotal + totalTax).toFixed(2));
    }

    /* ------------------------------
       ADD ROW
    ------------------------------ */
    $container.on('click', '.addItemRow, #addItemRow', function () {
        const row = `
        <tr>
            <td class="serial text-center"></td>

            <td>
                <input type="text" name="items[product_name][]" class="form-control form-control-sm" required>
                <input type="hidden" name="items[product_id][]">
            </td>

            <td>
                <input type="text" name="items[product_description][]" class="form-control form-control-sm">
            </td>

            <td>
                <input type="text" name="items[hsn_code][]" class="form-control form-control-sm">
            </td>

            <td>
                <input type="number" name="items[quantity][]" class="form-control form-control-sm qty" value="1" min="1" required>
            </td>

            <td>
                <input type="number" name="items[unit_price][]" class="form-control form-control-sm rate" step="0.01" min="0" required>
            </td>

            <td>
                <input type="text" name="items[uom][]" class="form-control form-control-sm">
            </td>

            <td>
                <input type="number" name="items[tax_rate][]" class="form-control form-control-sm" value="18" min="0" required>
            </td>

            <td>
                <input type="number" name="items[tax_amount][]" class="form-control form-control-sm" min="0" readonly>
            </td>

            <td>
                <input type="number" name="items[total_with_tax][]" class="form-control form-control-sm" min="0" readonly>
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
            </td>
        </tr>`;

        $itemsTable.find('tbody').append(row);
        updateSerials();
    });

    /* ------------------------------
       REMOVE ROW
    ------------------------------ */
    $container.on('click', '.removeRow', function () {
        if ($itemsTable.find('tbody tr').length > 1) {
            $(this).closest('tr').remove();
            updateSerials();
            calculateTotals();
        }
    });

    /* ------------------------------
       LIVE RECALCULATION
    ------------------------------ */
    $container.on(
        'input',
        '.qty, .rate, [name="items[tax_rate][]"]',
        calculateTotals
    );

    /* ------------------------------
       TAX TYPE CHANGE
    ------------------------------ */
    $taxType.on('change', calculateTotals);

    /* ------------------------------
       INIT
    ------------------------------ */
    updateSerials();
    calculateTotals();
}
