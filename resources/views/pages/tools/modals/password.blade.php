@extends('layout')

@section('content')
<style>
    .verify-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1rem;
    }
    
    .verify-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        max-width: 450px;
        width: 100%;
        overflow: hidden;
        animation: slideUp 0.5s ease;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .verify-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        text-align: center;
        color: white;
    }
    
    .verify-header i {
        font-size: 4rem;
        margin-bottom: 1rem;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
    
    .verify-header h2 {
        margin: 0;
        font-weight: 700;
        font-size: 1.75rem;
    }
    
    .verify-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 0.95rem;
    }
    
    .verify-body {
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .password-input-wrapper {
        position: relative;
    }
    
    .form-control {
        padding: 0.875rem 3rem 0.875rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 0.75rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    
    .toggle-password {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 1.2rem;
        padding: 0.25rem;
        transition: color 0.3s ease;
    }
    
    .toggle-password:hover {
        color: #667eea;
    }
    
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .btn-verify {
        width: 100%;
        padding: 0.875rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 1.05rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .btn-verify:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
    
    .btn-verify:active {
        transform: translateY(0);
    }
    
    .security-note {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 1.5rem;
    }
    
    .security-note i {
        color: #667eea;
        margin-right: 0.5rem;
    }
    
    .security-note p {
        margin: 0;
        font-size: 0.875rem;
        color: #666;
    }
    
    /* Mobile responsive */
    @media (max-width: 576px) {
        .verify-header {
            padding: 1.5rem;
        }
        
        .verify-header i {
            font-size: 3rem;
        }
        
        .verify-header h2 {
            font-size: 1.5rem;
        }
        
        .verify-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="verify-container">
    <div class="verify-card">
        <!-- Header -->
        <div class="verify-header">
            <i class="bi bi-shield-lock-fill"></i>
            <h2>Protected QR Code</h2>
            <p>Enter password to access patient information</p>
        </div>
        
        <!-- Body -->
        <div class="verify-body">
            <form action="{{ route('qr.verify', $qr->qr_id) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="qr_password" class="form-label">
                        <i class="bi bi-key-fill"></i> Password
                    </label>
                    <div class="password-input-wrapper">
                        <input 
                            type="password" 
                            name="qr_password" 
                            id="qr_password" 
                            class="form-control @error('qr_password') is-invalid @enderror" 
                            placeholder="Enter QR code password"
                            autofocus
                            required
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="bi bi-eye-fill" id="toggleIcon"></i>
                        </button>
                    </div>
                    
                    @error('qr_password')
                        <div class="invalid-feedback" style="display: flex;">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <button type="submit" class="btn-verify">
                    <i class="bi bi-unlock-fill"></i>
                    Verify & Access
                </button>
                
                <div class="security-note">
                    <i class="bi bi-info-circle-fill"></i>
                    <p><strong>Security Notice:</strong> This QR code contains sensitive patient information. Please ensure you have proper authorization to access this data.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('qr_password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye-fill');
            toggleIcon.classList.add('bi-eye-slash-fill');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash-fill');
            toggleIcon.classList.add('bi-eye-fill');
        }
    }
    
    // Auto-focus on input when page loads
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('qr_password').focus();
    });
    
    // Show error animation if there's an error
    @error('qr_password')
        const card = document.querySelector('.verify-card');
        card.style.animation = 'shake 0.5s';
        
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-10px); }
                75% { transform: translateX(10px); }
            }
        `;
        document.head.appendChild(style);
    @enderror
</script>
@endsection