@extends('layouts.app')
@section('title', 'Vacante: ' . $jobPosting->title)

@push('styles')
<style>
    .detail-banner {
        background: linear-gradient(135deg, #273475 0%, #1d2659 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .detail-banner::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        background: rgba(0,150,63,.12);
        border-radius: 50%;
    }
    .detail-banner > * { position: relative; z-index: 1; }

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
    .panel-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f0f2fb;
        font-weight: 700;
        font-size: .88rem;
        color: #1a1f36;
    }
    .panel-header i { color: #273475; }

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

    .company-logo {
        width: 52px; height: 52px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid rgba(255,255,255,.2);
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
    .btn-action-back { background: rgba(255,255,255,.15); color: #fff; }
    .btn-action-back:hover { background: rgba(255,255,255,.25); color: #fff; }
    .btn-action-toggle { background: rgba(255,255,255,.15); color: #fff; border: none; cursor: pointer; }
    .btn-action-toggle:hover { background: rgba(255,255,255,.25); color: #fff; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- Banner --}}
    <div class="detail-banner mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $jobPosting->company->logo_url }}" class="company-logo" alt="logo">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
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
                        {{ $jobPosting->company->company_name }}
                        &middot; Publicada el {{ $jobPosting->created_at->format('d/m/Y') }}
                        &middot; {{ $jobPosting->applications_count }} {{ Str::plural('postulación', $jobPosting->applications_count) }}
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                @if($jobPosting->status !== 'closed')
                    <form method="POST" action="{{ route('admin.vacancies.toggle', $jobPosting) }}">
                        @csrf
                        <button type="submit" class="btn-action btn-action-toggle"
                                onclick="return confirm('¿{{ $jobPosting->status === 'active' ? 'Pausar' : 'Activar' }} esta vacante?')">
                            <i class="bi bi-{{ $jobPosting->status === 'active' ? 'pause-fill' : 'play-fill' }}"></i>
                            {{ $jobPosting->status === 'active' ? 'Pausar' : 'Activar' }}
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.vacancies') }}" class="btn-action btn-action-back">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Columna principal --}}
        <div class="col-lg-8">
            {{-- Info rápida --}}
            <div class="info-grid mb-4">
                <div class="row g-0">
                    <div class="col-6 col-md-2 info-item">
                        <i class="bi bi-geo-alt text-primary"></i>
                        <div class="info-value">{{ $jobPosting->location }}</div>
                        <div class="info-label">Ubicación</div>
                    </div>
                    <div class="col-6 col-md-2 info-item">
                        <i class="bi bi-cash-stack text-success"></i>
                        <div class="info-value">{{ $jobPosting->salary_label }}</div>
                        <div class="info-label">Salario</div>
                    </div>
                    <div class="col-6 col-md-2 info-item">
                        <i class="bi bi-people text-info"></i>
                        <div class="info-value">{{ $jobPosting->positions }}</div>
                        <div class="info-label">Plazas</div>
                    </div>
                    <div class="col-6 col-md-2 info-item">
                        <i class="bi bi-calendar-x {{ $jobPosting->isExpired() ? 'text-danger' : 'text-warning' }}"></i>
                        <div class="info-value">{{ $jobPosting->deadline->format('d/m/Y') }}</div>
                        <div class="info-label">{{ $jobPosting->isExpired() ? 'Expirada' : $jobPosting->remaining_days . ' días' }}</div>
                    </div>
                    <div class="col-6 col-md-2 info-item">
                        <i class="bi bi-tag text-primary"></i>
                        <div class="info-value" style="font-size:.78rem;">{{ $jobPosting->area }}</div>
                        <div class="info-label">Área</div>
                    </div>
                    <div class="col-6 col-md-2 info-item">
                        <i class="bi bi-{{ match($jobPosting->modality) { 'remote' => 'wifi', 'hybrid' => 'shuffle', default => 'building' } }} text-secondary"></i>
                        <div class="info-value" style="font-size:.78rem;">{{ match($jobPosting->modality) { 'onsite' => 'Presencial', 'remote' => 'Remoto', 'hybrid' => 'Híbrido', default => $jobPosting->modality } }}</div>
                        <div class="info-label">Modalidad</div>
                    </div>
                </div>
            </div>

            {{-- Contenido --}}
            <div class="panel-card p-4 mb-4">
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="badge bg-secondary">{{ $jobPosting->contract_type }}</span>
                    @if($jobPosting->requires_cv)
                        <span class="badge" style="background:#f3f4f6; color:#374151;">
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

            {{-- Datos de la empresa --}}
            <div class="panel-card p-4">
                <h6 class="section-title"><i class="bi bi-building me-2 text-primary"></i>Información de la empresa</h6>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $jobPosting->company->logo_url }}" class="rounded" width="48" height="48" style="object-fit:cover; border:1px solid #eef0f9;">
                    <div>
                        <div style="font-weight:700; color:#1a1f36;">{{ $jobPosting->company->company_name }}</div>
                        <div style="font-size:.82rem; color:#6b7280;">{{ $jobPosting->company->sector }}</div>
                    </div>
                </div>
                <div class="row g-2" style="font-size:.84rem; color:#4b5563;">
                    @if($jobPosting->company->nit)
                        <div class="col-md-6"><i class="bi bi-hash me-1 text-muted"></i><strong>NIT:</strong> {{ $jobPosting->company->nit }}</div>
                    @endif
                    @if($jobPosting->company->contact_person)
                        <div class="col-md-6"><i class="bi bi-person me-1 text-muted"></i><strong>Contacto:</strong> {{ $jobPosting->company->contact_person }}</div>
                    @endif
                    @if($jobPosting->company->phone)
                        <div class="col-md-6"><i class="bi bi-telephone me-1 text-muted"></i><strong>Teléfono:</strong> {{ $jobPosting->company->phone }}</div>
                    @endif
                    @if($jobPosting->company->address)
                        <div class="col-md-6"><i class="bi bi-geo-alt me-1 text-muted"></i><strong>Dirección:</strong> {{ $jobPosting->company->address }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Columna lateral — Postulantes --}}
        <div class="col-lg-4">
            <div class="panel-card sticky-top" style="top: 80px;">
                <div class="panel-header">
                    <i class="bi bi-people me-2"></i>Postulantes
                    <span class="badge bg-primary ms-1" style="font-size:.72rem;">{{ $jobPosting->applications_count }}</span>
                </div>

                @forelse($jobPosting->applications as $app)
                    <div class="applicant-row">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $app->user->avatar_url }}" class="student-avatar" alt="avatar">
                                <div>
                                    <div style="font-size:.84rem; font-weight:600; color:#1a1f36;">
                                        {{ Str::limit($app->user->name, 22) }}
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
                        @if($app->cover_letter)
                            <div class="mt-2 p-2 rounded" style="background:#f8f9ff; font-size:.78rem; color:#6b7280; border:1px solid #eef0f9;">
                                <i class="bi bi-chat-quote me-1"></i>{{ Str::limit($app->cover_letter, 120) }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-4" style="color:#9ca3af;">
                        <i class="bi bi-inbox d-block fs-3 mb-2 opacity-25"></i>
                        <p class="small mb-0">Aún no hay postulantes.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
