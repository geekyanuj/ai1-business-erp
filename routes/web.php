<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;

use App\Http\Controllers\{
    DashboardController,
    AuthController,
    UserController,
    ProductController,
    CategoryController,
    ClientController,
    SalesQuotationController,
    SalesProformaController,
    SalesInvoiceController,
    ProductClientMappingController,
    SalesInvoicePaymentController,
    SupplierSerialSearchController,
    InventoryController,
    InventorySerialNumberController,
    ProductionBatchController,
    PurchaseOrderController,
    PurchaseOrderItemController,
    ShipmentController,
    ShipmentItemController,
    ReportController,
    AuditLogController,
    NotificationController,
    ActivityLogController,
    LabelController,
    SupplierController,
    QcController,
    GrnController,
    AddressController,
    GeneralSettingController,
    CompanySettingsController,

};

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
// Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | USER PROFILE
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->group(function () {
        Route::get('/{id}', [UserController::class, 'showProfile'])->name('profile.show');
        Route::put('/{id}/update', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::put('/{id}/change-password', [UserController::class, 'changePassword'])->name('password.change');
    });


    /*
    |--------------------------------------------------------------------------
    | SALES ORDERS (Invoice)
    |--------------------------------------------------------------------------
    */
    Route::get('/sales-order/invoices/data', [SalesInvoiceController::class, 'getInvoicesDataTable'])
        ->name('sales-orders.invoices.data');

    Route::get('/sales-orders/invoices/generate-invoice-number', [SalesInvoiceController::class, 'generateInvoiceNumber'])
        ->name('sales-orders.invoices.generate-invoice-number');



    Route::put('/sales-order/invoices/{invoice}/items', [SalesInvoiceController::class, 'updateItems'])
        ->name('sales-orders.invoices.items.update');

    // Sales Order PDF
    Route::get('/sales-orders/invoices/{id}/print', [SalesInvoiceController::class, 'printInvoice'])
        ->name('sales-orders.invoices.print');

    // Update SO status
    Route::post('/sales-orders/invoices/{id}/status', [SalesInvoiceController::class, 'updateStatus'])
        ->name('sales-orders.invoices.updateStatus');

    Route::post('/sales-orders/invoices/{id}/send-mail', [SalesInvoiceController::class, 'sendMail'])->name('sales-orders.invoices.send-mail');


    Route::post('/invoices/{invoice}/payments', [SalesInvoicePaymentController::class, 'store'])
        ->name('invoices.payments.store');

    Route::resources(['/sales-order/invoices' => SalesInvoiceController::class,]);


    /*
    |--------------------------------------------------------------------------
    | SALES ORDERS (Quotation)
    |--------------------------------------------------------------------------
    */

    Route::get('/sales-order/quotations/data', [SalesQuotationController::class, 'getQuotationsDataTable'])
        ->name('sales-orders.quotations.data');

    Route::get('/sales-orders/quotations/generate-quotation-number', [SalesQuotationController::class, 'generateQuotationNumber'])
        ->name('sales-orders.quotations.generate-quotation-number');

    Route::put('/sales-orders/quotations/{quotation}/items', [SalesQuotationController::class, 'updateItems'])
        ->name('sales-orders.quotations.items.update');

    // Sales Order PDF
    Route::get('/sales-orders/quotations/{id}/print', [SalesQuotationController::class, 'printQuotation'])
        ->name('sales-orders.quotations.print');

    // Update SO status
    Route::post('/sales-orders/quotations/{id}/status', [SalesQuotationController::class, 'updateStatus'])
        ->name('sales-orders.quotations.updateStatus');

    Route::post('/sales-orders/quotations/{quotation}/accept', [SalesQuotationController::class, 'accept'])
        ->name('sales-orders.quotations.accept');

    Route::post('/sales-orders/quotations/{id}/send-mail', [SalesQuotationController::class, 'sendMail'])->name('sales-orders.quotations.send-mail');

    //Quotation → Proforma
    Route::post('/sales-orders/quotations/{quotation}/convert-to-proforma', [SalesProformaController::class, 'storeFromQuotation'])
        ->name('quotations.convertToProforma');

    // Quotation → Invoice (Direct)
    Route::post('/sales-orders/quotations/{quotation}/convert-to-invoice', [SalesInvoiceController::class, 'storeFromQuotation'])
        ->name('quotations.convertToInvoice');

    Route::resources(['/sales-order/quotations' => SalesQuotationController::class]);



    /*
    |--------------------------------------------------------------------------
    | SALES ORDERS (Proforma)
    |--------------------------------------------------------------------------
    */


    Route::get('/sales-order/proforma/data', [SalesProformaController::class, 'getProformasDataTable'])
        ->name('sales-orders.proformas.data');

    Route::get('/sales-orders/proformas/generate-proforma-number', [SalesProformaController::class, 'generateProformaNumber'])
        ->name('sales-orders.proformas.generate-proforma-number');

    Route::put('/sales-orders/proformas/{proforma}/items', [SalesProformaController::class, 'updateItems'])
        ->name('sales-orders.proformas.items.update');

    Route::get('/sales-orders/proformas/{id}/print', [SalesProformaController::class, 'printProforma'])
        ->name('sales-orders.proformas.print');

    Route::post('/sales-orders/proformas/{proforma}/accept', [SalesProformaController::class, 'accept'])
        ->name('sales-orders.proformas.accept');

    Route::post('/sales-orders/proformas/{id}/send-mail', [SalesProformaController::class, 'sendMail'])->name('sales-orders.proformas.send-mail');



    // Proforma → Invoice
    Route::post(
        '/proformas/{proforma}/convert-to-invoice',
        [SalesInvoiceController::class, 'storeFromProforma']
    )
        ->name('proformas.convertToInvoice');

    Route::resources(['/sales-order/proformas' => SalesProformaController::class]);


    /*
    |--------------------------------------------------------------------------
    | PURCHASE ORDERS (Accessible by all authenticated users)
    |--------------------------------------------------------------------------
    */

    Route::get('/purchase-orders/data', [PurchaseOrderController::class, 'getPurchaseOrdersDataTable'])
        ->name('purchase-orders.data');

    Route::get('/purchase-orders/generate-po', [PurchaseOrderController::class, 'generatePONumber'])
        ->name('purchase-orders.generate-po');

    Route::post('purchase-orders/{id}/approve', [PurchaseOrderController::class, 'approve'])
        ->name('purchase-orders.approve');

    Route::post('/purchase-orders/{id}/receive', [PurchaseOrderController::class, 'storeInventoryAndReceive'])
        ->name('purchase-orders.receive');

    Route::get('/purchase-orders/{id}/print', [PurchaseOrderController::class, 'printPO'])
        ->name('purchase-orders.pdf');

    Route::post('/purchase-orders/{id}/send-mail', [PurchaseOrderController::class, 'sendMail'])->name('purchase-orders.send-mail');


    Route::post('/purchase-order-items', [PurchaseOrderItemController::class, 'store'])
        ->name('po.items.store');

    Route::put('/purchase-order-items/{id}', [PurchaseOrderItemController::class, 'update'])
        ->name('po.items.update');

    Route::delete('/purchase-order-items/{id}', [PurchaseOrderItemController::class, 'destroy'])
        ->name('po.items.destroy');

    Route::prefix('purchase-orders')->group(function () {
        Route::prefix('grns')->name('grns.')->group(function () {
            Route::get('/', [GrnController::class, 'index'])->name('index');
            Route::get('/{grn}', [GrnController::class, 'show'])->name('show');
        });
    });

    Route::resource('purchase-orders', PurchaseOrderController::class);


    //Supplier routes
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/data', [SupplierController::class, 'data'])->name('suppliers.data');

    Route::post('/suppliers/ajax-store', [SupplierController::class, 'ajaxStore'])
        ->name('suppliers.ajax-store');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::get('supplier/serial-search', [SupplierSerialSearchController::class, 'index'])
        ->name('supplier.serial.search');



    /*
       |--------------------------------------------------------------------------
       | INVENTORY
       |--------------------------------------------------------------------------
       */

    Route::prefix('inventory')->name('inventory.')->group(function () {

        Route::get(
            '{inventory}/adjust',
            [InventoryController::class, 'adjust']
        )->name('adjust');

        Route::post(
            '{inventory}/adjust',
            [InventoryController::class, 'storeAdjustment']
        )->name('adjust.store');

        Route::get(
            '{inventory}/movements',
            [InventoryController::class, 'movements']
        )->name('movements');
    });

    /*
    |--------------------------------------------------------------------------
    | Production Module
    |--------------------------------------------------------------------------
    */
    Route::prefix('production')->name('production.')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Production Batches
        |--------------------------------------------------------------------------
        */
        Route::get('lots/datatable', [ProductionBatchController::class, 'getDataTable'])
            ->name('batches.datatable');

        Route::resource('lots', ProductionBatchController::class)
            ->names('batches')
            ->parameters(['lots' => 'batch']);

        Route::post('lots/{batch}/start', [ProductionBatchController::class, 'start'])
            ->name('batches.start');

        Route::post('lots/{batch}/complete', [ProductionBatchController::class, 'complete'])
            ->name('batches.complete');

    });

    /*
    |--------------------------------------------------------------------------
    | ORDER REPORTS
    |--------------------------------------------------------------------------
    */
    Route::get('/reports/orders', [ReportController::class, 'orderReport'])
        ->name('reports.order');


    /*
    |--------------------------------------------------------------------------
    | SHARED PRODUCT LIST (non-admin)
    |--------------------------------------------------------------------------
    */
    Route::get('/products/list', [ProductController::class, 'getProductsList'])
        ->name('products.list');

    Route::get('/inventory/datatable', [InventoryController::class, 'getInventoryDataTable'])->name('inventory.datatable');


    Route::get('/product-client-mappings', [ProductClientMappingController::class, 'index'])
        ->name('product-client-mappings.index');

    Route::get('/product-client-mappings-datatable', [ProductClientMappingController::class, 'getMappingDatatable'])
        ->name('product-client-mappings.datatable');

    Route::post('/product-client-mappings', [ProductClientMappingController::class, 'store'])
        ->name('product-client-mappings.store');

    Route::delete('/product-client-mappings/{id}', [ProductClientMappingController::class, 'destroy'])
        ->name('product-client-mappings.destroy');


    Route::get('/labels/unit/create', [LabelController::class, 'unitLabelCreate'])->name('unit-label-create');
    Route::get('/labels/category/{category}/create', [LabelController::class, 'categoryLabelCreate'])->name('labels.category.create');
    Route::post('/labels/category/{category}', [LabelController::class, 'storeCategory'])->name('labels.category.store');
    Route::get('/labels/category/{category}/box', [LabelController::class, 'categoryBoxLabelCreate'])->name('labels.category.box');
    Route::get('/labels/box/create', [LabelController::class, 'boxLabelCreate'])->name('box-label-create');
    Route::get('/labels/traceability', [LabelController::class, 'serialTraceability'])->name('labels.traceability');
    Route::get('/labels/studio', [LabelController::class, 'studio'])->name('labels.studio');
    Route::get('/labels/studio/items', [LabelController::class, 'studioItems'])->name('labels.studio.items');
    Route::post('/labels/studio/print', [LabelController::class, 'printStudio'])->name('labels.studio.print');

    Route::post('/labels/store', [LabelController::class, 'store'])->name('labels.store');

    Route::get('/labels/{id}/print', [LabelController::class, 'printUnitLabels'])->name('labels.print-unit');
    Route::get('/labels/{id}/print-box', [LabelController::class, 'printBoxLabels'])->name('labels.print-box');
    Route::get('/ajax/lots/search', [LabelController::class, 'ajaxLotSearch'])->name('ajax.lots.search');
    Route::get('/labels/box', [LabelController::class, 'boxLabelSearch'])->name('labels.box.search');

    Route::get('/labels/show', [LabelController::class, 'getLabelDatatable'])->name('label.datatable');

    Route::get('/labels/{id}', [LabelController::class, 'show'])->name('labels.show');

    Route::delete('/labels/{id}', [LabelController::class, 'destroy'])->name('labels.destroy');

    Route::get('/product-client-part-no', [ProductClientMappingController::class, 'getClientPartNo'])->name('product-client-part-no');


    Route::get('/qc', fn() => view('qc'));
    Route::post('/qc-upload', [QcController::class, 'upload']);

    Route::get('/qc-check', [QcController::class, 'index'])->name('qc-check.index');


    Route::get('/qc/progress-view/{id}', [QcController::class, 'progressView'])->name('qc.progress.view');

    Route::get('/qc/progress/{id}', [QcController::class, 'progress'])->name('qc.progress');

    Route::get('/qc/download/{batch}', [QcController::class, 'downloadZip'])->name('qc.download');


    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */
    Route::get('/users/data', [UserController::class, 'getUsers'])->name('users.data');
    Route::resource('users', UserController::class);


    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */
    Route::get('/products/data', [ProductController::class, 'getProducts'])->name('products.data');
    Route::post('/products/{id}/upload-file', [ProductController::class, 'uploadFile'])->name('product.uploadFile');
    Route::delete('/products/files/{id}', [ProductController::class, 'deleteFile'])->name('product.deleteFile');
    Route::resource('products', ProductController::class);

    /*
    |--------------------------------------------------------------------------
    | CATEGORIES
    |--------------------------------------------------------------------------
    */
    Route::get('/categories/data', [CategoryController::class, 'getCategories'])
        ->name('categories.data');

    Route::get('/categories/options', [CategoryController::class, 'options'])
        ->name('categories.options');

    Route::get(
        '/categories/{category}/sub-categories',
        [CategoryController::class, 'subCategories']
    )->name('categories.sub-categories');

    Route::post(
        '/categories/{category}/sub-categories',
        [CategoryController::class, 'storeSubCategory']
    )->name('categories.sub-categories.store');

    Route::put(
        '/categories/{category}/sub-categories/{subCategory}',
        [CategoryController::class, 'updateSubCategory']
    )->name('categories.sub-categories.update');

    Route::delete(
        '/categories/{category}/sub-categories/{subCategory}',
        [CategoryController::class, 'destroySubCategory']
    )->name('categories.sub-categories.destroy');

    Route::resource('categories', CategoryController::class);



    /*
    |--------------------------------------------------------------------------
    | CLIENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/clients/data', [ClientController::class, 'getClients'])->name('clients.data');
    Route::get('/clients/list', [ClientController::class, 'getClientsList'])->name('clients.list');
    Route::post('/clients/ajax-store', [ClientController::class, 'ajaxStore'])
        ->name('clients.ajax-store');
    Route::resource('clients', ClientController::class);

    /*
    |--------------------------------------------------------------------------
    | ADDRESSES 
    |--------------------------------------------------------------------------
    */
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/data', [AddressController::class, 'data'])->name('addresses.data');
    Route::get('/addresses/all', [AddressController::class, 'getAllAddresses'])->name('addresses.all');
    Route::get('/addresses/{id}/show-ajax', [AddressController::class, 'showAjax']);
    Route::post('/addresses/store', [AddressController::class, 'storeAjax']);
    Route::post('/addresses/client/{client}', [AddressController::class, 'storeForClient']);
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::get('/clients/{client}/addresses', [AddressController::class, 'getAddressesByClient']);
    Route::get('/suppliers/{supplier}/addresses', [AddressController::class, 'getAddressesBySupplier']);
    Route::get('/company/addresses', [AddressController::class, 'getBranchAddresses']);



    Route::post('/notifications/read', [NotificationController::class, 'getNotification']);


    /*
    |--------------------------------------------------------------------------
    | SYSTEM / ADMIN MODULE RESOURCES
    |--------------------------------------------------------------------------
    */
    Route::resource('product-client-mappings', ProductClientMappingController::class)
        ->except(['index', 'store', 'destroy']);


    Route::resources([
        'inventory' => InventoryController::class,
        'inventory-serials' => InventorySerialNumberController::class,
        'production-batches' => ProductionBatchController::class,
        'shipments' => ShipmentController::class,
        'shipment-items' => ShipmentItemController::class,
        'reports' => ReportController::class,
        'audit-logs' => AuditLogController::class,
        'notifications' => NotificationController::class,
        'activity-log' => ActivityLogController::class,
    ]);

});



/*
|--------------------------------------------------------------------------
| ADMIN ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', AdminAuth::class])->group(function () {
    Route::get('/settings/company', [CompanySettingsController::class, 'index'])
        ->name('company.index');

    Route::post('/settings/company', [CompanySettingsController::class, 'store'])
        ->name('company.store');

    Route::put('/settings/company/{company}', [CompanySettingsController::class, 'update'])
        ->name('company.update');

    Route::get('/settings/general', [GeneralSettingController::class, 'index'])
        ->name('settings.general');

    Route::post('/settings/general', [GeneralSettingController::class, 'store'])
        ->name('settings.general.store');
});
