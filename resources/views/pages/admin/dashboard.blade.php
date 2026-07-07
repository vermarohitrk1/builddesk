@extends('layouts.main')

@section('content')

    <div class="row">
        <div class="col-md-8 mx-auto text-center mt-5">
            <h1 class="fw-bold mb-3">Welcome, Super Admin!</h1>
            <p class="text-muted fs-5">You are properly authenticated using the <code>super_admin</code> multi-auth guard, safely isolated from conventional tenant access.</p>
        </div>
    </div>

@endsection