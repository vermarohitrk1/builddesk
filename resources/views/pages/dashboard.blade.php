@extends('layouts.main')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>Dashboard</h2>
        <p>Welcome, {{ Auth::user()->name }} ({{ strtoupper(str_replace('_', ' ', Auth::user()->role)) }})</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Leads</h5>
                <p class="card-text fs-2">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Customers</h5>
                <p class="card-text fs-2">0</p>
            </div>
        </div>
    </div>
</div>
@endsection
