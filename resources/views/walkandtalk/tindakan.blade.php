@extends('layouts.main')

@section('title', 'Completion of Reports')

@section('content')
<div class="container-fluid px-4">
    <div class="page-header mb-4">
        <div>
            <h1 class="mb-2">Completion of Reports</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('laporan.index') }}" class="text-decoration-none">Reports</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Completion of Reports</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('laporan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>
    
    <!-- Tampilkan detail masalah dengan struktur data baru -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Problem Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <h6 class="fw-bold">Report Date:</h6>
                    <p>{{ $laporan->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Area:</h6>
                    <p>{{ $laporan->area->name ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Station:</h6>
                    <p>{{ $laporan->penanggungJawab->station ?? '-' }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <h6 class="fw-bold">Status:</h6>
                    @php
                        $statusMap = [
                            'Ditugaskan' => 'Assigned',
                            'Selesai' => 'Completed',
                        ];
                        $statusEn = $statusMap[$laporan->status] ?? $laporan->status;
                    @endphp
                    <p>{{ $statusEn }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Problem Category:</h6>
                    <p>{{ $laporan->problemCategory->name ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Deadline:</h6>
                    <p>{{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <h6 class="fw-bold">Person in Charge:</h6>
                    <p>
                        @if($laporan->penanggungJawab)
                            {{ $laporan->penanggungJawab->name }}
                        @elseif($laporan->area && $laporan->area->penanggungJawabs && $laporan->area->penanggungJawabs->count() > 0)
                            {{ $laporan->area->penanggungJawabs->pluck('name')->join(', ') }}
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <strong>Problem Description:</strong>
                    <p style="white-space: pre-line;">{{ $laporan->deskripsi_masalah }}</p>
                </div>
            </div>
            @if(!empty($laporan->Foto) && is_array($laporan->Foto))
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="fw-bold">Problem Photos:</h6>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach($laporan->Foto as $foto)
                            <div class="position-relative">
                                <img 
                                    src="{{ asset('images/' . $foto) }}" 
                                    class="img-thumbnail cursor-pointer" 
                                    style="height: 100px; object-fit: cover;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalFotoFull"
                                    data-photos="{{ json_encode(array_map(function($f) { return asset('images/' . $f); }, $laporan->Foto)) }}"
                                    alt="Issue Photo"
                                >
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <div class="card p-4">
        <form action="{{ route('laporan.storeTindakan', $laporan->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="Tanggal" class="form-label fw-semibold">Completion Date <span class="text-danger">*</span></label>
                <div class="elegant-date-group">
                    <div class="input-group">
                        <input type="text" class="form-control completion-date @error('Tanggal') is-invalid @enderror" id="Tanggal_display" placeholder="Select completion date..." readonly required>
                        <input type="hidden" id="Tanggal" name="Tanggal" value="{{ old('Tanggal') }}">
                        <span class="input-group-text">
                            <i class="fas fa-calendar-alt"></i>
                        </span>
                    </div>
                </div>
                @error('Tanggal')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <!-- Jika menampilkan tanggal yang sudah ada: -->
            @if(isset($penyelesaian) && $penyelesaian->Tanggal)
                <div class="mb-3">
                    <label class="form-label">Completion Date:</label>
                    <p>{{ \Carbon\Carbon::parse($penyelesaian->Tanggal)->locale('en')->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
            @endif
            <div class="mb-3">
                <label for="Foto" class="form-label">Completion Photos:</label>
                <input type="file" class="form-control @error('Foto.*') is-invalid @enderror" id="Foto" name="Foto[]" multiple>
                @error('Foto.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div id="foto-preview-container" class="mt-2 d-flex flex-wrap gap-2"></div>
                <button type="button" class="btn btn-secondary mt-2" id="openCameraBtn">Take Photo</button>
                <div id="cameraContainer" style="display:none; margin-top:10px;">
                    <video id="video" autoplay playsinline style="width:100%; max-width:350px; border:1px solid #ccc; border-radius:8px;"></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <div class="mt-2">
                        <button type="button" class="btn btn-success" id="captureBtn">Take Photo</button>
                        <button type="button" class="btn btn-danger" id="closeCameraBtn">Close Camera</button>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="deskripsi_penyelesaian" class="form-label">Completion Description:</label>
                <textarea class="form-control @error('deskripsi_penyelesaian') is-invalid @enderror" id="deskripsi_penyelesaian" name="deskripsi_penyelesaian" rows="3">{{ old('deskripsi_penyelesaian') }}</textarea>
                @error('deskripsi_penyelesaian')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                    <option value="Ditugaskan" {{ old('status', $laporan->status) == 'Ditugaskan' ? 'selected' : '' }}>Assigned</option>
                    <option value="Selesai" {{ old('status', $laporan->status) == 'Selesai' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary mt-4">Save</button>
        </form>
    </div>
</div>

<!-- Modal Preview Foto Full -->
<div class="modal fade" id="modalFotoFull" tabindex="-1" aria-labelledby="modalFotoFullLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body text-center p-0">
        <div id="photoCarousel" class="carousel slide">
          <div class="carousel-inner">
            <!-- Carousel items will be injected here -->
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </div>
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

@if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let pesan = `{!! session('error') !!}`;
            let toastBody = document.getElementById('mainToastBody');
            let toastEl = document.getElementById('mainToast');
            let toastIcon = document.getElementById('mainToastIcon');
            if (toastBody && toastEl && toastIcon) {
                toastBody.innerHTML = pesan;
                toastEl.classList.remove('bg-success', 'bg-info', 'bg-warning');
                toastEl.classList.add('bg-danger', 'text-white');
                toastIcon.innerHTML = `<i class="fas fa-times-circle text-white"></i>`;
            }
        });
    </script>
@endif
@endsection

@push('styles')
<style>
/* Keep only minimal tweaks for the tindakan datepicker; let global elegant styles handle positioning */
.input-group-text { cursor: pointer; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const tanggalInput = document.getElementById('Tanggal');
    const tanggalInputDisplay = document.getElementById('Tanggal_display');
    const deskripsiInput = document.getElementById('deskripsi_penyelesaian');
    const fotoInput = document.getElementById('Foto');

    function updateRequiredFields() {
        if (statusSelect.value === 'Selesai') {
            tanggalInput.required = true;
            deskripsiInput.required = true;
        } else {
            tanggalInput.required = false;
            deskripsiInput.required = false;
        }
    }

    statusSelect.addEventListener('change', updateRequiredFields);
    updateRequiredFields(); // initial

    // --- Bootstrap Datepicker for Completion Date ---
    if (tanggalInputDisplay) {
        const dateInputGroup = tanggalInputDisplay.closest('.input-group');
        
        // Initialize datepicker on display input
        const containerEl = dateInputGroup.closest('.elegant-date-group') || 'body';
        $(tanggalInputDisplay).datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            startDate: new Date(),
            orientation: 'auto',
            container: containerEl,
            todayBtn: 'linked',
            clearBtn: true,
            language: 'en',
            weekStart: 1,
            calendarWeeks: true,
            showOnFocus: true,
            toggleActive: true,
            templates: {
                leftArrow: '<i class="fas fa-chevron-left"></i>',
                rightArrow: '<i class="fas fa-chevron-right"></i>'
            }
        }).on('changeDate', function(e) {
            // Validate selected date
            const selectedDate = e.date;
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                $(this).addClass('is-invalid');
                $(this).attr('data-bs-original-title', 'Completion date cannot be in the past');
            } else {
                $(this).removeClass('is-invalid');
                $(this).removeAttr('data-bs-original-title');
            }
            
            // Convert to Y-m-d format for hidden input
            const year = selectedDate.getFullYear();
            const month = String(selectedDate.getMonth() + 1).padStart(2, '0');
            const day = String(selectedDate.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;
            
            // Update hidden input with correct format
            tanggalInput.value = formattedDate;
        });
        
        // Rely on plugin placement; no custom fixed positioning
        
        // Trigger datepicker when input group text is clicked
        $('#datePickerBtn').on('click', function() {
            $(tanggalInputDisplay).datepicker('show');
        });
        
        // Load existing value if any
        if (tanggalInput.value) {
            const existingDate = new Date(tanggalInput.value);
            if (!isNaN(existingDate.getTime())) {
                const day = String(existingDate.getDate()).padStart(2, '0');
                const month = String(existingDate.getMonth() + 1).padStart(2, '0');
                const year = existingDate.getFullYear();
                tanggalInputDisplay.value = `${day}/${month}/${year}`;
            }
        }
    }
    
    // Foto preview dan kamera
    const previewContainer = document.getElementById('foto-preview-container');
    const openCameraBtn = document.getElementById('openCameraBtn');
    const cameraContainer = document.getElementById('cameraContainer');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureBtn = document.getElementById('captureBtn');
    const closeCameraBtn = document.getElementById('closeCameraBtn');
    
    let fileStore = [];
    let stream = null;

    if (!openCameraBtn) return;

    fotoInput.addEventListener('change', function(event) {
        const newFiles = Array.from(event.target.files);
        const cameraFiles = fileStore.filter(f => f.name.startsWith('penyelesaian-'));
        addFiles([...cameraFiles, ...newFiles], true);
    });

    openCameraBtn.addEventListener('click', async function () {
        cameraContainer.style.display = 'block';
        openCameraBtn.style.display = 'none';
        
        try {
            // Pertama coba gunakan kamera belakang
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { facingMode: "environment" } 
            });
        } catch (e) {
            try {
                // Fallback ke kamera depan jika kamera belakang tidak tersedia
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: true 
                });
            } catch (err) {
                alert('Cannot access the camera. Please grant camera permission.');
                stopCamera();
                return;
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
            const newFile = new File([blob], `penyelesaian-${Date.now()}.jpg`, { type: 'image/jpeg' });
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
                const wrapper = document.createElement('div');
                wrapper.className = 'position-relative d-inline-block';
                wrapper.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="width:100px; height:100px; object-fit:cover;">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 p-0 d-flex justify-content-center align-items-center" style="width:20px;height:20px;line-height:1;">&times;</button>
                `;
                wrapper.querySelector('button').onclick = () => {
                    fileStore.splice(index, 1);
                    updateFileInput();
                    renderPreviews();
                };
                previewContainer.appendChild(wrapper);
            }
            reader.readAsDataURL(file);
        });
    }

    function updateFileInput() {
        const dataTransfer = new DataTransfer();
        fileStore.forEach(file => dataTransfer.items.add(file));
        fotoInput.files = dataTransfer.files;
    }
    
    // Modal untuk galeri foto masalah
    document.querySelectorAll('img[data-bs-toggle="modal"][data-bs-target="#modalFotoFull"]').forEach(img => {
        img.addEventListener('click', function() {
            const photos = JSON.parse(this.getAttribute('data-photos'));
            const carouselInner = document.querySelector('#photoCarousel .carousel-inner');
            carouselInner.innerHTML = '';
            
            if (photos && Array.isArray(photos) && photos.length > 0) {
                photos.forEach((photoUrl, index) => {
                    const activeClass = index === 0 ? 'active' : '';
                    carouselInner.innerHTML += `
                        <div class="carousel-item ${activeClass}">
                            <img src="${photoUrl}" class="d-block w-100 img-fluid rounded shadow" style="max-height:80vh; object-fit: contain;" alt="Foto Laporan">
                        </div>
                    `;
                });
            }
            
            // Tampilkan/sembunyikan tombol navigasi carousel
            const prevNextButtons = document.querySelectorAll('#photoCarousel .carousel-control-prev, #photoCarousel .carousel-control-next');
            prevNextButtons.forEach(btn => {
                btn.style.display = photos && photos.length > 1 ? 'block' : 'none';
            });
        });
    });
});
</script>
@endpush