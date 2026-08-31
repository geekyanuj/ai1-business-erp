<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title') - Inventory ERP | TE TechSolution</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar d-flex flex-column p-3">
    {{-- <img class="logo mb-3" src="{{ Vite::asset('resources/images/logo.png') }}" alt="Logo">--}}
    <img class="logo mb-3" src="/images/logo.png" alt="Logo">
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">

      <!-- Dashboard -->
      <li class="nav-item">
        <a href="{{ route('dashboard') }}"
          class="nav-link {{ trim($__env->yieldContent('title')) === 'Dashboard' ? 'active' : '' }}">
          <i class="fa-solid fa-house me-2"></i> Dashboard
        </a>
      </li>

      <!-- Users (Admin only) -->
      @if (auth()->user()->role == 'Admin')
        <li class="nav-item">
          <a href="{{ route('users.index') }}"
            class="nav-link {{ trim($__env->yieldContent('title')) === 'Users' ? 'active' : '' }}">
            <i class="fa-solid fa-users me-2"></i> Users
          </a>
        </li>
      @endif

      <!-- Products -->
      <li class="nav-item">
        <a href="{{ route('products.index') }}"
          class="nav-link {{ in_array(trim($__env->yieldContent('title')), ['Products', 'Product Details']) ? 'active' : '' }}">
          <i class="fa-solid fa-layer-group me-2"></i> Products
        </a>
      </li>

      <!-- Clients (Admin only) -->
      @if (auth()->user()->role == 'Admin')
        <li class="nav-item">
          <a href="{{ route('clients.index') }}"
            class="nav-link {{ trim($__env->yieldContent('title')) === 'Clients' ? 'active' : '' }}">
            <i class="fa-solid fa-people-group me-2"></i> Clients
          </a>
        </li>
      @endif

      <!-- Clients (Admin only) -->
      @if (auth()->user()->role == 'Admin')
        <li class="nav-item">
          <a href="{{ route('suppliers.index') }}"
            class="nav-link {{ trim($__env->yieldContent('title')) === 'Suppliers' ? 'active' : '' }}">
            <i class="fa-solid fa-random me-2"></i> Suppliers
          </a>
        </li>
      @endif

      <!-- Addresses (Admin only) -->
      @if (auth()->user()->role == 'Admin')
        <li class="nav-item">
          <a href="{{ route('addresses.index') }}"
            class="nav-link {{ trim($__env->yieldContent('title')) === 'Addresses' ? 'active' : '' }}">
            <i class="fa-solid fa-map-location-dot me-2"></i> Addresses
          </a>
        </li>
      @endif

      <!-- Orders -->
      <li class="nav-item">

        @php
          $orderTitles = [
            // Purchase
            'Purchase Orders',
            'Purchase Order Details',

            // Sales
            'Quotations',
            'Quotation Details',
            'Proforma Invoices',
            'Proforma Invoice Details',
            'Tax Invoices',
            'Tax Invoice Details',
          ];

          $isOrdersActive = in_array(trim($__env->yieldContent('title')), $orderTitles);

          $salesTitles = [
            'Quotations',
            'Quotation Details',
            'Proforma Invoices',
            'Proforma Invoice Details',
            'Tax Invoices',
            'Tax Invoice Details',
          ];

          $isSalesActive = in_array(trim($__env->yieldContent('title')), $salesTitles);
        @endphp

        <!-- Orders Parent -->
        <a class="nav-link d-flex justify-content-between align-items-center {{ $isOrdersActive ? 'active' : '' }}"
          data-bs-toggle="collapse" href="#ordersMenu" role="button"
          aria-expanded="{{ $isOrdersActive ? 'true' : 'false' }}">
          <span>
            <i class="fa-solid fa-list-check me-2"></i> Orders
          </span>
          <i class="fas fa-chevron-down transition-icon"></i>
        </a>

        <div class="collapse {{ $isOrdersActive ? 'show' : '' }}" id="ordersMenu">
          <ul class="list-unstyled ps-3 small">

            <!-- Purchase Orders -->
            <li class="mt-1">
              <a href="{{ route('purchase-orders.index') }}"
                class="nav-link {{ in_array(trim($__env->yieldContent('title')), ['Purchase Orders', 'Purchase Order Details']) ? 'active' : '' }}">
                Purchase Orders
              </a>
            </li>

            <!-- Sales Orders Parent -->
            <li class="mt-1">

              <a class="nav-link d-flex justify-content-between align-items-center {{ $isSalesActive ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#salesOrdersMenu" role="button"
                aria-expanded="{{ $isSalesActive ? 'true' : 'false' }}">
                <span>Sales Orders</span>
                <i class="fas fa-chevron-down transition-icon"></i>
              </a>

              <div class="collapse {{ $isSalesActive ? 'show' : '' }}" id="salesOrdersMenu">
                <ul class="list-unstyled ps-3">

                  <li class="mt-1">
                    <a href="{{ route('quotations.index') }}"
                      class="nav-link {{ in_array(trim($__env->yieldContent('title')), ['Quotations', 'Quotation Details']) ? 'active' : '' }}">
                      Quotation
                    </a>
                  </li>

                  <li class="mt-1">
                    <a href="{{ route('proformas.index') }}"
                      class="nav-link {{ in_array(trim($__env->yieldContent('title')), ['Proforma Invoices', 'Proforma Invoice Details']) ? 'active' : '' }}">
                      Proforma Invoice
                    </a>
                  </li>

                  <li class="mt-1">
                    <a href="{{ route('invoices.index') }}"
                      class="nav-link {{ in_array(trim($__env->yieldContent('title')), ['Tax Invoices', 'Tax Invoice Details']) ? 'active' : '' }}">
                      Tax Invoice
                    </a>
                  </li>

                </ul>
              </div>

            </li>

          </ul>
        </div>
      </li>




      <!-- Inventory with Submenu -->
      <li class="nav-item">
        <a class="nav-link d-flex justify-content-between align-items-center
        {{ in_array(trim($__env->yieldContent('title')), ['Inventory', 'Inventory Details', 'Inventory Serials']) ? 'active' : '' }}"
          data-bs-toggle="collapse" href="#inventorySubmenu" role="button"
          aria-expanded="{{ in_array(trim($__env->yieldContent('title')), ['Inventory', 'Inventory Details', 'Inventory Serials']) ? 'true' : 'false' }}"
          aria-controls="inventorySubmenu">

          <span>
            <i class="fa-solid fa-boxes-stacked me-2"></i> Inventory
          </span>

          <i class="fas fa-chevron-down transition-icon"></i>
        </a>

        <div
          class="collapse {{ in_array(trim($__env->yieldContent('title')), ['Inventory', 'Inventory Details', 'Inventory Serials']) ? 'show' : '' }}"
          id="inventorySubmenu">

          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-3">

            <!-- Inventory List -->
            <li class="mt-1">
              <a href="{{ route('inventory.index') }}"
                class="nav-link {{ in_array(trim($__env->yieldContent('title')), ['Inventory', 'Inventory Details']) ? 'active' : '' }}">
                Inventory
              </a>
            </li>

            <!-- Serial / Lot Tracking -->
            <li class="mt-1">
              <a href="{{ route('inventory-serials.index') }}"
                class="nav-link {{ trim($__env->yieldContent('title')) === 'Inventory Serials' ? 'active' : '' }}">
                Serial / Lot Tracking
              </a>
            </li>

          </ul>
        </div>
      </li>


      <!-- Productions -->
      @php
        $productionTitles = [
          'Production Lots',
          'Production Lot Details',
          'Create Production Lot',
        ];
        $isProductionActive = in_array(trim($__env->yieldContent('title')), $productionTitles);
      @endphp

      <!-- Production Module -->
      <li class="nav-item">
        <a class="nav-link d-flex justify-content-between align-items-center
        {{ $isProductionActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#productionSubmenu" role="button"
          aria-expanded="{{ $isProductionActive ? 'true' : 'false' }}" aria-controls="productionSubmenu">

          <span>
            <i class="fa-solid fa-industry me-2"></i> Production
          </span>

          <i class="fas fa-chevron-down transition-icon"></i>
        </a>

        <div class="collapse {{ $isProductionActive ? 'show' : '' }}" id="productionSubmenu">
          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-3">

            <!-- Production Lots -->
            <li class="mt-1">
              <a href="{{ route('production.batches.index') }}"
                class="nav-link {{ in_array(trim($__env->yieldContent('title')), ['Production Lots', 'Production Lot Details', 'Create Production Lot']) ? 'active' : '' }}">
                <i class="fa fa-industry me-1"></i> Production Lots
              </a>
            </li>


          </ul>
        </div>
      </li>


      <!-- Productions -->
      <li class="nav-item">
        <a href={{ route('product-client-mappings.index') }}
          class="nav-link {{ trim($__env->yieldContent('title')) === 'Product Client Mapping' ? 'active' : '' }}">
          <i class="fa-solid fa-map me-2"></i> Prod ⇄ Client Map
        </a>
      </li>

      <!-- Order Reports -->
      <li class="nav-item">
        <a href="{{ route('reports.order') }}"
          class="nav-link {{ trim($__env->yieldContent('title')) === 'Order Reports' ? 'active' : '' }}">
          <i class="fa-solid fa-book me-2"></i> Order Reports
        </a>
      </li>

      <!-- Label Printing with Submenu -->
      <li class="nav-item">
        <a class="nav-link d-flex justify-content-between align-items-center
    {{ str_ends_with(trim($__env->yieldContent('title')), 'Labels') || in_array(trim($__env->yieldContent('title')), ['Label Studio', 'Unit Label Printing', 'Box Label Printing']) ? 'active' : '' }}"
          data-bs-toggle="collapse" href="#labelPrintingSubmenu" role="button"
          aria-expanded="{{ str_ends_with(trim($__env->yieldContent('title')), 'Labels') || in_array(trim($__env->yieldContent('title')), ['Label Studio', 'Unit Label Printing', 'Box Label Printing']) ? 'true' : 'false' }}"
          aria-controls="labelPrintingSubmenu">
          <span>
            <i class="fa-solid fa-tags me-2"></i> Label Printing
          </span>

          <i class="fas fa-chevron-down transition-icon"></i>
        </a>

        <div
          class="collapse {{ str_ends_with(trim($__env->yieldContent('title')), 'Labels') || in_array(trim($__env->yieldContent('title')), ['Label Studio', 'Unit Label Printing', 'Box Label Printing']) ? 'show' : '' }}"
          id="labelPrintingSubmenu">
          <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-3">

            <!-- Mixed-category label workspace -->
            <li class="mt-1">
              <a href="{{ route('labels.studio') }}"
                class="nav-link {{ trim($__env->yieldContent('title')) === 'Label Studio' ? 'active' : '' }}">
                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Label Studio
              </a>
            </li>

            <li class="mt-1">
              <a href="{{ route('labels.traceability') }}"
                class="nav-link {{ trim($__env->yieldContent('title')) === 'Serial Traceability' ? 'active' : '' }}">
                <i class="fa fa-magnifying-glass me-1"></i> Serial Traceability
              </a>
            </li>

          </ul>
        </div>
      </li>


      <!-- QC Check -->
      <li class="nav-item">
        <a href="{{ route('qc-check.index') }}"
          class="nav-link {{ trim($__env->yieldContent('title')) === 'Quality Check' ? 'active' : '' }}">
          <i class="fa-solid fa-book me-2"></i> Quality Check
        </a>
      </li>


    </ul>
  </div>


  <!-- Page Content -->
  <div class="content">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light shadow">
      <div class="container-fluid">
        <span>Hi, {{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
        @include('header')
      </div>
    </nav>
    <!-- Main Body -->
    <div class="wrapper">
      @yield('content')
    </div>
    @include('footer')

    @if (session('success'))
      <div id="success-message" style="
                                          position: fixed;
                                          top: 60px;
                                          right: 20px;
                                          background-color: #d4edda;
                                          color: #155724;
                                          padding: 15px 20px;
                                          border-left: 5px solid #28a745;
                                          border-radius: 5px;
                                          box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                          z-index: 9999;
                                          width:300px;
                                      ">
        {{ session('success') }}
      </div>
    @endif

    @if (session('error'))
      <div id="error-message" style="
                                          position: fixed;
                                          top: 60px;
                                          right: 20px;
                                          width:300px;
                                          background-color: #f8d7da;
                                          color: #721c24;
                                          padding: 15px 20px;
                                          border-left: 5px solid #dc3545;
                                          border-radius: 5px;
                                          box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                          z-index: 9999;
                                      ">
        {{ session('error') }}
      </div>
    @endif

    @if ($errors->any())
      <div id="error-message" style="
                                          position: fixed;
                                          top: 60px;
                                          right: 20px;
                                          width:300px;
                                          background-color: #f8d7da;
                                          color: #721c24;
                                          padding: 15px 20px;
                                          border-left: 5px solid #dc3545;
                                          border-radius: 5px;
                                          box-shadow: 0 0 10px rgba(0,0,0,0.1);
                                          z-index: 9999;
                                      ">
        {{ $errors->first() }}
      </div>
    @endif
  </div>


  <script>
    // Auto-hide the messages after 3 seconds
    setTimeout(function () {
      const successMsg = document.getElementById('success-message');
      const errorMsg = document.getElementById('error-message');
      if (successMsg) successMsg.style.display = 'none';
      if (errorMsg) errorMsg.style.display = 'none';
    }, 3000); // 3000ms = 3 seconds
  </script>

  @stack('modals')
  @stack('scripts')
</body>

</html>