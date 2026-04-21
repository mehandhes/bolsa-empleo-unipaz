@extends('layouts.app')
@section('title', 'Panel Administrador')

@push('styles')
<style>
    .admin-layout { display: flex; min-height: calc(100vh - 92px); }
    .admin-sidebar {
        width: 220px;
        flex-shrink: 0;
        background: var(--unipaz-blue);
        padding: 1.25rem .6rem;
    }
    .admin-sidebar .sidebar-label {
        font-size: .63rem;
        font-weight: 700;
        letter-spacing: 1.3px;
        text-transform: uppercase;
        color: rgba(255,255,255,.3);
        padding: .5rem .75rem .25rem;
    }
    .admin-sidebar .nav-link {
        color: rgba(255,255,255,.75);
        border-radius: 8px;
        padding: .55rem .75rem;
        font-size: .875rem;
        font-weight: 400;
        margin: 1px 0;
        transition: all .15s;
        display: flex;
        align-items: center;
        gap: .5rem;
    }
    .admin-sidebar .nav-link i { width: 18px; font-size: .95rem; opacity: .8; }
    .admin-sidebar .nav-link:hover { background: rgba(255,255,255,.12); color: #fff; }
    .admin-sidebar .nav-link.active { background: var(--unipaz-green); color: #fff; font-weight: 600; }
    .admin-sidebar .nav-link.active i { opacity: 1; }

    .admin-content { flex: 1; min-width: 0; padding: 1.75rem 2rem; }
    @media (max-width: 991px) {
        .admin-layout { flex-direction: column; }
        .admin-sidebar { width: 100%; }
        .admin-content { padding: 1.25rem; }
    }

    .kpi-card {
        background: #fff;
        border-radius: 14px;
        border: none;
        box-shadow: 0 1px 8px rgba(0,0,0,.06);
        padding: 1.25rem 1.4rem;
        transition: box-shadow .2s, transform .2s;
    }
    .kpi-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.1); transform: translateY(-2px); }
    .kpi-card .kpi-icon {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .kpi-card .kpi-value { font-size: 1.9rem; font-weight: 800; line-height: 1; color: #1a1f36; }
    .kpi-card .kpi-label { font-size: .78rem; color: #6b7280; font-weight: 500; margin-top: .2rem; }

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
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .panel-header i { color: #273475; }
    .data-row {
        padding: .85rem 1.25rem;
        border-bottom: 1px solid #f9fafb;
        transition: background .12s;
    }
    .data-row:last-child { border-bottom: none; }
    .data-row:hover { background: #f8f9ff; }

    .progress-bar-unipaz { background: #273475; border-radius: 4px; }
    .progress { border-radius: 6px; background: #eef0f9; }

    .pending-badge {
        background: #fef3c7;
        color: #92400e;
        border-radius: 20px;
        font-size: .7rem;
        font-weight: 700;
        padding: .2rem .6rem;
    }
    .page-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: #1a1f36;
        margin-bottom: 0;
    }
    .page-title-bar {
        width: 36px; height: 3px;
        background: #00963F;
        border-radius: 2px;
        margin-top: .4rem;
        margin-bottom: 1.5rem;
    }
    .btn-sm-unipaz {
        background: #eef0f9;
        color: #273475;
        border: none;
        border-radius: 7px;
        font-size: .78rem;
        font-weight: 600;
        padding: .3rem .75rem;
        text-decoration: none;
        transition: background .15s;
    }
    .btn-sm-unipaz:hover { background: #273475; color: #fff; }
</style>
@endpush

@section('content')
<div class="admin-layout">

    {{-- ── Sidebar ── --}}
    <aside class="admin-sidebar">
        <div class="sidebar-label">Administración</div>
        <nav class="nav flex-column mt-1">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.companies') ? 'active' : '' }}"
               href="{{ route('admin.companies') }}">
                <i class="bi bi-building"></i> Empresas
                @if($pendingCompanies > 0)
                    <span class="pending-badge ms-auto">{{ $pendingCompanies }}</span>
                @endif
            </a>
            <a class="nav-link {{ request()->routeIs('admin.vacancies') ? 'active' : '' }}"
               href="{{ route('admin.vacancies') }}">
                <i class="bi bi-briefcase"></i> Vacantes
            </a>
            <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}"
               href="{{ route('admin.users') }}">
                <i class="bi bi-people"></i> Usuarios
            </a>
            <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}"
               href="{{ route('admin.reports') }}">
                <i class="bi bi-bar-chart-line"></i> Reportes
            </a>
        </nav>

        <div class="px-2 mt-4">
            <div style="background:rgba(255,255,255,.07); border-radius:10px; padding:.9rem; text-align:center;">
                <img src="{{ asset('images/LogoWhite_.png') }}"
                     alt="UNIPAZ"
                     style="height: 50px; width: auto; margin-bottom: .5rem;">
                <div style="font-size:.68rem; color:rgba(255,255,255,.35); letter-spacing:.3px;">
                    Panel de Administración
                </div>
            </div>
        </div>
    </aside>

    {{-- ── Contenido ── --}}
    <main class="admin-content">

        <div class="page-title">Panel de Administración</div>
        <div class="page-title-bar"></div>

        {{-- KPIs --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="kpi-card d-flex align-items-center gap-3"
                     style="border-left: 4px solid #273475;">
                    <div class="kpi-icon" style="background:#eef0f9; color:#273475;">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>
                    <div>
                        <div class="kpi-value">{{ number_format($totalStudents) }}</div>
                        <div class="kpi-label">Estudiantes registrados</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="kpi-card d-flex align-items-center gap-3"
                     style="border-left: 4px solid #00963F;">
                    <div class="kpi-icon" style="background:#e6f7ed; color:#00963F;">
                        <i class="bi bi-building-fill"></i>
                    </div>
                    <div>
                        <div class="kpi-value">{{ $totalCompanies }}</div>
                        <div class="kpi-label">
                            Empresas aprobadas
                            @if($pendingCompanies > 0)
                                <span class="pending-badge ms-1">{{ $pendingCompanies }} pend.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="kpi-card d-flex align-items-center gap-3"
                     style="border-left: 4px solid #f59e0b;">
                    <div class="kpi-icon" style="background:#fef3c7; color:#b45309;">
                        <i class="bi bi-briefcase-fill"></i>
                    </div>
                    <div>
                        <div class="kpi-value">{{ $totalVacantes }}</div>
                        <div class="kpi-label">Vacantes activas</div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="kpi-card d-flex align-items-center gap-3"
                     style="border-left: 4px solid #6366f1;">
                    <div class="kpi-icon" style="background:#ede9fe; color:#4f46e5;">
                        <i class="bi bi-send-fill"></i>
                    </div>
                    <div>
                        <div class="kpi-value">{{ number_format($totalPostulaciones) }}</div>
                        <div class="kpi-label">Total postulaciones</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- Empresas recientes --}}
            <div class="col-lg-6">
                <div class="panel-card h-100">
                    <div class="panel-header">
                        <span><i class="bi bi-building me-2"></i>Últimas empresas</span>
                        <a href="{{ route('admin.companies') }}" class="btn-sm-unipaz">Ver todas</a>
                    </div>
                    @foreach($recentCompanies as $company)
                        <div class="data-row d-flex justify-content-between align-items-center">
                            <div>
                                <div style="font-size:.875rem; font-weight:600; color:#1a1f36;">
                                    {{ $company->company_name }}
                                </div>
                                <div style="font-size:.77rem; color:#9ca3af;">{{ $company->sector }}</div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge {{ match($company->status) {
                                    'approved' => 'bg-success',
                                    'pending'  => 'bg-warning text-dark',
                                    'rejected' => 'bg-danger',
                                    default    => 'bg-secondary'
                                } }}" style="font-size:.7rem;">
                                    {{ ucfirst($company->status) }}
                                </span>
                                @if($company->status === 'pending')
                                    <form method="POST" action="{{ route('admin.companies.approve', $company) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm py-0 px-2"
                                                style="background:#e6f7ed; color:#00963F; border-radius:6px; font-weight:700; border:none;"
                                                title="Aprobar">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Últimas postulaciones --}}
            <div class="col-lg-6">
                <div class="panel-card h-100">
                    <div class="panel-header">
                        <span><i class="bi bi-clock-history me-2"></i>Últimas postulaciones</span>
                    </div>
                    @foreach($recentApplications as $app)
                        <div class="data-row d-flex justify-content-between align-items-center">
                            <div>
                                <div style="font-size:.875rem; font-weight:600; color:#1a1f36;">
                                    {{ Str::limit($app->user->name, 28) }}
                                </div>
                                <div style="font-size:.77rem; color:#9ca3af;">
                                    <i class="bi bi-arrow-right me-1"></i>{{ Str::limit($app->jobPosting->title, 32) }}
                                </div>
                            </div>
                            <span class="badge {{ $app->status_badge }}" style="font-size:.7rem;">
                                {{ $app->status_label }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Áreas con más vacantes --}}
            <div class="col-12">
                <div class="panel-card">
                    <div class="panel-header">
                        <span><i class="bi bi-bar-chart-line me-2"></i>Áreas con más vacantes activas</span>
                    </div>
                    <div class="p-4">
                        @foreach($topAreas as $area)
                            @php $pct = ($area->total / ($topAreas->first()->total ?: 1)) * 100; @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span style="font-size:.85rem; font-weight:500; color:#374151;">{{ $area->area }}</span>
                                    <span style="font-size:.82rem; font-weight:700; color:#273475;">{{ $area->total }}
                                        {{ Str::plural('vacante', $area->total) }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar-unipaz" role="progressbar"
                                         style="width: {{ $pct }}%; height:8px;"
                                         aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </main>

</div>
@endsection
