@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <h5>Dashboard</h5>
    <div class="row">
        <div class="col-md-9">
            <div class="d-flex justify-content-around gap-3">
                <a href="{{ route('products.index') }}" class="card shadow dashboard-card text-decoration-none text-dark"
                    role="button">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <span class="icons-dashboard-container">
                            <i class="fa-solid fa-list-check icons-dashboard"></i>
                        </span>
                        <h6>Total Products</h6>
                        <span class="fs-5">{{ $totalProducts }}</span>
                    </div>
                </a>
                <a href="#" class="card shadow dashboard-card text-decoration-none text-dark" role="button">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <span class="icons-dashboard-container"><i class="fa-solid fa-paperclip icons-dashboard"></i></span>
                        <h6>Total Orders</h6>
                        <span class="fs-5">{{ $totalOrders }}</span>
                    </div>
                </a>
                <a href="{{ route('products.index') }}" class="card shadow dashboard-card text-decoration-none text-dark"
                    role="button">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <span class="icons-dashboard-container"><i class="fa-solid fa-bullhorn icons-dashboard"></i></span>
                        <h6>Total Sales</h6>
                        <span class="fs-5">₹ {{ inr_format($totalSales) }}</span>
                    </div>
                </a>
                <a class="card shadow dashboard-card text-decoration-none text-dark" role="button">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <span class="icons-dashboard-container"><i class="fa-solid fa-users icons-dashboard"></i></span>
                        <h6>Total Users</h6>
                        <span class="fs-5">{{ $totalUsers }}</span>
                    </div>
                </a>
            </div>

            <div class="d-flex mt-5">
                <div class="container-fluid doughnut-container me-3" style="height:300px;">
                    <canvas id="productStatusChart"></canvas>
                </div>
                <div class="container-fluid line-chart-container" style="height:300px;">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow mb-3 recent-activity-card">
    <div class="card-body">
        <h5 class="card-title">Recent Activity</h5>

        <div class="recent-activity-scroll">
            <ul class="list-group list-group-flush">
                @forelse ($recentActivities as $activity)
                    <li class="list-group-item recent-activity-item small-text">
                        {{ \Illuminate\Support\Str::words($activity->description, 5, '…') }}
                    </li>
                @empty
                    <li class="list-group-item text-muted small">
                        No recent activity
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>


            <div class="card shadow">
                <div style="max-height: 180px; overflow-y: auto;">
                    <div class="card-body">

                        <h5 class="card-title">Upcoming Deadlines</h5>
                        <ul class="list-group list-group-flush">
                            @forelse ($upcomingOrders as $order)
                                <li class="list-group-item small-text">
                                    SO {{ $order->so_number }} - {{ $order->order_date }}
                                </li>
                            @empty
                                <li class="list-group-item small-text text-muted">
                                    No upcoming deadlines
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mt-3">
            <div class="container">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">Product Overview</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col">Product Part No</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Added on</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>{{ $product->our_part_no }}</td>
                                            <td>{{ $product->category ?? 'N/A' }}</td>
                                            <td>{{ $product->created_at->format('Y-m-d') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                No products found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.productStatusData = @json($productStatus);
        window.orderStatusData = @json($orderStatus);
    </script>
    @vite('resources/js/pages/dashboard.js')
@endpush    