// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/dashboard.js',
                'resources/js/pages/users.js',
                'resources/js/pages/products-index.js',
                'resources/js/pages/products-show.js',
                'resources/js/pages/clients-index.js',
                'resources/js/pages/addresses-index.js',
                'resources/js/pages/suppliers-index.js',
                'resources/js/pages/purchase-orders-index.js',
                'resources/js/pages/purchase-order/purchase-order-show.js',
                'resources/js/pages/purchase-order/grns/grns-index.js',
                'resources/js/pages/sales-order/quotation/index.js',
                'resources/js/pages/sales-order/quotation/show.js',
                'resources/js/pages/sales-order/proforma/index.js',
                'resources/js/pages/sales-order/proforma/show.js',
                'resources/js/pages/sales-order/invoice/index.js',
                'resources/js/pages/sales-order/invoice/show.js',
                'resources/js/pages/inventory/inventory-index.js',
                'resources/js/pages/production/batches-index.js',
                'resources/js/pages/production/boms-index.js',
                'resources/js/pages/production/bom-create.js',
                'resources/js/pages/product-client-mapping-index.js',
                'resources/js/pages/unit-product-label-create.js',
                'resources/js/pages/label-studio.js',
                'resources/js/pages/addresses-index.js',

                'resources/js/modules/po-items-table.js',
                'resources/js/modules/sales-items-table.js',
            ],
            refresh: true, // This is the key for Blade auto-reload!
        }),
    ],
});