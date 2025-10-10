@extends('layouts.main')

@section('title', 'Buat Laporan Baru')

@section('content')
<div class="container-fluid px-4">
    <div class="page-header mb-4 position-relative">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-2">Add New Report</h1>
                <p class="text-muted mb-0">Create a new safety walk and talk report</p>
            </div>
        </div>
        <!-- Tombol Back di kanan atas -->
        <div class="position-absolute" style="top: 0; right: 0;">
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
                    <label for="Foto" class="form-label fw-semibold">Completion Photos:</label>
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
                    <input type="date" class="form-control @error('tenggat_waktu') is-invalid @enderror" id="tenggat_waktu" name="tenggat_waktu" value="{{ old('tenggat_waktu') }}" required>
                    @error('tenggat_waktu')
                        <div class="invalid-feedback">{{ $message }}</div>
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
                </div>

                <!-- Problem Category -->
                <div class="col-md-6">
                    <label for="problem_category_id" class="form-label fw-semibold">Problem Category <span class="text-danger">*</span></label>
                    <select class="form-select @error('problem_category_id') is-invalid @enderror" id="problem_category_id" name="problem_category_id" required>
                        <option value="">Select Category</option>
                        @foreach(\App\Models\ProblemCategory::active()->ordered()->get() as $category)
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
            const cameraFiles = fileStore.filter(f => f.name.startsWith('camera-'));
            addFiles([...cameraFiles, ...newFiles], true);
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
                    const cameraFiles = fileStore.filter(f => f.name.startsWith('camera-'));
                    addFiles([...cameraFiles, ...files], true);
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
    
    // Form submission handling
    const form = document.getElementById('reportForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Update CSRF token before submission
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formToken = document.querySelector('input[name="_token"]');
            if (formToken) {
                formToken.value = csrfToken;
            }
            
            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            
            // Re-enable after 5 seconds as fallback
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Report';
            }, 5000);
        });
    }
});
</script>
@endpush