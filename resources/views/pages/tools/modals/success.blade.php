@extends('layout')
@section('title', 'Registration Successful')

@section('content')
<div class="container mt-5 px-3">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-11 col-md-10 col-lg-8">
            <div class="card border-0 shadow-lg">
                <div class="card-body text-center py-4 py-md-5 px-3 px-md-4">
                    <!-- Animated Success Icon -->
                    <div class="mb-4 position-relative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>
                    
                    <!-- Success Message -->
                    <h1 class="display-4 text-success mb-3 fw-bold">Registration Successful!</h1>
                    
                    @if(session('success'))
                        <p class="lead text-muted mb-4 px-3">{{ session('success') }}</p>
                    @endif
                    
                    <!-- Decorative Element -->
                    <div class="mt-5">
                        <p class="text-muted small mb-2">Thank you for choosing our healthcare services</p>
                        <p class="text-muted small mb-0"><em>You may now close this page/tab</em></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .success-checkmark {
        width: 100px;
        height: 100px;
        margin: 0 auto;
    }
    
    .success-checkmark .check-icon {
        width: 100px;
        height: 100px;
        position: relative;
        border-radius: 50%;
        box-sizing: content-box;
        border: 4px solid #198754;
    }
    
    .success-checkmark .check-icon::before {
        top: 3px;
        left: -2px;
        width: 25px;
        transform-origin: 100% 50%;
        border-radius: 100px 0 0 100px;
    }
    
    .success-checkmark .check-icon::after {
        top: 0;
        left: 25px;
        width: 50px;
        transform-origin: 0 50%;
        border-radius: 0 100px 100px 0;
        animation: rotate-circle 4.25s ease-in;
    }
    
    .success-checkmark .check-icon::before, .success-checkmark .check-icon::after {
        content: '';
        height: 100px;
        position: absolute;
        background: #fff;
        transform: rotate(-45deg);
    }
    
    .success-checkmark .check-icon .icon-line {
        height: 4px;
        background-color: #198754;
        display: block;
        border-radius: 2px;
        position: absolute;
        z-index: 10;
    }
    
    .success-checkmark .check-icon .icon-line.line-tip {
        top: 46px;
        left: 18px;
        width: 21px;
        transform: rotate(45deg);
        animation: icon-line-tip 0.75s;
    }
    
    .success-checkmark .check-icon .icon-line.line-long {
        top: 40px;
        right: 11px;
        width: 39px;
        transform: rotate(-45deg);
        animation: icon-line-long 0.75s;
    }
    
    .success-checkmark .check-icon .icon-circle {
        top: -4px;
        left: -4px;
        z-index: 10;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        position: absolute;
        box-sizing: content-box;
        border: 4px solid rgba(25, 135, 84, .2);
    }
    
    .success-checkmark .check-icon .icon-fix {
        top: 8px;
        width: 5px;
        left: 22px;
        z-index: 1;
        height: 70px;
        position: absolute;
        transform: rotate(-45deg);
        background-color: #fff;
    }
    
    /* Medium screens and up */
    @media (min-width: 768px) {
        .success-checkmark {
            width: 120px;
            height: 120px;
        }
        
        .success-checkmark .check-icon {
            width: 120px;
            height: 120px;
            border: 4px solid #198754;
        }
        
        .success-checkmark .check-icon::before {
            width: 30px;
            height: 120px;
        }
        
        .success-checkmark .check-icon::after {
            left: 30px;
            width: 60px;
            height: 120px;
        }
        
        .success-checkmark .check-icon .icon-line {
            height: 5px;
        }
        
        .success-checkmark .check-icon .icon-line.line-tip {
            top: 56px;
            left: 21px;
            width: 25px;
        }
        
        .success-checkmark .check-icon .icon-line.line-long {
            top: 48px;
            right: 13px;
            width: 47px;
        }
        
        .success-checkmark .check-icon .icon-circle {
            width: 120px;
            height: 120px;
        }
        
        .success-checkmark .check-icon .icon-fix {
            left: 26px;
            height: 85px;
        }
    }
    
    @keyframes rotate-circle {
        0% {
            transform: rotate(-45deg);
        }
        5% {
            transform: rotate(-45deg);
        }
        12% {
            transform: rotate(-405deg);
        }
        100% {
            transform: rotate(-405deg);
        }
    }
    
    @keyframes icon-line-tip {
        0% {
            width: 0;
            left: 1px;
            top: 16px;
        }
        54% {
            width: 0;
            left: 1px;
            top: 16px;
        }
        70% {
            width: 42px;
            left: -7px;
            top: 31px;
        }
        84% {
            width: 14px;
            left: 18px;
            top: 40px;
        }
        100% {
            width: 21px;
            left: 18px;
            top: 46px;
        }
    }
    
    @keyframes icon-line-long {
        0% {
            width: 0;
            right: 38px;
            top: 45px;
        }
        65% {
            width: 0;
            right: 38px;
            top: 45px;
        }
        84% {
            width: 46px;
            right: 0px;
            top: 29px;
        }
        100% {
            width: 39px;
            right: 11px;
            top: 40px;
        }
    }
    
    /* Medium screens and up - adjust animations */
    @media (min-width: 768px) {
        @keyframes icon-line-tip {
            0% {
                width: 0;
                left: 1px;
                top: 19px;
            }
            54% {
                width: 0;
                left: 1px;
                top: 19px;
            }
            70% {
                width: 50px;
                left: -8px;
                top: 37px;
            }
            84% {
                width: 17px;
                left: 21px;
                top: 48px;
            }
            100% {
                width: 25px;
                left: 21px;
                top: 56px;
            }
        }
        
        @keyframes icon-line-long {
            0% {
                width: 0;
                right: 46px;
                top: 54px;
            }
            65% {
                width: 0;
                right: 46px;
                top: 54px;
            }
            84% {
                width: 55px;
                right: 0px;
                top: 35px;
            }
            100% {
                width: 47px;
                right: 13px;
                top: 48px;
            }
        }
    }
    
    .card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
    
    .display-4 {
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endsection