@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-danger">Dashboard Error</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Error!</h5>
                        {{ $error }}
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary mr-2">Refresh Dashboard</a>
                        <a href="{{ route('home') }}" class="btn btn-secondary">Return to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
