@extends('layouts.app')
@section('title', 'Gestión de Vacantes')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-briefcase text-primary me-2"></i>Gestión de Vacantes</h4>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Dashboard
        </a>
    </div>

    {{-- Mensajes flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros -->
    <form method="GET" class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-2">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Buscar por título o empresa..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos los estados</option>
                        <option value="active" @selected(request('status') === 'active')>Activas</option>
                        <option value="paused" @selected(request('status') === 'paused')>Pausadas</option>
                        <option value="closed" @selected(request('status') === 'closed')>Cerradas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="area" class="form-select form-select-sm">
                        <option value="">Todas las áreas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area }}" @selected(request('area') === $area)>{{ $area }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filtrar</button>
                </div>
                @if(request()->hasAny(['search', 'status', 'area']))
                    <div class="col-auto">
                        <a href="{{ route('admin.vacancies') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-lg me-1"></i>Limpiar
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Vacante</th>
                        <th>Empresa</th>
                        <th>Área</th>
                        <th>Modalidad</th>
                        <th>Postulaciones</th>
                        <th>Fecha límite</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vacancies as $vacancy)
                        <tr>
                            <td>
                                <div>
                                    <p class="mb-0 fw-semibold small">{{ Str::limit($vacancy->title, 35) }}</p>
                                    <small class="text-muted">{{ $vacancy->contract_type }} &middot; {{ $vacancy->positions }} {{ Str::plural('plaza', $vacancy->positions) }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $vacancy->company->logo_url }}" class="rounded" width="28" height="28" style="object-fit:cover;">
                                    <small>{{ Str::limit($vacancy->company->company_name, 25) }}</small>
                                </div>
                            </td>
                            <td><small>{{ $vacancy->area }}</small></td>
                            <td>
                                <small>
                                    <i class="bi bi-{{ match($vacancy->modality) { 'remote' => 'wifi', 'hybrid' => 'shuffle', default => 'building' } }} me-1"></i>
                                    {{ match($vacancy->modality) { 'onsite' => 'Presencial', 'remote' => 'Remoto', 'hybrid' => 'Híbrido', default => $vacancy->modality } }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark" style="font-size:.78rem;">
                                    <i class="bi bi-people me-1"></i>{{ $vacancy->applications_count }}
                                </span>
                            </td>
                            <td>
                                @php $isExpired = $vacancy->deadline->isPast(); @endphp
                                <small class="{{ $isExpired ? 'text-danger' : '' }}">
                                    {{ $vacancy->deadline->format('d/m/Y') }}
                                    @if($isExpired)
                                        <i class="bi bi-exclamation-circle ms-1" title="Expirada"></i>
                                    @endif
                                </small>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($vacancy->status) {
                                        'active' => 'bg-success',
                                        'paused' => 'bg-warning text-dark',
                                        'closed' => 'bg-danger',
                                        default  => 'bg-secondary',
                                    };
                                    $badgeLabel = match($vacancy->status) {
                                        'active' => 'Activa',
                                        'paused' => 'Pausada',
                                        'closed' => 'Cerrada',
                                        default  => $vacancy->status,
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}" style="font-size:.72rem;">{{ $badgeLabel }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                <a href="{{ route('admin.vacancies.show', $vacancy) }}" class="btn btn-outline-primary btn-sm py-0 px-2" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($vacancy->status !== 'closed')
                                    <form method="POST" action="{{ route('admin.vacancies.toggle', $vacancy) }}">
                                        @csrf
                                        @if($vacancy->status === 'active')
                                            <button class="btn btn-outline-warning btn-sm py-0 px-2" title="Pausar vacante"
                                                    onclick="return confirm('¿Pausar esta vacante?')">
                                                <i class="bi bi-pause-fill"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-outline-success btn-sm py-0 px-2" title="Activar vacante"
                                                    onclick="return confirm('¿Activar esta vacante?')">
                                                <i class="bi bi-play-fill"></i>
                                            </button>
                                        @endif
                                    </form>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-briefcase d-block fs-3 mb-2 opacity-25"></i>
                                No se encontraron vacante