@extends('layouts.main')

@section('title', 'Buat Laporan Baru')

@php
    use App\Models\ProblemCategory;
@endphp

@section('content')
<div class="container-fluid px-4">
    <div class="page-header mb-4">
        <div>
            <h1 class="mb-2">Add New Report</h1>
            <p class="text-muted mb-0">Create a new safety walk and talk report</p>
        </div>
        <div>
            <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary" style="border: 2px solid #6c757d;">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>
    <div class="card p-4">
        <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
            @csrf
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <!-- Completion Photos and Deadline side by side -->
            <div class="row g-4 mb-4">
                <!-- Completion Photos -->
                <div class="col-md-6">
                    <label for="Foto" class="form-label fw-semibold">Report Photos:</label>
                    <input type="file" class="form-control @error('Foto.*') is-invalid @enderror" id="Foto" name="Foto[]" accept="image/*" multiple>
                    @error('Foto.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <button type="button" class="btn btn-outline-secondary mt-2" id="openCameraBtn" style="border: 2px solid #6c757d;">
                        <i class="fas fa-camera me-2"></i>Take Photo
                    </button>
                    <div id="cameraContainer" style="display:none; margin-top:10px;">
                        <video id="video" autoplay playsinline style="width:100%; max-width:350px; border:1px solid #ccc; border-radius:8px;"></video>
                        <canvas id="canvas" style="display:none;"></canvas>
                        <div class="mt-2">
                            <button type="button" class="btn btn-primary" id="captureBtn">
                                <i class="fas fa-camera me-1"></i>Take Photo
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="closeCameraBtn">
                                <i class="fas fa-times me-1"></i>Close Camera
                            </button>
                        </div>
                    </div>
                    <div id="foto-preview-container" class="mt-3 d-flex flex-wrap gap-2"></div>
                </div>

                <!-- Deadline -->
                <div class="col-md-6">
                    <label for="tenggat_waktu" class="form-label fw-semibold">Deadline <span class="text-danger">*</span></label>
                    <div class="elegant-date-input">
                        <input type="text" class="form-control elegant-datepicker @error('tenggat_waktu') is-invalid @enderror" id="tenggat_waktu" name="tenggat_waktu" value="{{ old('tenggat_waktu') }}" placeholder="Select date..." required>
                        <i class="fas fa-calendar-alt calendar-icon"></i>
                    </div>
                    @error('tenggat_waktu')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Form Fields in Grid Layout -->
            <div class="row g-4">
                <!-- Area Dropdown -->
                <div class="col-md-6">
                    <label for="area_id" class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
                    <select class="form-select @error('area_id') is-invalid @enderror" id="area_id" name="area_id" required>
                        <option value="">Select Area</option>
                        @foreach(($areas ?? []) as $area)
                            <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                        @endforeach
                    </select>
                    @error('area_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Station Dropdown -->
                <div class="col-md-6">
                    <label for="penanggung_jawab_id" class="form-label fw-semibold">Station <small class="text-muted">(Optional)</small></label>
                    <select class="form-select @error('penanggung_jawab_id') is-invalid @enderror" id="penanggung_jawab_id" name="penanggung_jawab_id" data-selected="{{ old('penanggung_jawab_id') }}">
                        <option value="">Select Station</option>
                    </select>
                    @error('penanggung_jawab_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Person in Charge Display -->
                <div class="col-md-6">
                    <label for="supervisor" class="form-label fw-semibold">Person in Charge</label>
                    <input type="text" class="form-control" id="supervisor" readonly>
                    <div class="mt-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="addAdditionalPic">
                            <i class="fas fa-plus me-1"></i>Add Person in Charge
                        </button>
                    </div>
                    <!-- Additional PICs Container -->
                    <div id="additionalPicContainer" class="mt-2">
                        <!-- Additional PIC items will be added here -->
                    </div>
                    @error('additional_pics')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Problem Category -->
                <div class="col-md-6">
                    <label for="problem_category_id" class="form-label fw-semibold">Problem Category <span class="text-danger">*</span></label>
                    <select class="form-select @error('problem_category_id') is-invalid @enderror" id="problem_category_id" name="problem_category_id" required>
                        <option value="">Select Category</option>
                        @foreach(ProblemCategory::active()->ordered()->get() as $category)
                            <option value="{{ $category->id }}" {{ old('problem_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('problem_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Problem Description -->
                <div class="col-12">
                    <label for="deskripsi_masalah" class="form-label fw-semibold">Problem Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('deskripsi_masalah') is-invalid @enderror" id="deskripsi_masalah" name="deskripsi_masalah" rows="4" required placeholder="Describe the problem in detail...">{{ old('deskripsi_masalah') }}</textarea>
                    @error('deskripsi_masalah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <!-- Submit Button -->
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                    <i class="fas fa-paper-plane me-2"></i>Submit Report
                </button>
            </div>
        </form>
    </div>
</div>

@if ($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function() {
    let errorMessages = @json($errors->all());
    let formattedErrors = errorMessages.map(msg => `• ${msg}`).join('<br>');
    
    let toastBody = document.getElementById('mainToastBody');
    let toastEl = document.getElementById('mainToast');
    let toastIcon = document.getElementById('mainToastIcon');
    
    if (toastBody && toastEl && toastIcon) {
        toastBody.innerHTML = formattedErrors;
        toastEl.classList.remove('bg-success', 'bg-info', 'bg-warning');
        toastEl.classList.add('bg-danger');
        toastIcon.innerHTML = '<i class="fas fa-exclamation-circle text-danger fs-5"></i>';
        
        let toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>
@endif

@if (session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    let toastBody = document.getElementById('mainToastBody');
    let toastEl = document.getElementById('mainToast');
    let toastIcon = document.getElementById('mainToastIcon');
    
    if (toastBody && toastEl && toastIcon) {
        toastBody.innerHTML = '{{ session('success') }}';
        toastEl.classList.remove('bg-danger', 'bg-info', 'bg-warning');
        toastEl.classList.add('bg-success');
        toastIcon.innerHTML = '<i class="fas fa-check-circle text-success fs-5"></i>';
        
        let toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});
</script>
@endif
@endsection

@push('scripts')
<style>
/* Upload Area Styling */
.upload-area {
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: var(--primary-dark) !important;
    background-color: rgba(37, 99, 235, 0.05);
}

.upload-area.dragover {
    border-color: var(--primary-color) !important;
    background-color: rgba(37, 99, 235, 0.1);
}

/* Form Styling */
.form-label {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 0.75rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
}

/* Photo Preview */
#foto-preview-container img {
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    transition: transform 0.2s ease;
}

#foto-preview-container img:hover {
    transform: scale(1.05);
}

/* Camera Container */
#cameraContainer {
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    padding: 1rem;
    background: var(--background-color);
}

/* Page Header */
.page-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
}

.page-header p {
    color: var(--text-secondary);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure CSRF token is fresh
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const formToken = document.querySelector('input[name="_token"]');
    if (formToken) {
        formToken.value = csrfToken;
    }
    
    // --- Camera & File Handling ---
    const fotoInput = document.getElementById('Foto');
    const previewContainer = document.getElementById('foto-preview-container');
    const openCameraBtn = document.getElementById('openCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const closeCameraBtn = document.getElementById('closeCameraBtn');
    
    let fileStore = []; // Store for files
    let stream = null;

    if (fotoInput && openCameraBtn) {
        // File input change handler
        fotoInput.addEventListener('change', function(event) {
            const newFiles = Array.from(event.target.files);
            addFiles(newFiles, false); // false = append, tidak replace
        });

        // Drag and drop functionality
        const uploadArea = document.querySelector('.upload-area');
        if (uploadArea) {
            uploadArea.addEventListener('click', () => fotoInput.click());
            
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
                if (files.length > 0) {
                    addFiles(files, false); // false = append, tidak replace
                }
            });
        }

        openCameraBtn.addEventListener('click', async function () {
            cameraContainer.style.display = 'block';
            openCameraBtn.style.display = 'none';
            
            try {
                // Try rear camera first
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: "environment" } 
                });
            } catch (e) {
                try {
                    // Fallback to any available camera
                    stream = await navigator.mediaDevices.getUserMedia({ 
                        video: true 
                    });
                } catch (err) {
                    alert('Tidak dapat mengakses kamera. Pastikan browser memiliki izin mengakses kamera.');
                    openCameraBtn.style.display = 'inline-block';
                    cameraContainer.style.display = 'none';
                }
            }
            
            if (stream) {
                video.srcObject = stream;
            }
        });

        captureBtn.addEventListener('click', function () {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(function (blob) {
                const newFile = new File([blob], `camera-${Date.now()}.jpg`, { type: 'image/jpeg' });
                addFiles([newFile]);
            }, 'image/jpeg', 0.95);
        });

        closeCameraBtn.addEventListener('click', stopCamera);

        function addFiles(newFiles, isReplacement = false) {
            let combined = isReplacement ? newFiles : [...fileStore, ...newFiles];
            fileStore = combined;
            updateFileInput();
            renderPreviews();
            openCameraBtn.style.display = 'inline-block';
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            video.srcObject = null;
            cameraContainer.style.display = 'none';
            openCameraBtn.style.display = 'inline-block';
        }

        function renderPreviews() {
            previewContainer.innerHTML = '';
            fileStore.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'position-relative';
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-preview-btn" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    previewContainer.appendChild(preview);
                    
                    // Add event listener to remove button
                    preview.querySelector('.remove-preview-btn').addEventListener('click', function() {
                        const idx = this.getAttribute('data-index');
                        fileStore.splice(idx, 1);
                        updateFileInput();
                        renderPreviews();
                    });
                }
                reader.readAsDataURL(file);
            });
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            fileStore.forEach(file => dataTransfer.items.add(file));
            fotoInput.files = dataTransfer.files;
        }
    }
    
    // Form submission handling with proper loading state
    const form = document.getElementById('reportForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Remove empty additional PIC selects before submission
            const additionalPicSelects = document.querySelectorAll('.additional-pic-select');
            let hasEmptySelect = false;
            
            additionalPicSelects.forEach(select => {
                if (!select.value || select.value === '') {
                    // Remove the entire additional-pic-item if no selection made
                    const picItem = select.closest('.additional-pic-item');
                    if (picItem) {
                        picItem.remove();
                    }
                    hasEmptySelect = true;
                }
            });
            
            // Update CSRF token before submission
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formToken = document.querySelector('input[name="_token"]');
            if (formToken) {
                formToken.value = csrfToken;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            
            // Keep loading state active until page unloads (redirect happens)
            // This ensures button stays disabled during server processing and redirect
            window.addEventListener('beforeunload', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
            });
            
            // Fallback: Re-enable after 30 seconds if something goes wrong
            setTimeout(() => {
                if (document.body.contains(submitBtn)) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Report';
                }
            }, 30000);
        });
    }

    // ============================================================================
    // Additional PIC Management (Simplified)
    // ============================================================================
    let additionalPicCount = 0;
    const additionalPicContainer = document.getElementById('additionalPicContainer');
    const addAdditionalPicBtn = document.getElementById('addAdditionalPic');

    // Get all penanggung jawab from all areas
    let allPenanggungJawab = [];
    
    // Fetch all penanggung jawab from server
    fetch(window.routes.allPenanggungJawab)
        .then(response => response.json())
        .then(data => {
            allPenanggungJawab = data.penanggung_jawab || [];
        })
        .catch(error => {
            console.error('Error fetching penanggung jawab:', error);
            allPenanggungJawab = [];
        });

    // Get filtered PIC options (exclude main PICs and selected Additional PICs)
    function getFilteredPicOptions(currentSelectValue = null) {
        // Get currently selected area
        const selectedAreaId = document.getElementById('area_id').value;
        
        // Get currently selected Station (if any)
        const selectedStationId = document.getElementById('penanggung_jawab_id').value;
        
        // Get supervisor names (main PICs) from supervisor input
        const supervisorInput = document.getElementById('supervisor');
        const mainPicNames = supervisorInput ? supervisorInput.value.split(',').map(name => name.trim()).filter(name => name) : [];
        
        // Get already selected Additional PICs (exclude current select to allow keeping its value)
        const selectedAdditionalPics = Array.from(document.querySelectorAll('.additional-pic-select'))
            .map(select => select.value)
            .filter(val => val !== '' && val !== currentSelectValue);
        
        let picOptions = '<option value="">Select Person in Charge</option>';
        allPenanggungJawab.forEach(pj => {
            // Add null checks to prevent errors
            if (!pj || !pj.id || !pj.name) {
                return; // Skip invalid records
            }
            
            const pjId = pj.id.toString();
            
            // If area is selected but no station (show all area PICs as main)
            if (selectedAreaId && !selectedStationId) {
                // Exclude ALL PICs from same area EXCEPT General
                if (pj.area_id && pj.area_id.toString() === selectedAreaId.toString()) {
                    // Only allow General from the same area
                    if (!pj.station || pj.station.toLowerCase() !== 'general') {
                        return; // Don't add - they are main PICs or non-General
                    }
                    // If it's General from same area, allow it to continue
                }
            }
            
            // If station is selected, exclude that specific PIC
            if (selectedStationId && pjId === selectedStationId.toString()) {
                return; // Don't add this option
            }
            
            // Skip if this PIC is already selected as Additional PIC (except current select)
            if (selectedAdditionalPics.includes(pjId)) {
                return; // Don't add this option
            }
            
            // Safe display with fallback values
            const displayName = pj.name || 'Unknown';
            const displayStation = pj.station || 'No Station';
            const displayArea = pj.area_name || 'Unknown Area';
            
            picOptions += `<option value="${pj.id}">${displayName} - ${displayStation} (${displayArea})</option>`;
        });
        
        return picOptions;
    }
    
    // Refresh all Additional PIC dropdowns
    function refreshAllAdditionalPicDropdowns() {
        const allSelects = document.querySelectorAll('.additional-pic-select');
        allSelects.forEach(select => {
            const currentValue = select.value;
            const newOptions = getFilteredPicOptions(currentValue);
            select.innerHTML = newOptions;
            
            // Try to restore the previous value if it's still valid
            if (currentValue) {
                const optionExists = Array.from(select.options).some(opt => opt.value === currentValue);
                if (optionExists) {
                    select.value = currentValue;
                } else {
                    select.value = '';
                }
            }
        });
    }

    // Add additional PIC function (with duplicate prevention)
    function addAdditionalPic() {
        if (allPenanggungJawab.length === 0) {
            alert('PIC data not loaded yet. Please wait a moment and try again.');
            return;
        }
        
        additionalPicCount++;
        
        const picOptions = getFilteredPicOptions();

        const picHtml = `
            <div class="additional-pic-item mb-2" data-index="${additionalPicCount}">
                <div class="input-group">
                    <select class="form-select additional-pic-select" name="additional_pics[${additionalPicCount}]">
                        ${picOptions}
                    </select>
                    <button type="button" class="btn btn-outline-danger remove-additional-pic">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        
        additionalPicContainer.insertAdjacentHTML('beforeend', picHtml);
    }

    // Add additional PIC button click
    if (addAdditionalPicBtn) {
        addAdditionalPicBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addAdditionalPic();
        });
    }

    // Remove additional PIC button click (event delegation)
    if (additionalPicContainer) {
        additionalPicContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-additional-pic')) {
                const picItem = e.target.closest('.additional-pic-item');
                picItem.remove();
                // Refresh all remaining dropdowns after removal
                refreshAllAdditionalPicDropdowns();
            }
        });
    }

    // Handle Additional PIC selection change
    if (additionalPicContainer) {
        additionalPicContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('additional-pic-select')) {
                // Refresh all dropdowns to update available options
                refreshAllAdditionalPicDropdowns();
            }
        });
    }
    
    // Listen for changes in Area dropdown
    const areaSelect = document.getElementById('area_id');
    if (areaSelect) {
        areaSelect.addEventListener('change', function() {
            // Refresh all Additional PIC dropdowns when area changes
            setTimeout(() => {
                refreshAllAdditionalPicDropdowns();
            }, 500); // Wait for area-station.js to finish updating
        });
    }
    
    // Listen for changes in Station dropdown (Person in Charge)
    const penanggungJawabSelect = document.getElementById('penanggung_jawab_id');
    if (penanggungJawabSelect) {
        penanggungJawabSelect.addEventListener('change', function() {
            const selectedMainPic = this.value;
            
            // If a Person in Charge is selected, check if it's already in Additional PICs
            if (selectedMainPic) {
                const additionalPicSelects = document.querySelectorAll('.additional-pic-select');
                let removedAny = false;
                
                additionalPicSelects.forEach(select => {
                    if (select.value === selectedMainPic) {
                        select.closest('.additional-pic-item').remove();
                        removedAny = true;
                    }
                });
                
                if (removedAny) {
                    alert('This person was already selected as Additional PIC and has been removed.');
                }
            }
            
            // Refresh all remaining Additional PIC dropdowns
            refreshAllAdditionalPicDropdowns();
        });
    }
});
</script>

<!-- Area-Station Management Script -->
<script src="{{ asset('js/area-station.js') }}"></script>
@endpush