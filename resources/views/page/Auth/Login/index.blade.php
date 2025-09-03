@extends('share.Blank.master_blank')

@section('content')
<div class="eco-background">
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="login-card">
            <!-- Header -->
            <div class="text-center mb-4">
                <div class="eco-icon mb-3">
                    <i class="fas fa-seedling"></i>
                </div>
                <h2 class="eco-title">AgriMRV</h2>
                <p class="eco-subtitle">Carbon Farming Management</p>
            </div>

            <!-- Error Alerts -->
            <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                <span id="errorMessage"></span>
            </div>

            @if(session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
            @endif

            <!-- Login Form -->
            <form id="loginForm" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <input type="email" class="eco-input" id="email" name="email"
                           placeholder="Email Address" required autocomplete="email">
                </div>

                <div class="form-group">
                    <input type="password" class="eco-input" id="password" name="password"
                           placeholder="Password" required autocomplete="current-password">
                </div>

                <button type="submit" class="eco-btn" id="loginBtn">
                    <span id="btnText">Sign In</span>
                </button>
            </form>

            <!-- Demo Info -->
            <div class="demo-section">
                <div class="demo-title">Demo Accounts</div>
                <div class="demo-account">
                    <span class="demo-label">Verifier:</span>
                    <span class="demo-value">verifier1@example.com</span>
                </div>
                <div class="demo-account">
                    <span class="demo-label">Bank:</span>
                    <span class="demo-value">bank@example.com</span>
                </div>
                <div class="demo-password">
                    <i class="fas fa-key"></i> Password: 12345678
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<style>
    /* Green Environmental Theme */
    :root {
        --eco-primary: #2d5016;
        --eco-secondary: #4a7c59;
        --eco-accent: #7cb342;
        --eco-light: #a5d6a7;
        --eco-bg: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
        --eco-shadow: rgba(45, 80, 22, 0.15);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* .eco-background {
        background: var(--eco-bg);
        min-height: 100vh;
        position: relative;
    } */

    .eco-background::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image:
            radial-gradient(circle at 20% 50%, rgba(139, 195, 74, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(76, 175, 80, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 80%, rgba(104, 159, 56, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 40px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 20px 40px var(--eco-shadow);
        border: 1px solid rgba(139, 195, 74, 0.2);
    }

    .eco-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--eco-accent), var(--eco-secondary));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        box-shadow: 0 8px 25px rgba(139, 195, 74, 0.3);
    }

    .eco-icon i {
        font-size: 2rem;
        color: white;
    }

    .eco-title {
        color: var(--eco-primary);
        font-weight: 700;
        font-size: 2rem;
        margin: 0;
        letter-spacing: -0.5px;
    }

    .eco-subtitle {
        color: var(--eco-secondary);
        font-size: 0.95rem;
        margin: 8px 0 0 0;
        font-weight: 500;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .eco-input {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid #e8f5e8;
        border-radius: 12px;
        font-size: 1rem;
        background: rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
        outline: none;
    }

    .eco-input:focus {
        border-color: var(--eco-accent);
        background: white;
        box-shadow: 0 0 0 4px rgba(139, 195, 74, 0.1);
    }

    .eco-input::placeholder {
        color: #81c784;
        font-weight: 500;
    }

    .eco-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, var(--eco-accent), var(--eco-secondary));
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 30px;
    }

    .eco-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(139, 195, 74, 0.4);
    }

    .eco-btn:active {
        transform: translateY(0);
    }

    .eco-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .demo-section {
        background: rgba(232, 245, 232, 0.6);
        border-radius: 12px;
        padding: 20px;
        border: 1px solid rgba(139, 195, 74, 0.2);
    }

    .demo-title {
        color: var(--eco-primary);
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 15px;
        text-align: center;
    }

    .demo-account {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.85rem;
    }

    .demo-label {
        color: var(--eco-secondary);
        font-weight: 600;
    }

    .demo-value {
        color: var(--eco-primary);
        font-family: 'Monaco', 'Menlo', monospace;
    }

    .demo-password {
        text-align: center;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(139, 195, 74, 0.3);
        color: var(--eco-secondary);
        font-size: 0.85rem;
        font-weight: 600;
    }

    .demo-password i {
        color: var(--eco-accent);
        margin-right: 5px;
    }

    .alert {
        border-radius: 12px;
        border: none;
        margin-bottom: 20px;
        padding: 12px 16px;
        font-size: 0.9rem;
    }

    .alert-danger {
        background: rgba(244, 67, 54, 0.1);
        color: #c62828;
        border-left: 4px solid #f44336;
    }

    /* Loading Animation */
    .spinner-border {
        width: 20px;
        height: 20px;
        border-width: 2px;
    }

    /* Responsive */
    @media (max-width: 480px) {
        .login-card {
            margin: 20px;
            padding: 30px 25px;
        }

        .eco-title {
            font-size: 1.75rem;
        }

        .eco-icon {
            width: 70px;
            height: 70px;
        }

        .eco-icon i {
            font-size: 1.75rem;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const loginBtn = document.getElementById('loginBtn');
    const btnText = document.getElementById('btnText');

    loginForm.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            e.preventDefault();
            showError('Please enter both email and password');
            return;
        }

        setLoading(true);
        hideError();
    });

    function setLoading(loading) {
        if (loading) {
            loginBtn.disabled = true;
            btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Signing In...';
        } else {
            loginBtn.disabled = false;
            btnText.innerHTML = 'Sign In';
        }
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorAlert.classList.remove('d-none');
        setTimeout(() => hideError(), 5000);
    }

    function hideError() {
        errorAlert.classList.add('d-none');
    }

    // Add subtle animations to inputs
    const inputs = document.querySelectorAll('.eco-input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });

        input.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection
