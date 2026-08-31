import $ from "jquery";
import "datatables.net-dt";
import "datatables.net-buttons-dt";
import "datatables.net-buttons/js/buttons.html5.js";
import JSZip from "jszip";
import Choices from "choices.js";

window.JSZip = JSZip;

/* =====================================================
                        DATATABLE
===================================================== */
export function initializeLabelTable(tableSelector, ajaxUrl) {
    const $table = $(tableSelector);
    if (!$table.length) return null;

    const table = $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,
        columns: [
            { data: "lot_no", name: "lot_no", className: "text-center"},
            { data: "client_name", name: "client_name", className: "text-center"},
            { data: "category", name: "category", className: "text-center"},
            { data: "notes", name: "notes", className: "text-center", orderable: false, searchable:false},
            { data: "actions", orderable: false, searchable: false, className: "text-center"},
        ],
        responsive: true,
        pageLength: 10,
    });

    return table;
}

/* =====================================================
                        CHOICES.JS
===================================================== */
function initChoices(element, isModal = false) {
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

/* =====================================================
                        LABEL LOGIC
===================================================== */
function updateFinalItemCode(row) {
    const input = row.querySelector(".item-input");
    const final = row.querySelector(".item-final");
    if (final) {
        final.value = input.value;
    }
}

function updateLabelNumbers() {
    document.querySelectorAll(".label-row").forEach((row, index) => {
        const labelNumber = row.querySelector(".label-number");
        if (labelNumber) {
            labelNumber.textContent = `#${index + 1}`;
        }
    });
}

function bindLabelRowEvents(row) {
    row.querySelector(".product-select").addEventListener("change", () =>
        updateClientPartNo(row),
    );

    row.querySelector(".quantity-input")?.addEventListener("input", () => {
        updateTotalQuantity();
    });

    document
        .getElementById("clientSelect")
        .addEventListener("change", () => updateClientPartNo(row));
}

function filterProductSelect(select) {
    const category = document.getElementById("categorySelect").value;
    const selectedValue = select.value;
    const options = [...select.querySelectorAll("option")];

    if (select.choices) {
        select.choices.destroy();
    }

    select.innerHTML = options
        .filter((option) => !option.value || option.dataset.category === category)
        .map((option) => option.outerHTML)
        .join("");
    select.value = options.some((option) => option.value === selectedValue && option.dataset.category === category)
        ? selectedValue
        : "";
    select.disabled = !category;
    initChoices(select, true);
}

function filterAllProductSelects() {
    document.querySelectorAll(".product-select").forEach(filterProductSelect);
}

function updateClientPartNo(row) {
    const productId = row.querySelector(".product-select").value;
    const clientId = document.getElementById("clientSelect").value;
    const input = row.querySelector(".client-part-no");

    if (!productId || !clientId) {
        input.value = "";
        return;
    }

    fetch(
        `/product-client-part-no?product_id=${productId}&client_id=${clientId}`,
    )
        .then((res) => res.json())
        .then((data) => {
            input.value = data.client_part_no ?? "-";
        });
}

function updateTotalQuantity() {
    let total = 0;

    document.querySelectorAll(".quantity-input").forEach((input) => {
        const val = parseInt(input.value, 10);
        if (!isNaN(val)) {
            total += val;
        }
    });

    document.getElementById("totalQuantity").value = total;
}

/* =====================================================
                    ADD / REMOVE ROW
===================================================== */
window.addLabelRow = function () {
    const container = document.getElementById("labels-container");

    const row = document.createElement("div");
    row.className = "label-row border rounded p-2 mb-2";

    row.innerHTML = `
<div class="row g-2 align-items-end ">

    <div class="col-md-4">
        <label class="small">Part No</label>
        <select class="form-select product-select"
            name="products[]" required>
            <option value="">Select Product</option>
            ${window.productsOptionsHtml}
        </select>
    </div>

    <div class="col-md-3">
        <label class="small">Client Part No</label>
        <input type="text" class="form-control client-part-no input-disabled-look" readonly>
    </div>

    <div class="col-md-1">
        <label class="small">Quantity</label>
        <input type="number" name="quantities[]" class="form-control quantity-input" min="1" required>
    </div>

    <div class="col-md-3">
        <label class="small">Serial Prefix (Item Code)</label>
        <input type="text" name="prefixes[]" class="form-control" placeholder="">
    </div>

    <div class="col-md-1 text-end">
        <div class="mb-1 fw-bold text-primary label-number">#</div>
        <button type="button"
            class="btn btn-danger btn-sm"
            onclick="removeLabelRow(this)">✕</button>
    </div>

</div>
`;

    container.appendChild(row);

    bindLabelRowEvents(row);
    filterProductSelect(row.querySelector(".product-select"));
    updateLabelNumbers();
    updateTotalQuantity();
};

window.removeLabelRow = function (btn) {
    const row = btn.closest(".label-row");

    if (document.querySelectorAll(".label-row").length === 1) {
        alert("At least one label is required");
        return;
    }

    row.remove();
    updateLabelNumbers();
    updateTotalQuantity(); 
};

/* =====================================================
                        INIT ON LOAD
===================================================== */
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".label-row").forEach((row) => {
        bindLabelRowEvents(row);
        initChoices(row.querySelector(".product-select"), true);
        initChoices(document.getElementById("clientSelect"), true);
    });

    document.getElementById("categorySelect")?.addEventListener("change", filterAllProductSelects);
    filterAllProductSelects();

    updateLabelNumbers();
    updateTotalQuantity();

    const tableSelector = "#LabelTable";
    const ajaxUrl = $(tableSelector).data("url");
    initializeLabelTable(tableSelector, ajaxUrl);
});
