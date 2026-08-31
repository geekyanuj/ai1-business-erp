import $ from 'jquery';
import 'datatables.net-dt';

const selected = new Map();

function selectedFields() {
    return new Set([...document.querySelectorAll('input[name="fields[]"]:checked')].map(input => input.value));
}

function syncHiddenInputs() {
    const form = document.getElementById('studioPrintForm');
    form.querySelectorAll('.selected-item-input').forEach(input => input.remove());
    selected.forEach(item => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'item_ids[]';
        input.value = item.id;
        input.className = 'selected-item-input';
        form.appendChild(input);
    });
}

function updateBoxConfig() {
    const boxConfig = document.getElementById('boxConfig');
    const boxRows = document.getElementById('boxRows');
    const isBox = document.querySelector('input[name="mode"]:checked')?.value === 'box';
    boxConfig.classList.toggle('d-none', !isBox);
    const products = [...new Map([...selected.values()].map(item => [item.productId, item.partNo])).entries()];
    boxRows.innerHTML = products.length
        ? products.map(([id, partNo]) => `<div class="box-row-config"><span title="${partNo}">${partNo}</span><input class="form-control form-control-sm box-units" name="units_per_box[${id}]" type="number" min="1" placeholder="Units" required ${isBox ? '' : 'disabled'}></div>`).join('')
        : 'Select labels to configure boxes by product.';
}

function renderPreview() {
    const grid = document.getElementById('previewGrid');
    const fields = selectedFields();
    const isBox = document.querySelector('input[name="mode"]:checked')?.value === 'box';
    const pageSize = document.querySelector('input[name="page_size"]:checked')?.value || 'A3';
    const columns = Number(document.getElementById('studioColumns').value || 2);
    const rows = Number(document.getElementById('studioRows').value || 5);
    document.getElementById('previewSize').textContent = `${pageSize} · ${columns} × ${rows}`;
    const items = [...selected.values()];
    if (!items.length) {
        grid.innerHTML = '<div class="preview-empty"><i class="fa-solid fa-eye-slash"></i><span>Select labels to preview the sheet.</span></div>';
        return;
    }
    let cards = items;
    if (isBox) {
        cards = [];
        [...new Map(items.map(item => [item.productId, item])).keys()].forEach(productId => {
            const matching = items.filter(item => item.productId === productId);
            const units = Number(document.querySelector(`input[name="units_per_box[${productId}]"]`)?.value || matching.length);
            for (let start = 0; start < matching.length; start += Math.max(1, units)) {
                cards.push({ ...matching[start], boxItems: matching.slice(start, start + Math.max(1, units)) });
            }
        });
    }
    grid.innerHTML = cards.map((item, index) => {
            const matching = item.boxItems || [item];
        const units = Number(document.querySelector(`input[name="units_per_box[${item.productId}]"]`)?.value || matching.length);
        const quantity = isBox ? Math.min(units, matching.length) : 1;
            const values = [];
        if (fields.has('client_name') && item.client) values.push(`<div class="preview-line"><strong>Client:</strong> ${item.client}</div>`);
        if (fields.has('client_part_no') && item.clientPart) values.push(`<div class="preview-line"><strong>Client part:</strong> ${item.clientPart}</div>`);
        if (fields.has('lot_no')) values.push(`<div class="preview-line"><strong>Lot:</strong> ${item.lot}</div>`);
        if (fields.has('quantity')) values.push(`<div class="preview-line"><strong>Qty:</strong> ${quantity} PCS</div>`);
        if (fields.has('item_code')) values.push(`<div class="preview-code">${isBox ? matching.map(candidate => candidate.code).join(', ') : item.code}</div>`);
        if (fields.has('description')) values.push(`<div class="preview-line">${item.description || ''}</div>`);
        const artwork = fields.has('artwork') ? '<img class="logo-preview" src="/images/client-logo.jpg" alt="Client Logo"><div class="preview-art"><img src="/images/make-in-india.jpg" alt="Make in India"><img src="/images/msl1.jpg" alt="MSL"><img src="/images/rohs.jpg" alt="RoHS"><img src="/images/reach.jpg" alt="REACH"></div>' : '';
        return `<article class="label-preview">${artwork}${fields.has('part_no') ? `<div class="preview-part">${item.partNo}</div>` : ''}${isBox ? `<div class="preview-line"><strong>Box:</strong> ${index + 1}</div>` : ''}${values.join('')}</article>`;
    }).join('');
}

function refreshState() {
    document.getElementById('selectedCount').textContent = selected.size;
    document.getElementById('printSelected').disabled = selected.size === 0;
    document.getElementById('capacity').textContent = Number(document.getElementById('studioColumns').value || 1) * Number(document.getElementById('studioRows').value || 1);
    syncHiddenInputs();
    updateBoxConfig();
    renderPreview();
}

function initializeStudioTable() {
    const table = $('#studioLabelsTable');
    if (!table.length) return;
    const dataUrl = table.data('url');
    const instance = table.DataTable({
        processing: true,
        serverSide: true,
        ajax: dataUrl,
        pageLength: 10,
        responsive: true,
        columns: [
            {
                data: 'select_data',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: (_, type, row) => `<input class="form-check-input studio-label-check" type="checkbox" data-label="${encodeURIComponent(JSON.stringify(row.select_data))}" ${selected.has(row.select_data.id) ? 'checked' : ''}>`,
            },
            { data: 'part_no', orderable: false, searchable: false, className: 'fw-semibold' },
            { data: 'category', orderable: false, searchable: false, render: data => `<span class="category-chip">${data}</span>` },
            { data: 'lot_no', orderable: false, searchable: false },
            { data: 'client_name', orderable: false, searchable: false, defaultContent: '-' },
            { data: 'item_code', name: 'item_code', render: data => `<code>${data}</code>` },
        ],
        drawCallback: () => {
            document.querySelectorAll('.studio-label-check').forEach(check => check.addEventListener('change', () => {
                const item = JSON.parse(decodeURIComponent(check.dataset.label));
                if (check.checked) selected.set(item.id, item);
                else selected.delete(item.id);
                refreshState();
            }));
        },
    });

    document.getElementById('toggleAll').addEventListener('click', event => {
        const checks = [...document.querySelectorAll('.studio-label-check')];
        const selectAll = checks.some(check => !check.checked);
        checks.forEach(check => {
            const item = JSON.parse(decodeURIComponent(check.dataset.label));
            check.checked = selectAll;
            if (selectAll) selected.set(item.id, item);
            else selected.delete(item.id);
        });
        event.currentTarget.innerHTML = selectAll ? '<i class="fa-solid fa-xmark me-1"></i> Clear visible' : '<i class="fa-solid fa-check-double me-1"></i> Select visible';
        refreshState();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initializeStudioTable();
    document.querySelectorAll('input[name="mode"], input[name="page_size"], input[name="fields[]"]').forEach(input => input.addEventListener('change', refreshState));
    document.getElementById('boxRows').addEventListener('input', renderPreview);
    document.getElementById('studioColumns').addEventListener('input', refreshState);
    document.getElementById('studioRows').addEventListener('input', refreshState);
    refreshState();
});
