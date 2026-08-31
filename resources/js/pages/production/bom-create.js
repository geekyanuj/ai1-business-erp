import $ from 'jquery';

$(document).ready(function () {
    let rowCount = $('#bom-items tbody tr').length;

    $('#add-row').on('click', function () {
        const inventories = $('#bom-items').data('inventories');
        let options = '<option value="">-- Select Raw Material --</option>';
        
        inventories.forEach(inv => {
            options += `<option value="${inv.material_name}" data-uom="${inv.uom}">${inv.material_name}</option>`;
        });

        const newRow = `
            <tr>
                <td>
                    <select name="items[${rowCount}][material_name]" class="form-select material-select" required>
                        ${options}
                    </select>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" step="0.001" min="0.001" name="items[${rowCount}][quantity_per_unit]" class="form-control" required>
                        <span class="uom-text"></span>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
                </td>
            </tr>
        `;

        $('#bom-items tbody').append(newRow);
        rowCount++;
    });

    $(document).on('change', '.material-select', function () {
        const uom = $(this).find(':selected').data('uom') || '';
        $(this).closest('tr').find('.uom-text').text(uom);
    });

    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
    });

    if ($('#bom-items').data('disabled')) {
        $('input, select, button').not('.btn-outline-secondary').prop('disabled', true);
    }
});
