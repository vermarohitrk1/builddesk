@extends('layouts.main')

@section('content')

<!-- Page Header (Welcome + Date + Quick Actions) -->
<x-ui.page-header title="Dashboard" :breadcrumbs="['Home' => null]">
    <div class="d-flex align-items-center gap-2">
        <span class="text-muted small me-2"><i class="far fa-calendar-alt"></i> {{ date('F j, Y') }}</span>
        <button class="btn btn-sm btn-primary"><i class="fas fa-plus"></i> Add Lead</button>
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-invoice-dollar"></i> Add Expense</button>
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-user-plus"></i> Add Customer</button>
    </div>
</x-ui.page-header>

<!-- Summary Cards Grid -->
<div class="row g-3 mb-4">
    <!-- Active Leads -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card h-100 border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0 small">Active Leads</h6>
                    <i class="fas fa-user-tie text-primary opacity-50"></i>
                </div>
                <h3 class="fw-bold mb-1">124</h3>
                <span class="badge bg-success bg-opacity-10 text-success small">+12% this month</span>
            </div>
        </div>
    </div>
    <!-- Running Projects -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card h-100 border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0 small">Running Projects</h6>
                    <i class="fas fa-project-diagram text-info opacity-50"></i>
                </div>
                <h3 class="fw-bold mb-1">18</h3>
                <span class="badge bg-success bg-opacity-10 text-success small">+2 new</span>
            </div>
        </div>
    </div>
    <!-- Monthly Revenue -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card h-100 border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0 small">Revenue (MTD)</h6>
                    <i class="fas fa-rupee-sign text-success opacity-50"></i>
                </div>
                <h3 class="fw-bold mb-1">₹4.2M</h3>
                <span class="badge bg-success bg-opacity-10 text-success small">+8% vs last month</span>
            </div>
        </div>
    </div>
    <!-- Monthly Expenses -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card h-100 border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0 small">Expenses (MTD)</h6>
                    <i class="fas fa-wallet text-danger opacity-50"></i>
                </div>
                <h3 class="fw-bold mb-1">₹850k</h3>
                <span class="badge bg-danger bg-opacity-10 text-danger small">+5% vs last month</span>
            </div>
        </div>
    </div>
    <!-- Pending Follow-ups -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card h-100 border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0 small">Follow-ups</h6>
                    <i class="fas fa-phone-alt text-warning opacity-50"></i>
                </div>
                <h3 class="fw-bold mb-1">29</h3>
                <span class="text-muted small">Due today</span>
            </div>
        </div>
    </div>
    <!-- Employees -->
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card h-100 border-0">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title text-muted mb-0 small">Employees</h6>
                    <i class="fas fa-users text-secondary opacity-50"></i>
                </div>
                <h3 class="fw-bold mb-1">42</h3>
                <span class="badge bg-secondary bg-opacity-10 text-secondary small">3 on leave</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Access -->
<div class="row g-3 mb-4">
    <div class="col-12">
        <h6 class="mb-3 text-muted fw-semibold">Quick Access</h6>
        <div class="d-flex gap-3 flex-wrap">
            <a href="#" class="btn btn-light bg-white border border-light-subtle shadow-sm px-4 py-2 d-flex flex-column align-items-center text-decoration-none">
                <i class="fas fa-users text-primary mb-2 fs-5"></i>
                <span class="small fw-medium text-dark">Customers</span>
            </a>
            <a href="#" class="btn btn-light bg-white border border-light-subtle shadow-sm px-4 py-2 d-flex flex-column align-items-center text-decoration-none">
                <i class="fas fa-user-tie text-info mb-2 fs-5"></i>
                <span class="small fw-medium text-dark">Leads</span>
            </a>
            <a href="#" class="btn btn-light bg-white border border-light-subtle shadow-sm px-4 py-2 d-flex flex-column align-items-center text-decoration-none">
                <i class="fas fa-project-diagram text-success mb-2 fs-5"></i>
                <span class="small fw-medium text-dark">Projects</span>
            </a>
            <a href="#" class="btn btn-light bg-white border border-light-subtle shadow-sm px-4 py-2 d-flex flex-column align-items-center text-decoration-none">
                <i class="fas fa-wallet text-danger mb-2 fs-5"></i>
                <span class="small fw-medium text-dark">Expenses</span>
            </a>
            <a href="#" class="btn btn-light bg-white border border-light-subtle shadow-sm px-4 py-2 d-flex flex-column align-items-center text-decoration-none">
                <i class="fas fa-id-card text-warning mb-2 fs-5"></i>
                <span class="small fw-medium text-dark">Employees</span>
            </a>
            <a href="#" class="btn btn-light bg-white border border-light-subtle shadow-sm px-4 py-2 d-flex flex-column align-items-center text-decoration-none">
                <i class="fas fa-chart-pie text-secondary mb-2 fs-5"></i>
                <span class="small fw-medium text-dark">Reports</span>
            </a>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-3 mb-4">
    <!-- <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header border-0 bg-transparent pt-3 pb-0">
                <h6 class="fw-bold mb-0">Revenue vs Expenses (Monthly)</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueExpenseChart" height="250"></canvas>
            </div>
        </div>
    </div> -->
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                <h6 class="fw-bold mb-0">Recent Leads</h6>
                <a href="#" class="btn btn-sm btn-light border-0">View All</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>Client Name</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Source</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Acme Corp</td>
                                <td>9876543210</td>
                                <td>#453 Stree 2, Auckland, New Zealand</td>
                                <td><span class="badge badge-status-pending">Website</span></td>
                                <td><a href="#" class="btn btn-sm btn-light border-0">View</a></td>
                            </tr>
                            <tr>
                                <td>Acme Corp</td>
                                <td>9876543210</td>
                                <td>#453 Stree 2, Auckland, New Zealand</td>
                                <td><span class="badge badge-status-pending">Website</span></td>
                                <td><a href="#" class="btn btn-sm btn-light border-0">View</a></td>
                            </tr>
                            <tr>
                                <td>Acme Corp</td>
                                <td>9876543210</td>
                                <td>#453 Stree 2, Auckland, New Zealand</td>
                                <td><span class="badge badge-status-pending">Website</span></td>
                                <td><a href="#" class="btn btn-sm btn-light border-0">View</a></td>
                            </tr>
                            <tr>
                                <td>Acme Corp</td>
                                <td>9876543210</td>
                                <td>#453 Stree 2, Auckland, New Zealand</td>
                                <td><span class="badge badge-status-pending">Website</span></td>
                                <td><a href="#" class="btn btn-sm btn-light border-0">View</a></td>
                            </tr>
                            <tr>
                                <td>Acme Corp</td>
                                <td>9876543210</td>
                                <td>#453 Stree 2, Auckland, New Zealand</td>
                                <td><span class="badge badge-status-pending">Website</span></td>
                                <td><a href="#" class="btn btn-sm btn-light border-0">View</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header border-0 bg-transparent pt-3 pb-0">
                <h6 class="fw-bold mb-0">Leads Created by Month</h6>
            </div>
            <div class="card-body">
                <canvas id="leadsChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities & Tables -->
<div class="row g-3">
    <!-- Right side items (Activities/Follow-ups) -->
    <div class="col-xl-4 order-xl-2">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                <h6 class="fw-bold mb-0">Upcoming Follow-ups</h6>
            </div>
            <div class="list-group list-group-flush">
                <div class="list-group-item py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 fw-semibold" style="font-size: 0.9rem;">Modern Villa Design</h6>
                            <p class="mb-0 small text-muted">Alice Smith - Finalize materials.</p>
                        </div>
                        <span class="badge bg-warning text-dark">Today, 2:00 PM</span>
                    </div>
                </div>
                <div class="list-group-item py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 fw-semibold" style="font-size: 0.9rem;">Office Renovation</h6>
                            <p class="mb-0 small text-muted">Bob Johnson - Send quotation.</p>
                        </div>
                        <span class="badge bg-danger">Overdue</span>
                    </div>
                </div>
                <div class="list-group-item py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 fw-semibold" style="font-size: 0.9rem;">Apartment Interior</h6>
                            <p class="mb-0 small text-muted">Charlie Davis - Site visit.</p>
                        </div>
                        <span class="badge bg-info">Tomorrow</span>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center bg-transparent">
                <a href="#" class="text-decoration-none small text-muted hover-primary">View All Follow-ups</a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-transparent">
                <h6 class="fw-bold mb-0">Recent Activity</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3 text-primary d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <div class="text-dark"><span class="fw-semibold">New Lead created</span> by John Doe</div>
                            <div class="text-muted" style="font-size: 0.75rem;">10 mins ago</div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="rounded-circle bg-success bg-opacity-10 p-2 me-3 text-success d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <div class="text-dark"><span class="fw-semibold">Quotation #1024</span> approved</div>
                            <div class="text-muted" style="font-size: 0.75rem;">1 hour ago</div>
                        </div>
                    </li>
                    <li class="list-group-item d-flex align-items-center py-3">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3 text-danger d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <div class="text-dark"><span class="fw-semibold">Expense added</span>: Office Supplies (₹1,500)</div>
                            <div class="text-muted" style="font-size: 0.75rem;">2 hours ago</div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Left side Tables -->
    <div class="col-xl-8 order-xl-1">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                <h6 class="fw-bold mb-0">Recent Quotations</h6>
                <a href="#" class="btn btn-sm btn-light border-0">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>#</th>
                                <th>Client Name</th>
                                <th>Project Type</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>QT-1025</td>
                                <td><span class="fw-semibold text-dark">Acme Corp</span></td>
                                <td>Commercial Interior</td>
                                <td class="fw-medium">₹2,50,000</td>
                                <td><span class="badge badge-status-pending">Sent</span></td>
                            </tr>
                            <tr>
                                <td>QT-1024</td>
                                <td><span class="fw-semibold text-dark">Sarah Jenkins</span></td>
                                <td>Residential Kitchen</td>
                                <td class="fw-medium">₹85,000</td>
                                <td><span class="badge badge-status-active">Approved</span></td>
                            </tr>
                            <tr>
                                <td>QT-1023</td>
                                <td><span class="fw-semibold text-dark">Tech Solutions Inc</span></td>
                                <td>Office Layout</td>
                                <td class="fw-medium">₹5,40,000</td>
                                <td><span class="badge badge-status-inactive">Rejected</span></td>
                            </tr>
                            <tr>
                                <td>QT-1022</td>
                                <td><span class="fw-semibold text-dark">Michael Chang</span></td>
                                <td>Living Room Setup</td>
                                <td class="fw-medium">₹65,000</td>
                                <td><span class="badge badge-status-active">Approved</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-transparent">
                <h6 class="fw-bold mb-0">Recent Expenses</h6>
                <a href="#" class="btn btn-sm btn-light border-0">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light text-muted">
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Today</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary">Marketing</span></td>
                                <td class="text-dark">Facebook Ads Campaign</td>
                                <td class="fw-semibold">₹15,000</td>
                            </tr>
                            <tr>
                                <td>Yesterday</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary">Materials</span></td>
                                <td class="text-dark">Plywood and Hardware</td>
                                <td class="fw-semibold">₹42,500</td>
                            </tr>
                            <tr>
                                <td>15 Jan, 2024</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-secondary">Travel</span></td>
                                <td class="text-dark">Site Visit Fuel</td>
                                <td class="fw-semibold">₹850</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Dummy Data for Revenue vs Expenses
        // const ctxRev = document.getElementById('revenueExpenseChart');
        // if(ctxRev) {
        //     new Chart(ctxRev.getContext('2d'), {
        //         type: 'bar',
        //         data: {
        //             labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        //             datasets: [
        //                 {
        //                     label: 'Revenue',
        //                     data: [120000, 190000, 150000, 220000, 180000, 250000],
        //                     backgroundColor: 'rgba(13, 110, 253, 0.8)',
        //                     borderRadius: 4
        //                 },
        //                 {
        //                     label: 'Expenses',
        //                     data: [80000, 95000, 70000, 110000, 90000, 130000],
        //                     backgroundColor: 'rgba(220, 53, 69, 0.8)',
        //                     borderRadius: 4
        //                 }
        //             ]
        //         },
        //         options: {
        //             responsive: true,
        //             maintainAspectRatio: false,
        //             scales: {
        //                 y: {
        //                     beginAtZero: true,
        //                     grid: { borderDash: [2, 4], color: '#e2e8f0' }
        //                 },
        //                 x: {
        //                     grid: { display: false }
        //                 }
        //             },
        //             plugins: {
        //                 legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } }
        //             }
        //         }
        //     });
        // }

        // Dummy Data for Leads Created
        const ctxLeads = document.getElementById('leadsChart');
        if(ctxLeads) {
            new Chart(ctxLeads.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'New Leads',
                        data: [15, 25, 20, 35, 30, 45],
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#17a2b8',
                        pointRadius: 4,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 4], color: '#e2e8f0' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    });
</script>
@endpush
