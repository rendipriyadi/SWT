@extends('layouts.main')

@section('title', 'Report Detail')

@section('content')
<div class="container-fluid px-4">
    <div class="page-header mb-4 position-relative">
        <div>
            <h1 class="mb-2">Report Detail</h1>
            <p class="text-muted mb-0">View complete report information</p>
        </div>
        <!-- Back Button (Top Right Corner) -->
        @php
            $backUrl = $laporan->status === 'Completed' ? route('sejarah.index') : route('laporan.index');
        @endphp
        <a href="{{ $backUrl }}" class="btn btn-outline-secondary position-absolute" style="top: 0; right: 0; border: 2px solid #6c757d;">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    <!-- Report Information Card -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Report Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Report Date:</label>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($laporan->created_at)->locale('en')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Status:</label>
                        <p class="mb-0">
                            @if($laporan->status == 'Assigned')
                                <span class="badge bg-info"><i class="fas fa-circle me-1"></i>Assigned</span>
                            @elseif($laporan->status == 'Completed')
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Completed</span>
                            @else
                                <span class="badge bg-secondary">{{ $laporan->status }}</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Area:</label>
                        <p class="mb-0">{{ $laporan->area->name ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Station:</label>
                        <p class="mb-0">{{ $laporan->penanggungJawab->station ?? '-' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Person in Charge:</label>
                        <p class="mb-0">
                            @if($laporan->penanggungJawab)
                                {{ $laporan->penanggungJawab->name }}
                            @elseif($laporan->area && $laporan->area->penanggungJawabs && $laporan->area->penanggungJawabs->count() > 0)
                                {{ $laporan->area->penanggungJawabs->pluck('name')->join(', ') }}
                            @else
                                Not assigned
                            @endif
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Problem Category:</label>
                        <p class="mb-0">
                            @if($laporan->problemCategory)
                                <span class="badge" style="background-color: {{ $laporan->problemCategory->color }}; color: white;">
                                    {{ $laporan->problemCategory->name }}
                                </span>
                            @else
                                <span class="text-muted">No Category</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Report Photos (right side of Problem Category) -->
                @if(!empty($laporan->Foto) && is_array($laporan->Foto) && count($laporan->Foto) > 0)
                <div class="col-md-6">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Report Photos:</label>
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            @foreach($laporan->Foto as $index => $foto)
                                <img src="{{ asset('images/reports/' . $foto) }}" 
                                     alt="Report Photo {{ $index + 1 }}" 
                                     class="img-thumbnail report-photo-thumb" 
                                     style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#photoModal{{ $index }}">
                                
                                <!-- Photo Modal -->
                                <div class="modal fade" id="photoModal{{ $index }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Report Photo {{ $index + 1 }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="{{ asset('images/reports/' . $foto) }}" 
                                                     alt="Report Photo {{ $index + 1 }}" 
                                                     class="img-fluid">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <div class="col-md-6">
                    <!-- Empty space if no photos -->
                </div>
                @endif
                
                <div class="col-md-6">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Deadline:</label>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($laporan->tenggat_waktu)->locale('en')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Problem Description:</label>
                        <p class="mb-0 text-pre-wrap">{{ $laporan->deskripsi_masalah }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons (Bottom Left) -->
    @if($laporan->status != 'Completed')
    <div class="mb-4">
        <div class="d-flex gap-2">
            <a href="{{ route('laporan.edit', encrypt($laporan->id)) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit Report
            </a>
            <a href="{{ route('laporan.tindakan', encrypt($laporan->id)) }}" class="btn btn-primary">
                <i class="fas fa-tasks me-2"></i>Complete Report
            </a>
        </div>
    </div>
    @endif

    <!-- Completion Details (if completed) -->
    @if($laporan->status == 'Completed' && $laporan->penyelesaian)
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Completion Details</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Completion Date:</label>
                        <p class="mb-0">{{ \Carbon\Carbon::parse($laporan->penyelesaian->Tanggal)->locale('en')->isoFormat('dddd, D MMMM YYYY') }}</p>
                    </div>
                </div>
                <div class="col-12">
                    <div class="detail-item">
                        <label class="fw-bold text-muted">Completion Description:</label>
                        <p class="mb-0 text-pre-wrap">{{ $laporan->penyelesaian->deskripsi_penyelesaian }}</p>
                    </div>
                </div>
            </div>

            <!-- Completion Photos -->
            @if(!empty($laporan->penyelesaian->Foto) && is_array($laporan->penyelesaian->Foto) && count($laporan->penyelesaian->Foto) > 0)
            <div class="mt-4">
                <label class="fw-bold text-muted mb-3">Completion Photos:</label>
                <div class="row g-3">
                    @foreach($laporan->penyelesaian->Foto as $index => $foto)
                    <div class="col-md-3 col-sm-4 col-6">
                        <img src="{{ asset('images/completions/' . $foto) }}" 
                             alt="Completion Photo {{ $index + 1 }}" 
                             class="img-thumbnail" 
                             style="width: 100%; height: 200px; object-fit: cover; cursor: pointer;"
                             data-bs-toggle="modal" 
                             data-bs-target="#completionPhotoModal{{ $index }}">
                    </div>

                    <!-- Completion Photo Modal -->
                    <div class="modal fade" id="completionPhotoModal{{ $index }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Completion Photo {{ $index + 1 }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="{{ asset('images/completions/' . $foto) }}" 
                                         alt="Completion Photo {{ $index + 1 }}" 
                                         class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
.detail-item {
    padding: 0.75rem 0;
}
.detail-item label {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    display: block;
}
.detail-item p {
    font-size: 1rem;
    color: var(--text-primary);
}
.text-pre-wrap {
    white-space: pre-wrap;
    word-wrap: break-word;
}
.card-header {
    font-weight: 600;
}
.report-photo-thumb {
    transition: transform 0.2s, box-shadow 0.2s;
}
.report-photo-thumb:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
</style>
@endpush
@endsection
