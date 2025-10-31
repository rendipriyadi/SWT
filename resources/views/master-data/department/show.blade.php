@extends('layouts.main')

@section('title', 'Detail Department')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Supervisor</h1>
        <div>
            <a href="{{ route('master-data.department.edit', $department) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('master-data.department.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Department
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold font-dark">Informasi Supervisor</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Supervisor:</strong></td>
                            <td>{{ $department->supervisor }}</td>
                        </tr>
                        <tr>
                            <td><strong>Department:</strong></td>
                            <td>{{ $department->departemen ?? $department->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Work Group:</strong></td>
                            <td>{{ $department->workgroup ?: '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
