@extends('layouts.main')

@section('title', 'Update Report')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Report</h1>
    <div class="card p-4">
        <form action="{{ route('laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="Foto" class="form-label">Add New Photos:</label>
                <input type="file" class="form-control @error('Foto.*') is-invalid @enderror" id="Foto" name="Foto[]" multiple>
                @error('Foto.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('Foto')
                     <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                
                <div id="foto-preview-container" class="mt-2 d-flex flex-wrap gap-2"></div>
                <button type="button" class="btn btn-secondary mt-2" id="openCameraBtn">Take Photo</button>
                <div id="cameraContainer" style="display:none; margin-top:10px;">
                    <video id="video" autoplay playsinline style="width:100%; max-width:350px; border:1px solid #ccc; border-radius:8px;"></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <div class="mt-2">
                        <button type="button" class="btn btn-primary" id="captureBtn">
                            <i class="fas fa-camera me-1"></i>Capture Photo
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="closeCameraBtn">
                            <i class="fas fa-times me-1"></i>Close Camera
                        </button>
                    </div>
                </div>
                
                <div class="mt-3">
                    <label class="form-label">Existing Photos:</label>
                    <div class="d-flex flex-wrap gap-2">
                        @if(!empty($laporan->Foto) && is_array($laporan->Foto))
                            @foreach($laporan->Foto as $key => $foto)
                                <div class="position-relative">
                                    <input type="hidden" name="existing_photos[]" value="{{ $foto }}" id="existing-photo-{{ $key }}">
                                    <img src="{{ asset('images/' . $foto) }}" alt="Foto {{ $key+1 }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-photo" data-input-id="existing-photo-{{ $key }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No photos</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Area Dropdown -->
            <div class="mb-3">
                <label for="area_id" class="form-label">Area:</label>
                <select class="form-select @error('area_id') is-invalid @enderror" id="area_id" name="area_id" required>
                    <option value="">Select Area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ $laporan->area_id == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
                @error('area_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Station Dropdown (akan diisi dinamis berdasarkan area) -->
            <div class="mb-3">
                <label for="penanggung_jawab_id" class="form-label">Station: <small class="text-muted">(Optional)</small></label>
                <select class="form-select @error('penanggung_jawab_id') is-invalid @enderror" id="penanggung_jawab_id" name="penanggung_jawab_id" data-selected="{{ $laporan->penanggung_jawab_id }}">
                    <option value="">Select Station</option>
                    <!-- Options will be populated dynamically based on selected area -->
                </select>
                @error('penanggung_jawab_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Penanggung Jawab Display -->
            <div class="mb-3">
                <label for="supervisor" class="form-label">Supervisor:</label>
                <input type="text" class="form-control" id="supervisor" readonly>
            </div>

            <div class="mb-3">
                <label for="problem_category_id" class="form-label">Problem Category:</label>
                <select class="form-select @error('problem_category_id') is-invalid @enderror" id="problem_category_id" name="problem_category_id" required>
                    <option value="">Select Category</option>
                    @foreach($problemCategories as $category)
                        <option value="{{ $category->id }}" {{ $laporan->problem_category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('problem_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="deskripsi_masalah" class="form-label">Problem Description:</label>
                <textarea class="form-control @error('deskripsi_masalah') is-invalid @enderror" id="deskripsi_masalah" name="deskripsi_masalah" rows="3" required>{{ $laporan->deskripsi_masalah }}</textarea>
                @error('deskripsi_masalah')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="tenggat_waktu" class="form-label">Deadline:</label>
                <div class="elegant-date-group">
                    <div class="input-group">
                        <input type="date" class="form-control deadline-date @error('tenggat_waktu') is-invalid @enderror" id="tenggat_waktu" name="tenggat_waktu" value="{{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('Y-m-d') }}" required>
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>
                @error('tenggat_waktu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            <button type="submit" class="btn btn-primary mt-4">Update</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Camera & File Handling ---
    const fotoInput = document.getElementById('Foto');
    const previewContainer = document.getElementById('foto-preview-container');
    const openCameraBtn = document.getElementById('openCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const closeCameraBtn = document.getElementById('closeCameraBtn');
    
    let fileStore = []; // Store for new files
    let stream = null;

    if (fotoInput && openCameraBtn) {
        fotoInput.addEventListener('change', function(event) {
            const newFiles = Array.from(event.target.files);
            const cameraFiles = fileStore.filter(f => f.name.startsWith('camera-'));
            addFiles([...cameraFiles, ...newFiles], true);
        });

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
                    alert('Cannot access camera. Please ensure browser has camera permission.');
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

    // Remove existing photo
    document.querySelectorAll('.remove-photo').forEach(button => {
        button.addEventListener('click', function() {
            const inputId = this.getAttribute('data-input-id');
            const input = document.getElementById(inputId);
            if (input) {
                input.parentElement.remove();
            }
        });
    });
});
</script>
@endpush