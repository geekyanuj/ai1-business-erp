// ----------------------------------------------------
// Initialize Choices
// ----------------------------------------------------
export function initChoices(element, isModal = false) {
    if (!element) return;

    if (element.choices) {
        try {
            element.choices.destroy();
        } catch (e) {}
    }

    const instance = new Choices(element, {
        searchEnabled: true,
        itemSelectText: "",
        allowHTML: false,
        shouldSort: false,
        position: isModal ? "bottom" : "auto",
    });

    element.choices = instance;
    return instance;
}

// ----------------------------------------------------
// Invoice / Quotation / Proforma Item Handler (REUSABLE)
// ----------------------------------------------------
export function handleSalesItems({
    enableDiscount = false,
    tableSelector = "#itemsTable",
    addButtonSelector = "#addItemRow",
    subtotalSelector = "#subtotal",
    taxSelector = "#total_tax",
    grandTotalSelector = "#grand_total",
    cgstSelector = "#cgst_amount",
    sgstSelector = "#sgst_amount",
    igstSelector = "#igst_amount",
    taxTypeSelector = "#tax_type",
    cgstLabelSelector = "#cgst_label",
    sgstLabelSelector = "#sgst_label",
    igstLabelSelector = "#igst_label",
} = {}) {
    const $table = $(tableSelector);

    // Get split percentages from window or defaults
    const cgstRatio = Number(window.cgst_division_percentage) || 50;
    const sgstRatio = Number(window.sgst_division_percentage) || 50;
    const totalRatio = cgstRatio + sgstRatio || 1;

    // ------------------------------------------------
    // ADD ROW
    // ------------------------------------------------
    $(document).on("click", addButtonSelector, function () {
        const index = $table.find("tbody tr").length;

        const row = `
        <tr>
            <td>
                <select name="items[${index}][product_id]"
                        class="product-dropdown form-select" required>
                    <option value="">Select</option>
                    ${products
                        .map(
                            (p) =>
                                `<option value="${p.id}">${p.our_part_no}</option>`
                        )
                        .join("")}
                </select>
            </td>

            <td>
                <input type="number" name="items[${index}][quantity]"
                       class="form-control qty" min="1" value="1">
            </td>

            <td>
                <input type="number" name="items[${index}][unit_price]"
                       class="form-control rate" step="0.01" min="0">
            </td>

            ${
                enableDiscount
                    ? `
            <td>
                <input type="number"
                       name="items[${index}][discount_percent]"
                       class="form-control discount_percent" step="0.01" min="0">
            </td>`
                    : ``
            }

            <td><input type="text" class="form-control taxable_amount" readonly></td>

            <td>
                <input type="number" name="items[${index}][tax_rate]"
                       class="form-control tax_rate" step="0.01" min="0">
            </td>

            <td><input type="text" class="form-control tax_amount" readonly></td>
            <td><input type="text" class="form-control total_with_tax" readonly></td>

            <td>
                <button type="button"
                        class="btn btn-danger btn-sm removeRow">X</button>
            </td>
        </tr>`;

        const $row = $(row);
        $table.find("tbody").append($row);

        initChoices($row.find(".product-dropdown")[0], true);

        reindexItems($table);
        updateDisabledProducts();
        calculateAll();
    });

    // ------------------------------------------------
    // DISABLE DUPLICATE PRODUCTS (TABLE-SCOPED)
    // ------------------------------------------------
    function updateDisabledProducts() {
        const selected = new Set(
            $table
                .find(".product-dropdown")
                .map(function () {
                    return $(this).val();
                })
                .get()
                .filter(Boolean)
        );

        $table.find(".product-dropdown").each(function () {
            const instance = this.choices;
            if (!instance) return;

            instance.setChoices(
                instance._store.choices.map((choice) => ({
                    value: choice.value,
                    label: choice.label,
                    selected: choice.selected,
                    disabled:
                        choice.value &&
                        selected.has(choice.value) &&
                        !choice.selected,
                })),
                "value",
                "label",
                true
            );
        });
    }

    // ------------------------------------------------
    // PRODUCT CHANGE
    // ------------------------------------------------
    $(document).on("change", `${tableSelector} .product-dropdown`, function () {
        const row = $(this).closest("tr");
        calculateRow(row);
        calculateAll();
        updateDisabledProducts();
    });

    // ------------------------------------------------
    // INPUT CHANGE
    // ------------------------------------------------
    $(document).on(
        "input",
        `${tableSelector} .qty,
         ${tableSelector} .rate,
         ${tableSelector} .discount_percent,
         ${tableSelector} .tax_rate`,
        function () {
            const row = $(this).closest("tr");
            calculateRow(row);
            calculateAll();
        }
    );

    // Watch tax_type change
    $(document).on("change", taxTypeSelector, function() {
        calculateAll();
    });

    // ------------------------------------------------
    // CALCULATE ROW
    // ------------------------------------------------
    function calculateRow(row) {
        const qty = Number(row.find(".qty").val()) || 0;
        const rate = Number(row.find(".rate").val()) || 0;
        const tax = Number(row.find(".tax_rate").val()) || 0;

        const gross = qty * rate;

        let discount = 0;
        if (enableDiscount) {
            const percent = Number(row.find(".discount_percent").val()) || 0;
            discount = (gross * percent) / 100;
        }

        const taxable = Math.max(gross - discount, 0);
        const taxAmt = (taxable * tax) / 100;
        const total = taxable + taxAmt;

        row.find(".taxable_amount").val(taxable.toFixed(2));
        row.find(".tax_amount").val(taxAmt.toFixed(2));
        row.find(".total_with_tax").val(total.toFixed(2));
    }

    // ------------------------------------------------
    // TOTALS (TABLE-SCOPED)
    // ------------------------------------------------
    function calculateAll() {
        let subtotal = 0;
        let taxTotal = 0;
        const taxRates = new Set();

        $table.find(".taxable_amount").each(function () {
            subtotal += Number($(this).val()) || 0;
        });

        $table.find(".tax_rate").each(function () {
            const val = $(this).val();
            if (val) taxRates.add(Number(val));
        });

        $table.find(".tax_amount").each(function () {
            taxTotal += Number($(this).val()) || 0;
        });

        $(subtotalSelector).val(subtotal.toFixed(2));
        $(taxSelector).val(taxTotal.toFixed(2));

        const taxType = $(taxTypeSelector).val();
        const commonTaxRate = taxRates.size === 1 ? [...taxRates][0] : null;

        if (taxType === 'cgst_sgst') {
            const cgst = taxTotal * (cgstRatio / totalRatio);
            const sgst = taxTotal * (sgstRatio / totalRatio);
            $(cgstSelector).val(cgst.toFixed(2));
            $(sgstSelector).val(sgst.toFixed(2));
            $(igstSelector).val("0.00");

            if (commonTaxRate !== null) {
                const c_rate = (commonTaxRate * cgstRatio / totalRatio).toFixed(2);
                const s_rate = (commonTaxRate * sgstRatio / totalRatio).toFixed(2);
                $(cgstLabelSelector).text(`CGST (${parseFloat(c_rate)}%)`);
                $(sgstLabelSelector).text(`SGST (${parseFloat(s_rate)}%)`);
            } else {
                $(cgstLabelSelector).text("CGST");
                $(sgstLabelSelector).text("SGST");
            }
            $(".cgst-sgst-row").show();
            $(".igst-row").hide();
        } else {
            $(igstSelector).val(taxTotal.toFixed(2));
            $(cgstSelector).val("0.00");
            $(sgstSelector).val("0.00");

            if (commonTaxRate !== null) {
                $(igstLabelSelector).text(`IGST (${parseFloat(commonTaxRate.toFixed(2))}% )`);
            } else {
                $(igstLabelSelector).text("IGST");
            }
            $(".igst-row").show();
            $(".cgst-sgst-row").hide();
        }

        $(grandTotalSelector).val((subtotal + taxTotal).toFixed(2));
    }

    function reindexItems($table) {
        $table.find("tbody tr").each(function (rowIndex) {
            $(this)
                .find("input, select, textarea")
                .each(function () {
                    const name = $(this).attr("name");
                    if (!name) return;

                    // Replace items[ANY][field] → items[rowIndex][field]
                    const newName = name.replace(
                        /items\[\d+\]/,
                        `items[${rowIndex}]`
                    );

                    $(this).attr("name", newName);
                });
        });
    }

    // ------------------------------------------------
    // REMOVE ROW (KEEP AT LEAST ONE)
    // ------------------------------------------------
    $(document).on("click", `${tableSelector} .removeRow`, function () {
        if ($table.find("tbody tr").length === 1) return;

        const select = $(this).closest("tr").find(".product-dropdown")[0];

        if (select?.choices) {
            select.choices.destroy();
        }

        $(this).closest("tr").remove();

        reindexItems($table);

        updateDisabledProducts();
        calculateAll();
    });
}
