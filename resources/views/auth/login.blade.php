@extends('layouts.app')
@section('title', 'Acceso Empresas')

@push('styles')
<style>
    /* ═══ LOGIN EMPRESAS — EMPLEA UNIPAZ ═══ */
    .login-page {
        min-height: calc(100vh - 60px);
        background: linear-gradient(135deg, #0d1b4c 0%, #10235f 60%, #0e2060 100%);
        display: flex;
        align-items: center;
        padding: 3rem 0;
        position: relative;
        overflow: hidden;
    }

    .login-page::before {
        content: '';
        position: absolute;
        top: -100px; right: -100px;
        width: 480px; height: 480px;
        background: radial-gradient(circle, rgba(52,211,153,.08) 0%, transparent 68%);
        pointer-events: none;
    }

    .login-page::after {
        content: '';
        position: absolute;
        bottom: -100px; left: -80px;
        width: 360px; height: 360px;
        background: radial-gradient(circle, rgba(52,211,153,.05) 0%, transparent 70%);
        pointer-events: none;
    }

    .login-page .container { position: relative; z-index: 1; }

    /* Tarjeta empresa */
    .company-card {
        background: rgba(255,255,255,.07);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 22px;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        overflow: hidden;
    }

    .company-card .card-body { padding: 2.5rem 2.25rem; }

    .company-card .login-icon-wrap {
        width: 62px; height: 62px;
        background: rgba(52,211,153,.1);
        border: 1.5px solid rgba(52,211,153,.22);
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #34d399;
        margin-bottom: 1.25rem;
    }

    .company-card h5 { font-weight: 800; color: #fff; font-size: 1.3rem; }
    .company-card .card-desc {
        color: rgba(255,255,255,.52);
        font-size: .84rem;
        line-height: 1.55;
        margin-bottom: 1.75rem;
    }

    /* Inputs */
    .company-card .form-label {
        color: rgba(255,255,255,.72);
        font-weight: 600;
        font-size: .82rem;
        margin-bottom: .35rem;
    }

    .company-card .form-control {
        background: rgba(255,255,255,.1) !important;
        border: 1px solid rgba(255,255,255,.15) !important;
        color: #fff !important;
        border-radius: 12px !important;
        padding: .8rem 1rem !important;
        font-size: .9rem;
        transition: border .15s, box-shadow .15s;
    }

    .company-card .form-control::placeholder { color: rgba(255,255,255,.32) !important; }

    .company-card .form-control:focus {
        border-color: rgba(52,211,153,.5) !important;
        box-shadow: 0 0 0 3px rgba(52,211,153,.13) !important;
        background: rgba(255,255,255,.14) !important;
        color: #fff !important;
        outline: none;
    }

    .company-card .input-group-text {
        background: rgba(255,255,255,.08) !important;
        border: 1px solid rgba(255,255,255,.15) !important;
        border-right: none !important;
        border-radius: 12px 0 0 12px !important;
        color: rgba(255,255,255,.38);
    }

    .company-card .form-control.border-start-0 {
        border-left: none !important;
        border-radius: 0 12px 12px 0 !important;
    }

    .company-card .form-check-label { color: rgba(255,255,255,.52); font-size: .82rem; }
    .company-card .form-check-input {
        border-color: rgba(255,255,255,.22);
        background-color: rgba(255,255,255,.08);
    }
    .company-card .form-check-input:checked { background-color: #34d399; border-color: #34d399; }

    /* Botón ingresar */
    .btn-company-login {
        background: #34d399;
        color: #0d1b4c;
        border: none;
        border-radius: 12px;
        padding: .85rem;
        font-weight: 700;
        font-size: .95rem;
        width: 100%;
        transition: all .2s;
        cursor: pointer;
    }

    .btn-company-login:hover {
        background: #10b981;
        color: #0d1b4c;
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(52,211,153,.3);
    }

    /* Link registrar empresa */
    .register-link {
        background: rgba(52,211,153,.1);
        color: #34d399;
        border: 1px solid rgba(52,211,153,.2);
        border-radius: 10px;
        font-weight: 600;
        font-size: .82rem;
        padding: .45rem 1rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        transition: all .18s;
    }

    .register-link:hover {
        background: rgba(52,211,153,.2);
        color: #34d399;
        border-color: rgba(52,211,153,.38);
    }

    /* Link volver al inicio */
    .back-link {
        color: rgba(255,255,255,.45);
        font-size: .82rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        transition: color .15s;
    }

    .back-link:hover { color: rgba(255,255,255,.8); }
</style>
@endpush

@section('content')
<div class="login-page">
    <div class="container">

        {{-- Header --}}
        <div class="text-center mb-4">
            <img src="{{ asset('images/LogoWhite_.png') }}"
                 alt="UNIPAZ — Instituto Universitario de la Paz"
                 style="height: 56px; width: auto; margin-bottom: 1.1rem;">
            <h1 style="font-size:1.4rem; font-weight:800; color:#fff;">Acceso Empresas</h1>
            <p style="color:rgba(255,255,255,.48); font-size:.86rem; margin-bottom:0;">
                Ingresa con tus credenciales para gestionar tus vacantes
            </p>
        </div>

        {{-- Formulario centrado --}}
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="company-card">
                    <div class="card-body">

                        <div class="text-center">
                            <div class="login-icon-wrap">
                                <i class="bi bi-building-fill"></i>
                            </div>
                            <h5 class="mb-1">Soy Empresa</h5>
                            <p class="card-desc">
                                Accede para publicar vacantes, revisar postulaciones y gestionar tu empresa.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Correo electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" name="email"
                                           class="form-control border-start-0 @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}"
                                           placeholder="correo@empresa.com"
                                           required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" name="password"
                                           class="form-control border-start-0 @error('password') is-invalid @enderror"
                                           placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Recordarme
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn-company-login">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                            </button>
                        </form>

                        <div class="text-center mt-4 pt-3"
                             style="border-top: 1px solid rgba(255,255,255,.08);">
                            <span style="color:rgba(255,255,255,.4); font-size:.82rem;">
                                ¿Tu empresa aún no está registrada?
                            </span>
                            <br>
                            <a href="{{ route('company.register') }}" class="register-link mt-2">
                                <i class="bi bi-building-add"></i>Registrar empresa
                            </a>
                        </div>

                    </div>
                </div>

                {{-- Volver --}}
                <div class="text-center mt-3">
                    <a href="{{ route('home') }}" class="back-link">
                        <i class="bi bi-arrow-left"></i> Volver al inicio
                    </a>
                </div>
            </div>
        </div>

        {{-- Nota de seguridad --}}
        <div class="text-center mt-4">
            <small style="color:rgba(255,255,255,.28); font-size:.76rem;">
                <i class="bi bi-shield-lock me-1" style="color:#34d399;"></i>
                Plataforma segura · Datos protegidos · Instituto Universitario de la Paz — UNIPAZ
            </small>
        </div>

    </div>
</div>
@endsection
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             