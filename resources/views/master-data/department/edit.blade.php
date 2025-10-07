@extends('layouts.main')

@section('title', 'Edit Department')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Supervisor</h1>
        <a href="{{ route('master-data.department.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Department
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold font-dark">Form Edit Supervisor</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('master-data.department.update', $department->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="supervisor" class="form-label">Supervisor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('supervisor') is-invalid @enderror" 
                                   id="supervisor" name="supervisor" value="{{ old('supervisor', $department->supervisor) }}" required>
                            @error('supervisor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $department->departemen ?? $department->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="workgroup" class="form-label">Work Group</label>
                            <input type="text" class="form-control @error('workgroup') is-invalid @enderror" 
                                   id="workgroup" name="workgroup" value="{{ old('workgroup', $department->workgroup ?? '') }}">
                            @error('workgroup')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $department->email ?? '') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('master-data.department.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
