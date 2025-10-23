@extends('layouts.main')

@section('title', 'Master Data - Edit Area')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Area</h1>
            <p class="text-muted">Edit area: <strong>{{ $area->name }}</strong></p>
        </div>
        <div>
            <a href="{{ route('master-data.area.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Area
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('master-data.area.update', $area) }}" method="POST" id="areaForm">
                @csrf
                @method('PUT')
                
                <!-- Area Name -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Area Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $area->name) }}" 
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
                            <i class="fas fa-plus me-1"></i>Add Station
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
                                <i class="fas fa-save me-2"></i>Update
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
                        <input type="text" class="form-control" name="stations[]" 
                               value="${station}" placeholder="Enter the station name" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">People in Charge</label>
                        <input type="text" class="form-control" name="penanggung_jawab[]" 
                               value="${penanggungJawab}" placeholder="Enter the name of the person in charge" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="emails[]" 
                               value="${email}" placeholder="Enter email (optional)">
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
        const stationItem = $(this).closest('.station-item');
        const stationName = stationItem.find('input[name="stations[]"]').val() || 'this station';
        
        if (typeof Swal !== 'undefined') {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            const theme = isDark ? {
                background: '#1e1e1e',
                color: '#e0e0e0',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            } : {
                background: '#ffffff',
                color: '#212529',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            };
            
            Swal.fire({
                title: 'Delete Station',
                text: `Are you sure you want to delete "${stationName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                ...theme
            }).then((result) => {
                if (result.isConfirmed) {
                    stationItem.remove();
                    
                    // Show/hide remove buttons
                    const stationItems = $('.station-item');
                    if (stationItems.length === 1) {
                        stationItems.find('.remove-station').hide();
                    }
                }
            });
        } else {
            // Fallback to native confirm if SweetAlert2 is not available
            if (confirm(`Are you sure you want to delete "${stationName}"?`)) {
                stationItem.remove();
                
                // Show/hide remove buttons
                const stationItems = $('.station-item');
                if (stationItems.length === 1) {
                    stationItems.find('.remove-station').hide();
                }
            }
        }
    });

    // Load existing data
    @if($area->penanggungJawabs->count() > 0)
        @foreach($area->penanggungJawabs as $index => $pj)
            addStation('{{ $pj->station }}', '{{ $pj->name }}', '{{ $pj->email }}');
        @endforeach
    @else
        addStation();
    @endif

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
            const station = $(this).find('input[name="stations[]"]').val().trim();
            const penanggungJawab = $(this).find('input[name="penanggung_jawab[]"]').val().trim();
            
            if (!station || !penanggungJawab) {
                allFilled = false;
                return false;
            }
        });

        if (!allFilled) {
            e.preventDefault();
            alert('All field stations and persons in charge must be filled in.!');
            return false;
        }
    });
});
</script>
@endpush
