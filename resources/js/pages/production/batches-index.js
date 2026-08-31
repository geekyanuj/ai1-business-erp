import $ from 'jquery';

$(document).ready(function() {
    const table = $('#batchesTable');
    if (table.length) {
        table.DataTable({
            processing: true,
            serverSide: true,
            ajax: table.data('url'),
            order: [[0, 'desc']],
            columns: [
                { data: 'lot_no', name: 'lot_no' },
                { data: 'product', name: 'product.our_part_no' },
                { data: 'category', name: 'category' },
                { data: 'quantity', name: 'quantity' },
                { data: 'client', name: 'client' },
                { data: 'serials', name: 'serials' },
                { data: 'notes', name: 'notes' },
                { data: 'production_date', name: 'production_date' },
                { data: 'source', name: 'source' },
                { data: 'status', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
            ]
        });
    }
});
