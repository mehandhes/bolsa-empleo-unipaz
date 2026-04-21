@extends('layouts.app')
@section('title', 'Mis Vacantes')

@push('styles')
<style>
    .vacancy-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #eef0f9;
        box-shadow: 0 1px 6px rgba(0,0,0,.05);
        padding: 1.25rem 1.5rem;
        transition: box-shadow .2s, transform .2s;
    }
    .vacancy-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.1); transform: translateY(-2px); }

    .badge-active   { background: #dcfce7; color: #166534; font-weight: 600; font-size: .72rem; }
    .badge-paused   { background: #fef3c7; color: #92400e; font-weight: 600; font-size: .72rem; }
    .badge-closed   { background: #fee2e2; color: #991b1b; font-weight: 600; font-size: .72rem; }
    .badge-expired  { background: #f3f4f6; color: #6b7280; font-weight: 600; font-size: .72rem; }

    .vacancy-meta { font-size: .8rem; color: #6b7280; }
    .vacancy-meta i { width: 16px; color: #9ca3af; }

    .btn-action {
        border: none;
        border-radius: 8px;
        padding: .4rem .7rem;
        font-size: .82rem;
        font-weight: 600;
        transition: all .15s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
    }
    .btn-action-edit { background: #eef0f9; color: #273475; }
    .btn-action-edit:hover { background: #273475; color: #fff; }
    .btn-action-close { background: #fee2e2; color: #b91c1c; }
    .btn-action-close:hover { background: #b91c1c; color: #fff; }

    .page-header {
        background: linear-gradient(135deg, #273475 0%, #1d2659 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        color: #fff;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- Mensajes flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Encabezado --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-briefcase-fill me-2"></i>Mis Vacantes</h4>
            <p class="mb-0" style="font-size:.85rem; color:rgba(255,255,255,.6);">
                {{ $jobPostings->total() }} {{ Str::plural('vacante', $jobPostings->total()) }} publicadas
            </p>
        </div>
        @if($company->isApproved())
            <a href="{{ route('company.jobs.create') }}"
               style="background:#00963F; color:#fff; border:none; border-radius:10px; padding:.6rem 1.2rem; font-weight:700; font-size:.88rem; text-decoration:none; display:inline-flex; align-items:center; gap:.5rem;"
               onmouseover="this.style.background='#007832'" onmouseout="this.style.background='#00963F'">
                <i class="bi bi-plus-circle-fill"></i>Nueva vacante
            </a>
        @endif
    </div>

    {{-- Lista de vacantes --}}
    @forelse($jobPostings as $job)
        <div class="vacancy-card mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h6 class="fw-bold mb-0" style="color:#1a1f36;">{{ $job->title }}</h6>
                        @php
                            $badgeClass = match($job->status) {
                                'active' => $job->isExpired() ? 'badge-expired' : 'badge-active',
                                'paused' => 'badge-paused',
                                'closed' => 'badge-closed',
                                default  => 'badge-expired',
                            };
                            $badgeLabel = match($job->status) {
                                'active' => $job->isExpired() ? 'Expirada' : 'Activa',
                                'paused' => 'Pausada',
                                'closed' => 'Cerrada',
                                default  => $job->status,
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                    </div>

                    <div class="d-flex flex-wrap gap-3 vacancy-meta">
                        <span><i class="bi bi-geo-alt me-1"></i>{{ $job->location }}</span>
                        <span><i class="bi bi-tag me-1"></i>{{ $job->area }}</span>
                        <span><i class="bi bi-file-text me-1"></i>{{ $job->contract_type }}</span>
                        <span>
                            <i class="bi bi-{{ match($job->modality) { 'remote' => 'wifi', 'hybrid' => 'shuffle', default => 'building' } }} me-1"></i>
                            {{ match($job->modality) { 'onsite' => 'Presencial', 'remote' => 'Remoto', 'hybrid' => 'Híbrido', default => $job->modality } }}
                        </span>
                        <span><i class="bi bi-calendar-event me-1"></i>Cierra {{ $job->deadline->format('d/m/Y') }}</span>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-2" style="font-size:.82rem;">
                        <span style="color:#273475; font-weight:600;">
                            <i class="bi bi-people-fill me-1"></i>{{ $job->applications_count }} {{ Str::plural('postulación', $job->applications_count) }}
                        </span>
                        <span style="color:#6b7280;">
                            <i class="bi bi-hash me-1"></i>{{ $job->positions }} {{ Str::plural('vacante', $job->positions) }}
                        </span>
                        @if($job->salary_negotiable)
                            <span style="color:#6b7280;"><i class="bi bi-cash me-1"></i>A convenir</span>
                        @elseif($job->salary_range)
                            <span style="color:#6b7280;"><i class="bi bi-cash me-1"></i>{{ $job->salary_range }}</span>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2 flex-shrink-0">
                    <a href="{{ route('company.jobs.show', $job) }}" class="btn-action" style="background:#e0f2fe; color:#0369a1;">
                        <i class="bi bi-eye"></i> Ver
                    </a>
                    <a href="{{ route('company.jobs.edit', $job) }}" class="btn-action btn-action-edit">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    @if($job->status !== 'closed')
                        <form method="POST" action="{{ route('company.jobs.destroy', $job) }}"
                              onsubmit="return confirm('¿Cerrar esta vacante? Los postulantes existentes no se verán afectados.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-action btn-action-close">
                                <i class="bi bi-x-circle"></i> Cerrar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5" style="color:#9ca3af;">
            <i class="bi bi-briefcase d-block fs-1 mb-3 opacity-25"></i>
            <p class="mb-2 fw-semibold" style="color:#6b7280;">Aún no has publicado vacantes</p>
            @if($company->isApproved())
                <a href="{{ route('company.jobs.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Publicar tu primera vacante
                </a>
            @else
                <p class="small text-muted">Tu empresa debe ser aprobada para publicar vacantes.</p>
            @endif
        </div>
    @endf