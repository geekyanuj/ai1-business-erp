<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>@yield('title') - AI1 Business ERP</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @stack('styles')
</head>

<body class="app-layout">

  {{-- =========================================================
  SIDEBAR
  ========================================================== --}}
  <aside class="sidebar">

    {{-- Fixed Sidebar Header --}}
    <div class="sidebar-header">

      <img class="logo-wide" src="{{ Vite::asset('resources/images/logo-wide.webp') }}" alt="AI1 Business ERP">

      <hr>
    </div>


    {{-- Scrollable Sidebar Menu --}}
    <div class="sidebar-menu">

      <ul class="nav nav-pills flex-column">

        {{-- Dashboard --}}
        <li class="nav-item">
          <a href="{{ route('dashboard') }}"
            class="nav-link {{ trim($__env->yieldContent('title')) === 'Dashboard' ? 'active' : '' }}">
            <i class="fa-solid fa-house me-2"></i>
            <span>Dashboard</span>
          </a>
        </li>


        


        {{-- Products --}}
        <li class="nav-item">
          <a href="{{ route('products.index') }}"
            class="nav-link {{ in_array(trim($__env->yieldContent('title')), ['Products', 'Product Details']) ? 'active' : '' }}">
            <i class="fa-solid fa-layer-group me-2"></i>
            <span>Products</span>
          </a>
        </li>


        {{-- Clients --}}
        @if (auth()->user()->role == 'Admin')
          <li class="nav-item">
            <a href="{{ route('clients.index') }}"
              class="nav-link {{ trim($__env->yieldContent('title')) === 'Clients' ? 'active' : '' }}">
              <i class="fa-solid fa-people-group me-2"></i>
              <span>Clients</span>
            </a>
          </li>
        @endif


        {{-- Suppliers --}}
        @if (auth()->user()->role == 'Admin')
          <li class="nav-item">
            <a href="{{ route('suppliers.index') }}"
              class="nav-link {{ trim($__env->yieldContent('title')) === 'Suppliers' ? 'active' : '' }}">
              <i class="fa-solid fa-random me-2"></i>
              <span>Suppliers</span>
            </a>
          </li>
        @endif


        


        {{-- Orders --}}
        @php

          $currentTitle = trim($__env->yieldContent('title'));

          $orderTitles = [
            'Purchase Orders',
            'Purchase Order Details',
            'Quotations',
            'Quotation Details',
            'Proforma Invoices',
            'Proforma Invoice Details',
            'Tax Invoices',
            'Tax Invoice Details',
          ];

          $salesTitles = [
            'Quotations',
            'Quotation Details',
            'Proforma Invoices',
            'Proforma Invoice Details',
            'Tax Invoices',
            'Tax Invoice Details',
          ];

          $isOrdersActive = in_array($currentTitle, $orderTitles);
          $isSalesActive = in_array($currentTitle, $salesTitles);

        @endphp

        <li class="nav-item">

          <a href="#ordersMenu"
            class="nav-link d-flex justify-content-between align-items-center {{ $isOrdersActive ? 'active' : '' }}"
            data-bs-toggle="collapse" role="button" aria-expanded="{{ $isOrdersActive ? 'true' : 'false' }}"
            aria-controls="ordersMenu">
            <span>
              <i class="fa-solid fa-list-check me-2"></i>
              Orders
            </span>

            <i class="fas fa-chevron-down transition-icon"></i>
          </a>


          <div id="ordersMenu" class="collapse {{ $isOrdersActive ? 'show' : '' }}">

            <ul class="list-unstyled ps-3 small">

              {{-- Purchase Orders --}}
              <li class="mt-1">
                <a href="{{ route('purchase-orders.index') }}"
                  class="nav-link {{ in_array($currentTitle, ['Purchase Orders', 'Purchase Order Details']) ? 'active' : '' }}">
                  Purchase Orders
                </a>
              </li>


              {{-- Sales Orders --}}
              <li class="mt-1">

                <a href="#salesOrdersMenu"
                  class="nav-link d-flex justify-content-between align-items-center {{ $isSalesActive ? 'active' : '' }}"
                  data-bs-toggle="collapse" role="button" aria-expanded="{{ $isSalesActive ? 'true' : 'false' }}"
                  aria-controls="salesOrdersMenu">
                  <span>Sales Orders</span>

                  <i class="fas fa-chevron-down transition-icon"></i>
                </a>


                <div id="salesOrdersMenu" class="collapse {{ $isSalesActive ? 'show' : '' }}">

                  <ul class="list-unstyled ps-3">

                    <li class="mt-1">
                      <a href="{{ route('quotations.index') }}"
                        class="nav-link {{ in_array($currentTitle, ['Quotations', 'Quotation Details']) ? 'active' : '' }}">
                        Quotation
                      </a>
                    </li>


                    <li class="mt-1">
                      <a href="{{ route('proformas.index') }}"
                        class="nav-link {{ in_array($currentTitle, ['Proforma Invoices', 'Proforma Invoice Details']) ? 'active' : '' }}">
                        Proforma Invoice
                      </a>
                    </li>


                    <li class="mt-1">
                      <a href="{{ route('invoices.index') }}"
                        class="nav-link {{ in_array($currentTitle, ['Tax Invoices', 'Tax Invoice Details']) ? 'active' : '' }}">
                        Tax Invoice
                      </a>
                    </li>

                  </ul>

                </div>

              </li>

            </ul>

          </div>

        </li>


        {{-- Inventory --}}
        @php
          $inventoryTitles = [
            'Inventory',
            'Inventory Details',
            'Inventory Serials',
          ];

          $isInventoryActive = in_array($currentTitle, $inventoryTitles);
        @endphp

        <li class="nav-item">

          <a href="#inventorySubmenu"
            class="nav-link d-flex justify-content-between align-items-center {{ $isInventoryActive ? 'active' : '' }}"
            data-bs-toggle="collapse" role="button" aria-expanded="{{ $isInventoryActive ? 'true' : 'false' }}"
            aria-controls="inventorySubmenu">
            <span>
              <i class="fa-solid fa-boxes-stacked me-2"></i>
              Inventory
            </span>

            <i class="fas fa-chevron-down transition-icon"></i>
          </a>


          <div id="inventorySubmenu" class="collapse {{ $isInventoryActive ? 'show' : '' }}">

            <ul class="list-unstyled ps-3 small">

              <li class="mt-1">
                <a href="{{ route('inventory.index') }}"
                  class="nav-link {{ in_array($currentTitle, ['Inventory', 'Inventory Details']) ? 'active' : '' }}">
                  Inventory
                </a>
              </li>


              <li class="mt-1">
                <a href="{{ route('inventory-serials.index') }}"
                  class="nav-link {{ $currentTitle === 'Inventory Serials' ? 'active' : '' }}">
                  Serial / Lot Tracking
                </a>
              </li>

            </ul>

          </div>

        </li>


        {{-- Production --}}
        @php
          $productionTitles = [
            'Production Lots',
            'Production Lot Details',
            'Create Production Lot',
          ];

          $isProductionActive = in_array($currentTitle, $productionTitles);
        @endphp

        <li class="nav-item">

          <a href="#productionSubmenu"
            class="nav-link d-flex justify-content-between align-items-center {{ $isProductionActive ? 'active' : '' }}"
            data-bs-toggle="collapse" role="button" aria-expanded="{{ $isProductionActive ? 'true' : 'false' }}"
            aria-controls="productionSubmenu">
            <span>
              <i class="fa-solid fa-industry me-2"></i>
              Production
            </span>

            <i class="fas fa-chevron-down transition-icon"></i>
          </a>


          <div id="productionSubmenu" class="collapse {{ $isProductionActive ? 'show' : '' }}">

            <ul class="list-unstyled ps-3 small">

              <li class="mt-1">
                <a href="{{ route('production.batches.index') }}"
                  class="nav-link {{ in_array($currentTitle, ['Production Lots', 'Production Lot Details', 'Create Production Lot']) ? 'active' : '' }}">
                  <i class="fa fa-industry me-1"></i>
                  Production Lots
                </a>
              </li>

            </ul>

          </div>
        </li>


        {{-- Product Client Mapping --}}
        <li class="nav-item">
          <a href="{{ route('product-client-mappings.index') }}"
            class="nav-link {{ $currentTitle === 'Product Client Mapping' ? 'active' : '' }}">
            <i class="fa-solid fa-map me-2"></i>
            <span>Prod ⇄ Client Map</span>
          </a>
        </li>


        


        {{-- Labels--}}
        @php
          $labelTitles = [
            'Label Studio',
            'Serial Traceability',
            'Unit Label Printing',
            'Box Label Printing',
          ];

          $isLabelActive =
            str_ends_with($currentTitle, 'Labels') ||
            in_array($currentTitle, $labelTitles);
        @endphp

        <li class="nav-item">

          <a href="#labelPrintingSubmenu"
            class="nav-link d-flex justify-content-between align-items-center {{ $isLabelActive ? 'active' : '' }}"
            data-bs-toggle="collapse" role="button" aria-expanded="{{ $isLabelActive ? 'true' : 'false' }}"
            aria-controls="labelPrintingSubmenu">
            <span>
              <i class="fa-solid fa-tags me-2"></i>
              Label Printing
            </span>

            <i class="fas fa-chevron-down transition-icon"></i>
          </a>


          <div id="labelPrintingSubmenu" class="collapse {{ $isLabelActive ? 'show' : '' }}">

            <ul class="list-unstyled ps-3 small">

              <li class="mt-1">
                <a href="{{ route('labels.studio') }}"
                  class="nav-link {{ $currentTitle === 'Label Studio' ? 'active' : '' }}">
                  <i class="fa-solid fa-wand-magic-sparkles me-1"></i>
                  Label Studio
                </a>
              </li>


              <li class="mt-1">
                <a href="{{ route('labels.traceability') }}"
                  class="nav-link {{ $currentTitle === 'Serial Traceability' ? 'active' : '' }}">
                  <i class="fa fa-magnifying-glass me-1"></i>
                  Serial Traceability
                </a>
              </li>

            </ul>

          </div>

        </li>


        {{-- Quality Check --}}
        <li class="nav-item">
          <a href="{{ route('qc-check.index') }}"
            class="nav-link {{ $currentTitle === 'Quality Check' ? 'active' : '' }}">
            <i class="fa-solid fa-book me-2"></i>
            <span>Quality Check</span>
          </a>
        </li>


        {{-- Users --}}
        @if (auth()->user()->role == 'Admin')
          <li class="nav-item">
            <a href="{{ route('users.index') }}"
              class="nav-link {{ trim($__env->yieldContent('title')) === 'Users' ? 'active' : '' }}">
              <i class="fa-solid fa-users me-2"></i>
              <span>Users</span>
            </a>
          </li>
        @endif

        {{-- Addresses --}}
        @if (auth()->user()->role == 'Admin')
          <li class="nav-item">
            <a href="{{ route('addresses.index') }}"
              class="nav-link {{ trim($__env->yieldContent('title')) === 'Addresses' ? 'active' : '' }}">
              <i class="fa-solid fa-map-location-dot me-2"></i>
              <span>Addresses</span>
            </a>
          </li>
        @endif

      </ul>

    </div>

  </aside>


  {{-- =========================================================
  RIGHT SIDE
  ========================================================== --}}
  <main class="content">

    {{-- Fixed Header --}}
    <header class="top-navbar">
      @include('header')
    </header>


    {{-- Scrollable Page Content --}}
    <section class="wrapper">
      @yield('content')
    </section>


    {{-- Fixed Footer --}}
    @include('footer')

  </main>


  {{-- =========================================================
  FLASH MESSAGES
  ========================================================== --}}

  @if (session('success'))
    <div id="success-message" class="flash-message flash-success">
      {{ session('success') }}
    </div>
  @endif


  @if (session('error'))
    <div id="error-message" class="flash-message flash-error">
      {{ session('error') }}
    </div>
  @endif


  @if ($errors->any())
    <div id="error-message" class="flash-message flash-error">
      {{ $errors->first() }}
    </div>
  @endif


  {{-- =========================================================
  SCRIPTS
  ========================================================== --}}

  <script>
    document.addEventListener('DOMContentLoaded', function () {

      setTimeout(function () {

        const successMsg = document.getElementById('success-message');
        const errorMsg = document.getElementById('error-message');

        if (successMsg) {
          successMsg.remove();
        }

        if (errorMsg) {
          errorMsg.remove();
        }

      }, 3000);

    });
  </script>


  @stack('modals')
  @stack('scripts')

</body>

</html>