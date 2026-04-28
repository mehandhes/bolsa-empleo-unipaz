@extends('layouts.app')
@section('title', 'Inicio')

@push('styles')
<style>
    /* ═══════════════════════════════════════
       LANDING PAGE — EMPLEA UNIPAZ
       Tema oscuro premium con acento esmeralda
    ═══════════════════════════════════════ */

    /* Wrapper global de la landing */
    .landing-wrapper {
        background: #0d1b4c;
        color: #fff;
    }

    /* ── HERO ── */
    .hero-section {
        background: linear-gradient(135deg, #0d1b4c 0%, #10235f 55%, #0e2060 100%);
        position: relative;
        overflow: hidden;
        padding: 5.5rem 0 5rem;
        min-height: 600px;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -120px;
        width: 560px;
        height: 560px;
        background: radial-gradient(circle, rgba(52, 211, 153, .09) 0%, transparent 68%);
        pointer-events: none;
    }

    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -100px;
        left: -80px;
        width: 420px;
        height: 420px;
        background: radial-gradient(circle, rgba(52, 211, 153, .06) 0%, transparent 70%);
        pointer-events: none;
    }

    .hero-section .container {
        position: relative;
        z-index: 1;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        background: rgba(52, 211, 153, .12);
        border: 1px solid rgba(52, 211, 153, .28);
        color: #6ee7b7;
        border-radius: 24px;
        padding: .45rem 1.15rem;
        font-size: .78rem;
        font-weight: 600;
        letter-spacing: .5px;
        margin-bottom: 1.4rem;
    }

    .hero-badge .dot {
        width: 7px;
        height: 7px;
        background: #34d399;
        border-radius: 50%;
        animation: pulse-dot 1.8s ease-in-out infinite;
        flex-shrink: 0;
    }

    @keyframes pulse-dot {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: .45;
            transform: scale(.75);
        }
    }

    .hero-title {
        font-size: clamp(2.2rem, 5vw, 3.7rem);
        font-weight: 900;
        line-height: 1.1;
        color: #fff;
        letter-spacing: -.8px;
        margin-bottom: 1.25rem;
    }

    .hero-title .highlight {
        color: #34d399;
    }

    .hero-lead {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, .78);
        line-height: 1.68;
        max-width: 520px;
        margin-bottom: 2rem;
    }

    /* Stat chips */
    .stat-chip {
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 14px;
        padding: 1.1rem 1.5rem;
        text-align: center;
        backdrop-filter: blur(6px);
        flex: 1;
        min-width: 110px;
    }

    .stat-chip .stat-number {
        font-size: 1.85rem;
        font-weight: 900;
        color: #fff;
        line-height: 1;
    }

    .stat-chip .stat-number span {
        color: #34d399;
    }

    .stat-chip .stat-label {
        font-size: .7rem;
        color: rgba(255, 255, 255, .52);
        margin-top: .3rem;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    /* Botones hero */
    .btn-hero-primary {
        background: #34d399;
        color: #0d1b4c;
        border: none;
        border-radius: 14px;
        padding: .9rem 2rem;
        font-weight: 700;
        font-size: .95rem;
        transition: all .22s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
    }

    .btn-hero-primary:hover {
        background: #10b981;
        color: #0d1b4c;
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(52, 211, 153, .35);
    }

    .btn-hero-secondary {
        background: transparent;
        color: rgba(255, 255, 255, .88);
        border: 1.5px solid rgba(255, 255, 255, .22);
        border-radius: 14px;
        padding: .9rem 2rem;
        font-weight: 600;
        font-size: .95rem;
        transition: all .22s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
    }

    .btn-hero-secondary:hover {
        background: rgba(255, 255, 255, .09);
        border-color: rgba(255, 255, 255, .5);
        color: #fff;
    }

    /* Tarjeta de búsqueda (panel derecho hero) */
    .search-card {
        background: rgba(255, 255, 255, .07);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 24px;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        padding: 2.25rem 2rem;
    }

    .search-card h3 {
        color: #fff;
        font-weight: 700;
        font-size: 1.3rem;
        margin-bottom: 1.5rem;
    }

    .search-card .form-control,
    .search-card .form-select {
        background: rgba(255, 255, 255, .1) !important;
        border: 1px solid rgba(255, 255, 255, .15) !important;
        color: #fff !important;
        border-radius: 12px !important;
        padding: .85rem 1rem !important;
        font-size: .88rem !important;
        transition: border .15s, box-shadow .15s !important;
    }

    .search-card .form-control::placeholder {
        color: rgba(255, 255, 255, .45) !important;
    }

    .search-card .form-select option {
        background: #10235f;
        color: #fff;
    }

    .search-card .form-control:focus,
    .search-card .form-select:focus {
        border-color: rgba(52, 211, 153, .55) !important;
        box-shadow: 0 0 0 3px rgba(52, 211, 153, .14) !important;
        background: rgba(255, 255, 255, .13) !important;
        color: #fff !important;
        outline: none;
    }

    .btn-search {
        background: #34d399;
        color: #0d1b4c;
        border: none;
        border-radius: 12px;
        padding: .9rem 1.5rem;
        font-weight: 700;
        font-size: .92rem;
        width: 100%;
        transition: all .2s;
        cursor: pointer;
    }

    .btn-search:hover {
        background: #10b981;
        color: #0d1b4c;
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(52, 211, 153, .3);
    }

    .search-divider {
        border: none;
        border-top: 1px solid rgba(255, 255, 255, .1);
        margin: 1.25rem 0 1rem;
    }

    /* ── SECCIONES ── */
    .section-dark {
        background: rgba(255, 255, 255, .025);
        padding: 4.5rem 0;
    }

    .section-darker {
        background: rgba(0, 0, 0, .12);
        padding: 4.5rem 0;
    }

    .section-tag {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: 1.8px;
        text-transform: uppercase;
        color: #34d399;
        margin-bottom: .5rem;
        display: block;
    }

    .section-title-dark {
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        line-height: 1.2;
    }

    /* ── CÓMO FUNCIONA ── */
    .how-step {
        text-align: center;
        padding: 1.75rem 1.25rem;
        background: rgba(255, 255, 255, .06);
        border: 1px solid rgba(255, 255, 255, .09);
        border-radius: 18px;
        transition: all .22s ease;
        height: 100%;
    }

    .how-step:hover {
        background: rgba(255, 255, 255, .1);
        border-color: rgba(52, 211, 153, .22);
        transform: translateY(-3px);
    }

    .how-step .step-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .how-step .step-number {
        font-size: .68rem;
        font-weight: 700;
        letter-spacing: 1.3px;
        text-transform: uppercase;
        color: #34d399;
        margin-bottom: .4rem;
    }

    .how-step h5 {
        font-size: .93rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: .5rem;
    }

    .how-step p {
        font-size: .81rem;
        color: rgba(255, 255, 255, .58);
        line-height: 1.62;
        margin: 0;
    }

    /* ── TARJETAS DE VACANTES (dark) ── */
    .job-card-dark {
        background: rgba(255, 255, 255, .07);
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: 18px;
        transition: all .22s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .job-card-dark:hover {
        background: rgba(255, 255, 255, .11);
        border-color: rgba(52, 211, 153, .28);
        transform: translateY(-3px);
        box-shadow: 0 18px 44px rgba(0, 0, 0, .32);
    }

    .job-card-dark .card-body {
        padding: 1.35rem;
        flex: 1;
    }

    .job-card-dark .company-logo {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: cover;
        border: 1.5px solid rgba(255, 255, 255, .12);
        background: rgba(255, 255, 255, .08);
        flex-shrink: 0;
    }

    .job-card-dark .job-title {
        font-size: .93rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.3;
        margin-bottom: .2rem;
    }

    .job-card-dark .company-name {
        font-size: .77rem;
        color: rgba(255, 255, 255, .55);
    }

    .job-card-dark .badge-location {
        font-size: .71rem;
        background: rgba(255, 255, 255, .1);
        color: rgba(255, 255, 255, .72);
        padding: .28rem .68rem;
        border-radius: 6px;
        font-weight: 500;
        white-space: nowrap;
    }

    .job-card-dark .badge-modality {
        font-size: .71rem;
        font-weight: 600;
        padding: .28rem .68rem;
        border-radius: 6px;
        white-space: nowrap;
    }

    .job-card-dark .badge-days {
        font-size: .71rem;
        background: rgba(234, 179, 8, .14);
        color: #fde68a;
        padding: .28rem .68rem;
        border-radius: 6px;
        font-weight: 600;
        white-space: nowrap;
    }

    .job-card-dark .job-desc {
        font-size: .8rem;
        color: rgba(255, 255, 255, .58);
        line-height: 1.55;
        margin-top: .75rem;
        margin-bottom: 0;
    }

    .job-card-dark .salary-label {
        font-size: .83rem;
        font-weight: 700;
        color: #34d399;
    }

    .job-card-dark .card-footer-dark {
        padding: .9rem 1.35rem;
        border-top: 1px solid rgba(255, 255, 255, .07);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-card-detail {
        background: rgba(52, 211, 153, .13);
        color: #34d399;
        border: 1px solid rgba(52, 211, 153, .22);
        border-radius: 8px;
        font-weight: 600;
        font-size: .77rem;
        padding: .38rem .9rem;
        text-decoration: none;
        transition: all .18s;
    }

    .btn-card-detail:hover {
        background: rgba(52, 211, 153, .25);
        color: #34d399;
        border-color: rgba(52, 211, 153, .45);
    }

    /* Login prompt card */
    .login-prompt-card {
        background: rgba(255, 255, 255, .05);
        border: 1.5px dashed rgba(255, 255, 255, .14);
        border-radius: 18px;
        text-align: center;
        padding: 2.75rem 1.5rem;
    }

    /* ── CTA EMPRESA ── */
    .cta-empresa {
        background: linear-gradient(135deg, #10235f 0%, #0e3855 100%);
        border: 1px solid rgba(52, 211, 153, .18);
        border-radius: 20px;
        padding: 3rem 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .cta-empresa::before {
        content: '';
        position: absolute;
        top: -70px;
        right: -70px;
        width: 260px;
        height: 260px;
        background: radial-gradient(circle, rgba(52, 211, 153, .1) 0%, transparent 68%);
        pointer-events: none;
    }

    .cta-empresa>* {
        position: relative;
        z-index: 1;
    }

    /* Ver todas btn */
    .btn-ver-todas {
        background: rgba(255, 255, 255, .08);
        color: rgba(255, 255, 255, .8);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 10px;
        font-weight: 600;
        font-size: .82rem;
        padding: .5rem 1rem;
        text-decoration: none;
        transition: all .18s;
        display: inline-flex;
        align-items: center;
        gap: .4rem;
    }

    .btn-ver-todas:hover {
        background: rgba(255, 255, 255, .13);
        color: #fff;
        border-color: rgba(255, 255, 255, .25);
    }
</style>
@endpush

@section('content')

<div class="landing-wrapper">

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- HERO                                                    --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">

                {{-- Columna izquierda: texto --}}
                <div class="col-lg-6">
                    <div class="hero-badge">
                        <span class="dot"></span>
                        Bolsa de empleo oficial · UNIPAZ Barrancabermeja
                    </div>

                    <h1 class="hero-title">
                        Conectamos talento<br>
                        universitario con<br>
                        <span class="highlight">oportunidades reales.</span>
                    </h1>

                    <p class="hero-lead">
                        Plataforma institucional para estudiantes, egresados y empresas del Districto de Barrancabermej y el Magdalena Medio.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-4">
                        <a href="{{ route('auth.google') }}" class="btn-hero-primary">
                            <i class="bi bi-mortarboard-fill"></i>
                            Buscar vacantes
                        </a>
                        <a href="{{ route('company.register') }}" class="btn-hero-secondary">
                            <i class="bi bi-building"></i>
                            Registrar empresa
                        </a>
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <div class="stat-chip">
                            <div class="stat-number">3<span>K+</span></div>
                            <div class="stat-label">Estudiantes</div>
                        </div>
                        <div class="stat-chip">
                            <div class="stat-number">{{ $totalJobs }}<span>+</span></div>
                            <div class="stat-label">Vacantes activas</div>
                        </div>
                        <div class="stat-chip">
                            <div class="stat-number">{{ $totalCompanies }}</div>
                            <div class="stat-label">Empresas aliadas</div>
                        </div>
                    </div>
                </div>

                {{-- Columna derecha: buscador --}}
                <div class="col-lg-6">
                    <div class="search-card">
                        <h3>Buscar oportunidades</h3>
                        <form action="{{ route('auth.google') }}" method="GET">
                            <div class="d-flex flex-column gap-3">
                                <input type="text"
                                    class="form-control"
                                    name="q"
                                    placeholder="Cargo o palabra clave…">
                                <input type="text"
                                    class="form-control"
                                    name="location"
                                    placeholder="Ciudad o región">
                                <select class="form-select" name="area">
                                    <option value="">Área de interés</option>
                                    <option value="ingenieria">Ingeniería y tecnología</option>
                                    <option value="administracion">Administración y negocios</option>
                                    <option value="salud">Salud y ciencias de la vida</option>
                                    <option value="derecho">Derecho y ciencias sociales</option>
                                    <option value="educacion">Educación</option>
                                    <option value="otro">Otro</option>
                                </select>
                                <button type="submit" class="btn-search">
                                    <i class="bi bi-search me-2"></i>Buscar ahora
                                </button>
                            </div>
                        </form>
                        <hr class="search-divider">
                        <div class="d-flex align-items-center gap-2"
                            style="font-size:.79rem; color:rgba(255,255,255,.48);">
                            <i class="bi bi-shield-check" style="color:#34d399; font-size:.9rem;"></i>
                            Plataforma institucional verificada · UNIPAZ
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- CÓMO FUNCIONA                                          --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <section class="section-darker">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-tag">¿Cómo funciona?</span>
                <h2 class="section-title-dark">Simple, rápido y gratuito</h2>
            </div>
            <div class="row g-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="how-step">
                        <div class="step-icon" style="background:rgba(16,35,95,.9);">
                            <i class="bi bi-google" style="color:#34d399;"></i>
                        </div>
                        <div class="step-number">Paso 01</div>
                        <h5>Ingresa con tu correo</h5>
                        <p>Usa tu cuenta institucional <strong style="color:rgba(255,255,255,.85);">@unipaz.edu.co</strong> para acceder de forma segura.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="how-step">
                        <div class="step-icon" style="background:rgba(52,211,153,.1);">
                            <i class="bi bi-person-badge" style="color:#34d399;"></i>
                        </div>
                        <div class="step-number">Paso 02</div>
                        <h5>Completa tu perfil</h5>
                        <p>Agrega tu programa académico, semestre, CV y habilidades para destacar ante las empresas.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="how-step">
                        <div class="step-icon" style="background:rgba(16,35,95,.9);">
                            <i class="bi bi-search" style="color:#34d399;"></i>
                        </div>
                        <div class="step-number">Paso 03</div>
                        <h5>Explora vacantes</h5>