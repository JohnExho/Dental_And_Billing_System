@extends("layout")
@section('title', 'Already Logged In | Chomply')

@section("content")
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #1b2a49, #3a6073);
        min-height: 100vh;
        position: relative;
        overflow: hidden;
    }
    
    .gradient-bg::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 15s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.5); opacity: 0.8; }
    }
    
    .alert-custom {
        background: rgba(255, 255, 255, 0.95);
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(10px);
        padding: 2rem;
        margin-bottom: 3rem;
    }
    
    .alert-custom h4 {
        color: #1b2a49;
        font-weight: 600;
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .alert-custom p {
        color: #5a6c7d;
        font-size: 1.1rem;
    }
    
    .card-custom {
        background: rgba(255, 255, 255, 0.98);
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        overflow: hidden;
        position: relative;
    }
    
    .card-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3a6073, #1b2a49);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }
    
    .card-custom:hover::before {
        transform: scaleX(1);
    }
    
    .card-custom:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
    }
    
    .card-custom.border-danger::before {
        background: linear-gradient(90deg, #dc3545, #c82333);
    }
    
    .card-body-custom {
        padding: 3rem 2rem;
    }
    
    .card-icon {
        font-size: 3rem;
        margin-bottom: 1.5rem;
        display: inline-block;
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .btn-custom {
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.05rem;
        transition: all 0.3s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }
    
    .btn-custom::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-custom:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-custom:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #3a6073, #1b2a49);
    }
    
    .btn-success-custom {
        background: linear-gradient(135deg, #28a745, #20c997);
    }
    
    .btn-danger-custom {
        background: linear-gradient(135deg, #dc3545, #c82333);
    }
    
    .card-title-custom {
        color: #1b2a49;
        font-weight: 700;
        font-size: 1.4rem;
        margin-bottom: 1.5rem;
    }
    
    .card-title-danger {
        color: #dc3545;
    }
    
    .role-badge {
        display: inline-block;
        padding: 0.5rem 1.25rem;
        background: linear-gradient(135deg, #3a6073, #1b2a49);
        color: white;
        border-radius: 50px;
        font-weight: 600;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>

<div class="gradient-bg d-flex align-items-center">
    <div class="container py-5">
        <div class="alert-custom text-center mx-auto" style="max-width: 700px;">
            <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 2.5rem; margin-bottom: 1rem;"></i>
            <h4>You are already logged in</h4>
            <p class="mb-0">Please choose what you'd like to do</p>
        </div>

        <div class="row justify-content-center g-4">
            {{-- Continue as current role --}}
            <div class="col-lg-5 col-md-6">
                <div class="card-custom h-100 text-center">
                    <div class="card-body-custom d-flex flex-column justify-content-center">
                        <div class="card-icon">
                            @if(session('active_role') === 'admin')
                                <i class="bi bi-person-badge-fill"></i>
                            @else
                                <i class="bi bi-person-circle"></i>
                            @endif
                        </div>
                        <span class="role-badge">{{ ucfirst(session('active_role')) }}</span>
                        <h5 class="card-title-custom">Continue Your Session</h5>
                        @if(session('active_role') === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-custom btn-primary-custom">
                                Go to Admin Dashboard →
                            </a>
                        @else
                            <a href="{{ route('staff.dashboard') }}" class="btn btn-custom btn-success-custom">
                                Go to Staff Dashboard →
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Logout --}}
            <div class="col-lg-5 col-md-6">
                <div class="card-custom border-danger h-100 text-center">
                    <div class="card-body-custom d-flex flex-column justify-content-center">
                        <div class="card-icon">
                            <i class="bi bi-box-arrow-right"></i>
                        </div>
                        <h5 class="card-title-custom card-title-danger">Switch Account</h5>
                        <p class="text-muted mb-4">Logout and sign in with a different account</p>
                        <form method="POST" action="{{ route('process-logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-custom btn-danger-custom">
                                Logout & Switch User
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection