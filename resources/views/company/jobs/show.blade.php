@extends('layouts.app')
@section('title', $jobPosting->title)

@push('styles')
<style>
    .detail-header {
        background: linear-gradient(135deg, #273475 0%, #1d2659 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .detail-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        background: rgba(0,150,63,.12);
        border-radius: 50%;
    }
    .detail-header > * { position: relative; z-index: 1; }

    .info-grid {
        background: #f8f9ff;
        border: 1px solid #eef0f9;
        border-radius: 14px;
        padding: 1.25rem;
    }
    .info-item {
        text-align: center;
        padding: .75rem .5rem;
    }
    .info-item i { font-size: 1.4rem; margin-bottom: .3rem; display: block; }
    .info-item .info-value { font-size: .88rem; font-weight: 700; color: #1a1f36; }
    .info-item .info-label { font-size: .72rem; color: #9ca3af; }

    .section-title {
        font-size: .95rem;
        font-weight: 700;
        color: #1a1f36;
        border-bottom: 2px solid #eef0f9;
        padding-bottom: .5rem;
        margin-bottom: .75rem;
    }

    .panel-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #eef0f9;
        box-shadow: 0 1px 6px rgba(0,0,0,.05);
        overflow: hidden;
    }

    .applicant-row {
        padding: .75rem 1.25rem;
        border-bottom: 1px solid #f5f5f7;
        transition: background .12s;
    }
    .applicant-row:last-child { border-bottom: none; }
    .applicant-row:hover { background: #f8f9ff; }

    .student-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid #eef0f9;
    }

    .badge-active   { background: #dcfce7; color: #166534; font-weight: 600; }
    .badge-paused   { background: #fef3c7; color: #92400e; font-weight: 600; }
    .badge-closed   { background: #fee2e2; color: #991b1b; font-weight: 600; }
    .badge-expired  { background: #f3f4f6; color: #6b7280; font-weight: 600; }

    .btn-action {
        border: none;
        border-radius: 8px;
        padding: .45rem .85rem;
        font-size: .82rem;
        font-weight: 600;
        transition: all .15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
    }
    .btn-action-edit { background: #eef0f9; color: #273475; }
    .btn-action-edit:hover { background: #273475; color: #fff; }
    .btn-action-back { background: #f3f4f6; color: #6b7280; }
    .btn-action-back:hover { background: #6b7280; color: #fff; }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- Encabezado --}}
    <div class="detail-header mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h4 class="fw-bold mb-0">{{ $jobPosting->title }}</h4>
                    @php
                        $badgeClass = match($jobPosting->status) {
                            'active' => $jobPosting->isExpired() ? 'badge-expired' : 'badge-active',
                            'paused' => 'badge-paused',
                            'closed' => 'badge-closed',
                            default  => 'badge-expired',
                        };
                        $badgeLabel = match($jobPosting->status) {
                            'active' => $jobPosting->isExpired() ? 'Expirada' : 'Activa',
                            'paused' => 'Pausada',
                            'closed' => 'Cerrada',
                            default  => $jobPosting->status,
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                </div>
                <p class="mb-0" style="font-size:.85rem; color:rgba(255,255,255,.6);">
                    Publicada el {{ $jobPosting->created_at->format('d/m/Y') }}
                    &middot; {{ $jobPosting->applications_count }} {{ Str::plural('postulación', $jobPosting->applications_count) }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('company.jobs.edit', $jobPosting) }}" class="btn-action btn-action-edit">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <a href="{{ route('company.jobs.index') }}" class="btn-action btn-action-back">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Columna principal --}}
        <div class="col-lg-8">
            {{-- Información rápida --}}
            <div class="info-grid mb-4">
                <div class="row g-0">
                    <div class="col-6 col-md-3 info-item">
                        <i class="bi bi-geo-alt text-primary"></i>
                        <div class="info-value">{{ $jobPosting->location }}</div>
                        <div class="info-label">Ubicación</div>
                    </div>
                    <div class="col-6 col-md-3 info-item">
                        <i class="bi bi-cash-stack text-success"></i>
                        <div class="info-value">{{ $jobPosting->salary_label }}</div>
                        <div class="info-label">Salario</div>
                    </div>
                    <div class="col-6 col-md-3 info-item">
                        <i class="bi bi-people text-info"></i>
                        <div class="info-value">{{ $jobPosting->positions }} {{ Str::plural('plaza', $jobPosting->positions) }}</div>
                        <div class="info-label">Vacantes</div>
                    </div>
                    <div class="col-6 col-md-3 info-item">
                        <i class="bi bi-calendar-x {{ $jobPosting->isExpired() ? 'text-danger' : 'text-warning' }}"></i>
                        <div class="info-value">{{ $jobPosting->deadline->format('d/m/Y') }}</div>
                        <div class="info-label">{{ $jobPosting->isExpired() ? 'Expirada' : $jobPosting->remaining_days . ' días restantes' }}</div>
                    </div>
                </div>
            </div>

            {{-- Detalles --}}
            <div class="panel-card p-4 mb-4">
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="badge bg-primary">{{ $jobPosting->area }}</span>
                    <span class="badge {{ $jobPosting->modality_badge }} text-white">
                        {{ match($jobPosting->modality) { 'onsite' => 'Presencial', 'remote' => 'Remoto', 'hybrid' => 'Híbrido', default => $jobPosting->modality } }}
                    </span>
                    <span class="badge bg-secondary">{{ $jobPosting->contract_type }}</span>
                    @if($jobPosting->requires_cv)
                        <span class="badge bg-outline-dark" style="background:#f3f4f6; color:#374151;">
                            <i class="bi bi-file-pdf me-1"></i>Requiere CV
                        </span>
                    @endif
                </div>

                <h6 class="section-title"><i class="bi bi-file-text me-2 text-primary"></i>Descripción del cargo</h6>
                <p style="color:#4b5563; font-size:.9rem; line-height:1.7;">{!! nl2br(e($jobPosting->description)) !!}</p>

                @if($jobPosting->responsibilities)
                    <h6 class="section-title mt-4"><i class="bi bi-list-check me-2 text-success"></i>Responsabilidades</h6>
                    <p style="color:#4b5563; font-size:.9rem; line-height:1.7;">{!! nl2br(e($jobPosting->responsibilities)) !!}</p>
                @endif

                <h6 class="section-title mt-4"><i class="bi bi-patch-check me-2 text-info"></i>Requisitos</h6>
                <p style="color:#4b5563; font-size:.9rem; line-height:1.7;">{!! nl2br(e($jobPosting->requirements)) !!}</p>

                @if($jobPosting->programs_targeted)
                    <div class="alert alert-info mt-3 mb-0" style="border-radius:10px;">
                        <i class="bi bi-mortarboard me-2"></i>
                        <strong>Programas académicos preferidos:</strong> {{ $jobPosting->programs_targeted }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Columna lateral — Postulantes --}}
        <div class="col-lg-4">
            <div class="panel-card sticky-top" style="top: 80px;">
                <div style="padding:1rem 1.25rem; border-bottom:1px solid #f0f2fb; font-weight:700; font-size:.88rem; color:#1a1f36;">
                    <i class="bi bi-people me-2" style="color:#273475;"></i>Postulantes
                    <span class="badge bg-primary ms-1" style="font-size:.72rem;">{{ $jobPosting->applications_count }}</span>
                </div>

                @forelse($jobPosting->applications as $app)
                    <div class="applicant-row d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $app->user->avatar_url }}" class="student-avatar" alt="avatar">
                            <div>
                                <div style="font-size:.84rem; font-weight:600; color:#1a1f36;">
                                    {{ Str::limit($app->user->name, 20) }}
                                </div>
                                <div style="font-size:.72rem; color:#9ca3af;">
                                    {{ $app->user->studentProfile?->program ?? $app->user->email }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            @if($app->cv_path)
                                <a href="{{ Storage::url($app->cv_path) }}" target="_blank"
                                   style="background:#fee2e2; color:#b91c1c; border:none; border-radius:6px; padding:.2rem .45rem; font-size:.75rem;"
                                   title="Ver CV">
                                    <i class="bi bi-file-pdf"></i>
                                </a>
                            @endif
                            <span class="badge {{ $app->status_badge }}" style="font-size:.68rem;">
                                {{ $app->status_label }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4" style="color:#9ca3af;">
                        <i class="bi bi-inbox d-block fs-3 mb-2 opacity-25"></i>
                        <p class="small mb-0">Aún no hay postulantes.</p>
                    </div>
                @endforelse

                @if($jobPosting->applications_count > 0)
                    <div style="padding:.75rem 1.25rem; border-top:1px solid #f0f2fb; text-align:center;">
                        <a href="{{ route('company.applications') }}" style="font-size:.82rem; font-weight:600; color:#273475; text-decoration:none;">
                            Ver todas las postulaciones <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
