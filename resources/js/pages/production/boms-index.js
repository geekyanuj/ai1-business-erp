import $ from 'jquery';

$(document).ready(function() {
    const table = $('#bomsTable');
    if (table.length) {
        table.DataTable({
            processing: true,
            serverSide: true,
            ajax: table.data('url'),
            order: [[0, 'desc']],
            columns: [
                { data: 'product', name: 'product.our_part_no' },
                { data: 'version', name: 'version' },
                { data: 'is_active', name: 'is_active' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ]
        });
    }
});
