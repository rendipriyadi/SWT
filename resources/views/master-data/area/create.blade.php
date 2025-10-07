@extends('layouts.main')

@section('title', 'Master Data - Tambah Area')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create Area</h1>
            <p class="text-muted">Add a new area along with the station and person in charge</p>
        </div>
        <div>
            <a href="{{ route('master-data.area.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Area
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form -->
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('master-data.area.store') }}" method="POST" id="areaForm">
                @csrf
                
                <!-- Area Name -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Area Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" 
                               placeholder="Masukkan nama area" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Station and People in Charge -->
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label">Station & Person in Charge <span class="text-danger">*</span></label>
                        <div id="stationContainer">
                            <!-- Station items will be added here -->
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addStation">
                            <i class="fas fa-plus me-1"></i>Create Station
                        </button>
                        @error('stations')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        @error('penanggung_jawab')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row">
                    <div class="col-12">
                        <hr>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save
                            </button>
                            <a href="{{ route('master-data.area.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let stationCount = 0;

    // Add station function
    function addStation(station = '', penanggungJawab = '', email = '') {
        stationCount++;
        const stationHtml = `
            <div class="station-item border rounded p-3 mb-3" data-index="${stationCount}">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Station</label>
                        <input type="text" class="form-control" name="stations[${stationCount}]" 
                               value="${station}" placeholder="Masukkan nama station" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Penanggung Jawab</label>
                        <input type="text" class="form-control" name="penanggung_jawab[${stationCount}]" 
                               value="${penanggungJawab}" placeholder="Masukkan nama penanggung jawab" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="emails[${stationCount}]" 
                               value="${email}" placeholder="Masukkan email (opsional)">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-station" 
                                ${stationCount === 1 ? 'style="display:none"' : ''}>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        $('#stationContainer').append(stationHtml);
    }

    // Add station button click
    $('#addStation').on('click', function() {
        addStation();
    });

    // Remove station button click
    $(document).on('click', '.remove-station', function() {
        $(this).closest('.station-item').remove();
        
        // Show/hide remove buttons
        const stationItems = $('.station-item');
        if (stationItems.length === 1) {
            stationItems.find('.remove-station').hide();
        }
    });

    // Add initial station if none exists
    if ($('.station-item').length === 0) {
        addStation();
    }

    // Form validation
    $('#areaForm').on('submit', function(e) {
        const stationItems = $('.station-item');
        if (stationItems.length === 0) {
            e.preventDefault();
            alert('At least one station must be added!');
            return false;
        }

        // Check if all fields are filled
        let allFilled = true;
        stationItems.each(function() {
            const station = $(this).find('input[name^="stations["]').val().trim();
            const penanggungJawab = $(this).find('input[name^="penanggung_jawab["]').val().trim();
            
            if (!station || !penanggungJawab) {
                allFilled = false;
                return false;
            }
        });

        if (!allFilled) {
            e.preventDefault();
            alert('Semua field station dan penanggung jawab harus diisi!');
            return false;
        }
    });
});
</script>
@endpush
