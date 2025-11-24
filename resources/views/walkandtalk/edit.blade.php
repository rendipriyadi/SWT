@extends('layouts.main')

@section('title', 'Update Report')

@section('content')
<div class="container-fluid px-4">
    <div class="page-header mb-4">
        <div>
            <h1 class="mb-2">Edit Report</h1>
            <p class="text-muted mb-0">Update report details and information</p>
        </div>
        <div>
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary" style="border: 2px solid #6c757d;">
                <i class="fas fa-arrow-left me-2"></i>{{ $backText }}
            </a>
        </div>
    </div>
    <div class="card p-4">
        <form action="{{ route('laporan.update', $laporan) }}" method="POST" enctype="multipart/form-data" id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="return_url" value="{{ $backUrl }}">

            <!-- Photos and Deadline side by side -->
            <div class="row g-4 mb-4">
                <!-- Photos Section -->
                <div class="col-md-6">
                    <label for="Foto" class="form-label fw-semibold">Add New Photos:</label>
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
                        <input type="text" class="form-control elegant-datepicker @error('tenggat_waktu') is-invalid @enderror" id="tenggat_waktu" name="tenggat_waktu" value="{{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->format('Y-m-d') }}" placeholder="Select date..." required>
                        <i class="fas fa-calendar-alt calendar-icon"></i>
                    </div>
                    @error('tenggat_waktu')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Existing Photos -->
            @if(!empty($laporan->Foto) && is_array($laporan->Foto))
            <div class="mb-4">
                <label class="form-label fw-semibold">Existing Photos:</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($laporan->Foto as $key => $foto)
                        <div class="position-relative">
                            <input type="hidden" name="existing_photos[]" value="{{ $foto }}" id="existing-photo-{{ $key }}">
                            <img src="{{ asset('storage/images/reports/' . $foto) }}" alt="Foto {{ $key+1 }}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-photo" data-input-id="existing-photo-{{ $key }}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Form Fields in Grid Layout -->
            <div class="row g-4">
                <!-- Area Dropdown -->
                <div class="col-md-6">
                    <label for="area_id" class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
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

                <!-- Station Dropdown -->
                <div class="col-md-6">
                    <label for="penanggung_jawab_id" class="form-label fw-semibold">Station <small class="text-muted">(Optional)</small></label>
                    <select class="form-select @error('penanggung_jawab_id') is-invalid @enderror" id="penanggung_jawab_id" name="penanggung_jawab_id" data-selected="{{ $laporan->penanggung_jawab_id }}">
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
                        <!-- Existing additional PICs -->
                        @if(!empty($additionalPics) && count($additionalPics) > 0)
                            @foreach($laporan->additional_pic_ids as $index => $picId)
                                @if(isset($additionalPics[$picId]))
                                    @php $pic = $additionalPics[$picId]; @endphp
                                    <div class="additional-pic-item mb-2" data-index="{{ $index + 1 }}">
                                        <div class="input-group">
                                            <select class="form-select additional-pic-select" name="additional_pics[{{ $index + 1 }}]" data-selected="{{ $picId }}">
                                                <option value="">Select Person in Charge</option>
                                                <!-- Options will be populated by JavaScript -->
                                            </select>
                                            <button type="button" class="btn btn-outline-danger remove-additional-pic">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
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

                <!-- Problem Description -->
                <div class="col-12">
                    <label for="deskripsi_masalah" class="form-label fw-semibold">Problem Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('deskripsi_masalah') is-invalid @enderror" id="deskripsi_masalah" name="deskripsi_masalah" rows="4" required placeholder="Describe the problem in detail...">{{ $laporan->deskripsi_masalah }}</textarea>
                    @error('deskripsi_masalah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Completion Section (Only for Completed Reports) -->
            @if($laporan->status === 'Completed' && $laporan->penyelesaian)
            <hr class="my-5">
            <h4 class="mb-4"><i class="fas fa-check-circle me-2 text-success"></i>Completion Details</h4>
            
            <!-- Two Column Layout: Completion Photos (Left) & Completion Date (Right) -->
            <div class="row g-4 mb-4">
                <!-- Completion Photos -->
                <div class="col-md-6">
                    <label for="completion_photos" class="form-label fw-semibold">Add New Completion Photos:</label>
                    <input type="file" class="form-control @error('completion_photos.*') is-invalid @enderror" 
                           id="completion_photos" name="completion_photos[]" accept="image/*" multiple>
                    @error('completion_photos.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="completion-foto-preview-container" class="mt-2 d-flex flex-wrap gap-2"></div>
                    <button type="button" class="btn btn-secondary mt-2" id="openCompletionCameraBtn">
                        <i class="fas fa-camera me-2"></i>Take Photo
                    </button>
                    <div id="completionCameraContainer" style="display:none; margin-top:10px;">
                        <video id="completionVideo" autoplay playsinline style="width:100%; max-width:350px; border:1px solid #ccc; border-radius:8px;"></video>
                        <canvas id="completionCanvas" style="display:none;"></canvas>
                        <div class="mt-2">
                            <button type="button" class="btn btn-success" id="captureCompletionBtn">
                                <i class="fas fa-camera me-1"></i>Take Photo
                            </button>
                            <button type="button" class="btn btn-danger" id="closeCompletionCameraBtn">
                                <i class="fas fa-times me-1"></i>Close Camera
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Completion Date -->
                <div class="col-md-6">
                    <label for="completion_date" class="form-label fw-semibold">Completion Date <span class="text-danger">*</span></label>
                    <div class="elegant-date-input">
                        <input type="text" class="form-control elegant-datepicker @error('completion_date') is-invalid @enderror" 
                               id="completion_date" name="completion_date" 
                               value="{{ old('completion_date', $laporan->penyelesaian->formatted_date ?? '') }}" 
                               placeholder="Select date..." required>
                        <i class="fas fa-calendar-alt calendar-icon"></i>
                    </div>
                    @error('completion_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Existing Completion Photos -->
            @if(!empty($laporan->penyelesaian->Foto) && is_array($laporan->penyelesaian->Foto))
            <div class="mb-4">
                <label class="form-label fw-semibold">Existing Completion Photos:</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($laporan->penyelesaian->Foto as $index => $foto)
                    <div class="position-relative">
                        <img src="{{ asset('storage/images/completions/' . $foto) }}" 
                             alt="Completion Photo {{ $index + 1 }}" 
                             class="img-thumbnail" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 delete-existing-completion-photo" 
                                data-photo="{{ $foto }}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="deleted_completion_photos" id="deleted_completion_photos" value="">
            </div>
            @endif

            <!-- Completion Description (Full Width Below) -->
            <div class="mb-3">
                <label for="completion_description" class="form-label fw-semibold">Completion Description <span class="text-danger">*</span></label>
                <textarea class="form-control @error('completion_description') is-invalid @enderror" 
                          id="completion_description" name="completion_description" rows="3" required 
                          placeholder="Describe how the problem was resolved...">{{ old('completion_description', $laporan->penyelesaian->deskripsi_penyelesaian) }}</textarea>
                @error('completion_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @endif

            <!-- Submit Button -->
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5" id="updateBtn">
                    <i class="fas fa-save me-2"></i>Update Report{{ $laporan->status === 'Completed' ? ' & Completion' : '' }}
                </button>
            </div>
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
            addFiles(newFiles, false); // false = append, tidak replace
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

    // Form update submission handling with proper loading state
    const updateForm = document.querySelector('form[action*="laporan"]');
    const updateBtn = document.getElementById('updateBtn');
    
    if (updateForm && updateBtn) {
        updateForm.addEventListener('submit', function(e) {
            // Remove empty additional PIC selects before submission
            const additionalPicSelects = document.querySelectorAll('.additional-pic-select');
            
            additionalPicSelects.forEach(select => {
                if (!select.value || select.value === '') {
                    // Remove the entire additional-pic-item if no selection made
                    const picItem = select.closest('.additional-pic-item');
                    if (picItem) {
                        picItem.remove();
                    }
                }
            });
            
            // Show loading state
            updateBtn.disabled = true;
            updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
            
            // Keep loading state active until page unloads (redirect happens)
            window.addEventListener('beforeunload', function() {
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
            });
            
            // Fallback: Re-enable after 30 seconds if something goes wrong
            setTimeout(() => {
                if (document.body.contains(updateBtn)) {
                    updateBtn.disabled = false;
                    updateBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Report';
                }
            }, 30000);
        });
    }

    // ============================================================================
    // Additional PIC Management for Edit Form
    // ============================================================================
    let additionalPicCount = {{ !empty($laporan->additional_pic_ids) ? count($laporan->additional_pic_ids) : 0 }};
    const additionalPicContainer = document.getElementById('additionalPicContainer');
    const addAdditionalPicBtn = document.getElementById('addAdditionalPic');
    
    /**
     * Get main PIC IDs dynamically from supervisor input
     * The supervisor input is populated by area-station.js based on area/station selection
     * Handles multiple supervisors separated by commas
     */
    function getMainPicIdsFromSupervisor() {
        const supervisorInput = document.getElementById('supervisor');
        if (!supervisorInput || !supervisorInput.value) {
            return [];
        }
        
        // Split by comma to handle multiple supervisors
        const supervisorNames = supervisorInput.value
            .split(',')
            .map(name => name.trim())
            .filter(name => name);
        
        // Find all PICs in allPenanggungJawab that match the supervisor names
        const mainPicIds = [];
        supervisorNames.forEach(supervisorName => {
            const mainPic = allPenanggungJawab.find(pj => 
                pj.name && pj.name.trim() === supervisorName
            );
            if (mainPic) {
                mainPicIds.push(mainPic.id.toString());
            }
        });
        
        return mainPicIds;
    }
    

    // Get all penanggung jawab from all areas
    let allPenanggungJawab = [];
    
    // Fetch all penanggung jawab from server
    fetch(window.routes.allPenanggungJawab)
        .then(response => response.json())
        .then(data => {
            allPenanggungJawab = data.penanggung_jawab || [];
            
            // Populate existing dropdowns
            populateExistingDropdowns();
        })
        .catch(error => {
            console.error('Error fetching penanggung jawab:', error);
            allPenanggungJawab = [];
        });

    // Populate existing dropdowns with options
    function populateExistingDropdowns() {
        const existingSelects = document.querySelectorAll('.additional-pic-select');
        existingSelects.forEach(select => {
            const currentValue = select.getAttribute('data-selected') || '';
            populateSelectOptions(select, currentValue);
        });
    }

    /**
     * Get filtered PIC options excluding main PICs and already selected Additional PICs
     * Same logic as Create Report (laporan.blade.php)
     */
    function getFilteredPicOptions(currentSelectValue = null) {
        const excludeMainPicIds = getMainPicIdsFromSupervisor();
        
        // Get already selected Additional PICs (exclude current select to allow keeping its value)
        const selectedAdditionalPics = Array.from(document.querySelectorAll('.additional-pic-select'))
            .map(select => select.value)
            .filter(val => val !== '' && val !== currentSelectValue);
        
        let picOptions = '<option value="">Select Person in Charge</option>';
        
        allPenanggungJawab.forEach(pj => {
            if (!pj || !pj.id || !pj.name) return;
            
            const pjId = pj.id.toString();
            
            // Exclude main PICs
            if (excludeMainPicIds.includes(pjId)) return;
            
            // Exclude already selected Additional PICs
            if (selectedAdditionalPics.includes(pjId)) return;
            
            const displayName = pj.name || 'Unknown';
            const displayStation = pj.station || 'No Station';
            const displayArea = pj.area_name || 'Unknown Area';
            
            picOptions += `<option value="${pj.id}">${displayName} - ${displayStation} (${displayArea})</option>`;
        });
        
        return picOptions;
    }
    
    /**
     * Populate select element with filtered PIC options
     * Restores previously selected value if still valid
     */
    function populateSelectOptions(selectElement, selectedValue = '') {
        const picOptions = getFilteredPicOptions(selectedValue);
        selectElement.innerHTML = picOptions;
        
        // Restore previous value if still valid
        if (selectedValue) {
            const optionExists = Array.from(selectElement.options).some(opt => opt.value === selectedValue);
            selectElement.value = optionExists ? selectedValue : '';
        }
    }

    /**
     * Add a new Additional PIC dropdown
     * Uses filtered options to exclude main PICs and already selected PICs
     */
    function addAdditionalPic() {
        if (allPenanggungJawab.length === 0) {
            alert('PIC data not loaded yet. Please wait a moment and try again.');
            return;
        }
        
        additionalPicCount++;
        
        // Use getFilteredPicOptions to get options with exclusions
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
        addAdditionalPicBtn.addEventListener('click', function() {
            addAdditionalPic();
        });
    }

    // Remove additional PIC button click (event delegation)
    if (additionalPicContainer) {
        additionalPicContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-additional-pic')) {
                const picItem = e.target.closest('.additional-pic-item');
                picItem.remove();
                // Refresh all dropdowns after removal
                refreshAllAdditionalPicDropdowns();
            }
        });
    }
    
    /**
     * Refresh all Additional PIC dropdowns with filtered options
     * Called when main PIC or any Additional PIC selection changes
     */
    function refreshAllAdditionalPicDropdowns() {
        const allSelects = document.querySelectorAll('.additional-pic-select');
        allSelects.forEach(select => {
            const currentValue = select.value;
            populateSelectOptions(select, currentValue);
        });
    }
    
    // Event listeners for dynamic dropdown refresh
    const areaSelect = document.getElementById('area_id');
    const stationSelect = document.getElementById('penanggung_jawab_id');
    
    // Refresh when area changes (supervisor name changes)
    if (areaSelect) {
        areaSelect.addEventListener('change', function() {
            setTimeout(() => refreshAllAdditionalPicDropdowns(), 100);
        });
    }
    
    // Refresh when station changes (supervisor name changes)
    if (stationSelect) {
        stationSelect.addEventListener('change', function() {
            setTimeout(() => refreshAllAdditionalPicDropdowns(), 100);
        });
    }
    
    // Refresh when any Additional PIC selection changes
    if (additionalPicContainer) {
        additionalPicContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('additional-pic-select')) {
                refreshAllAdditionalPicDropdowns();
            }
        });
    }

    // Prevent duplicate selection
    if (additionalPicContainer) {
        additionalPicContainer.addEventListener('change', function(e) {
            if (e.target.classList.contains('additional-pic-select')) {
                const selectedValue = e.target.value;
                const allSelects = document.querySelectorAll('.additional-pic-select');
                
                // Check for duplicates
                let duplicateFound = false;
                allSelects.forEach(select => {
                    if (select !== e.target && select.value === selectedValue && selectedValue !== '') {
                        duplicateFound = true;
                    }
                });
                
                if (duplicateFound) {
                    alert('This person is already selected. Please choose a different person.');
                    e.target.value = '';
                }
            }
        });
    }

    // Handle Completion Photos Preview and Camera
    const completionPhotosInput = document.getElementById('completion_photos');
    const completionPreviewContainer = document.getElementById('completion-foto-preview-container');
    const openCompletionCameraBtn = document.getElementById('openCompletionCameraBtn');
    const completionCameraContainer = document.getElementById('completionCameraContainer');
    const completionVideo = document.getElementById('completionVideo');
    const completionCanvas = document.getElementById('completionCanvas');
    const captureCompletionBtn = document.getElementById('captureCompletionBtn');
    const closeCompletionCameraBtn = document.getElementById('closeCompletionCameraBtn');
    
    let completionFileStore = [];
    let completionStream = null;
    
    if (completionPhotosInput && completionPreviewContainer) {
        completionPhotosInput.addEventListener('change', function(e) {
            const newFiles = Array.from(e.target.files);
            addCompletionFiles(newFiles, false);
        });

        if (openCompletionCameraBtn) {
            openCompletionCameraBtn.addEventListener('click', async function() {
                completionCameraContainer.style.display = 'block';
                openCompletionCameraBtn.style.display = 'none';
                
                try {
                    completionStream = await navigator.mediaDevices.getUserMedia({ 
                        video: { facingMode: "environment" } 
                    });
                } catch (e) {
                    try {
                        completionStream = await navigator.mediaDevices.getUserMedia({ 
                            video: true 
                        });
                    } catch (err) {
                        alert('Cannot access camera. Please ensure browser has camera permission.');
                        openCompletionCameraBtn.style.display = 'inline-block';
                        completionCameraContainer.style.display = 'none';
                    }
                }
                
                if (completionStream) {
                    completionVideo.srcObject = completionStream;
                }
            });
        }

        if (captureCompletionBtn) {
            captureCompletionBtn.addEventListener('click', function() {
                completionCanvas.width = completionVideo.videoWidth;
                completionCanvas.height = completionVideo.videoHeight;
                completionCanvas.getContext('2d').drawImage(completionVideo, 0, 0, completionCanvas.width, completionCanvas.height);
                completionCanvas.toBlob(function(blob) {
                    const newFile = new File([blob], `completion-camera-${Date.now()}.jpg`, { type: 'image/jpeg' });
                    addCompletionFiles([newFile]);
                }, 'image/jpeg', 0.95);
            });
        }

        if (closeCompletionCameraBtn) {
            closeCompletionCameraBtn.addEventListener('click', stopCompletionCamera);
        }

        function addCompletionFiles(newFiles, isReplacement = false) {
            let combined = isReplacement ? newFiles : [...completionFileStore, ...newFiles];
            completionFileStore = combined;
            updateCompletionFileInput();
            renderCompletionPreviews();
            if (openCompletionCameraBtn) openCompletionCameraBtn.style.display = 'inline-block';
        }

        function stopCompletionCamera() {
            if (completionStream) {
                completionStream.getTracks().forEach(track => track.stop());
                completionStream = null;
            }
            completionVideo.srcObject = null;
            completionCameraContainer.style.display = 'none';
            if (openCompletionCameraBtn) openCompletionCameraBtn.style.display = 'inline-block';
        }

        function renderCompletionPreviews() {
            completionPreviewContainer.innerHTML = '';
            completionFileStore.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'position-relative';
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="img-thumbnail" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 remove-completion-preview-btn" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    completionPreviewContainer.appendChild(preview);
                    
                    preview.querySelector('.remove-completion-preview-btn').addEventListener('click', function() {
                        const idx = this.getAttribute('data-index');
                        completionFileStore.splice(idx, 1);
                        updateCompletionFileInput();
                        renderCompletionPreviews();
                    });
                };
                reader.readAsDataURL(file);
            });
        }

        function updateCompletionFileInput() {
            const dataTransfer = new DataTransfer();
            completionFileStore.forEach(file => dataTransfer.items.add(file));
            completionPhotosInput.files = dataTransfer.files;
        }
    }

    // Handle Delete Existing Completion Photos
    const deletedCompletionPhotos = [];
    const deletedCompletionPhotosInput = document.getElementById('deleted_completion_photos');
    
    document.querySelectorAll('.delete-existing-completion-photo').forEach(btn => {
        btn.addEventListener('click', function() {
            const photo = this.getAttribute('data-photo');
            const photoContainer = this.closest('.position-relative');
            
            if (confirm('Are you sure you want to delete this completion photo?')) {
                deletedCompletionPhotos.push(photo);
                deletedCompletionPhotosInput.value = JSON.stringify(deletedCompletionPhotos);
                photoContainer.remove();
            }
        });
    });
});
</script>
@endpush