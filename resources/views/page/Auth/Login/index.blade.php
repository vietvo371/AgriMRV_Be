@extends('share.Blank.master_blank')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <div class="text-center mb-4">
            <i class="fas fa-user-graduate fa-3x text-primary"></i>
            <h4 class="mt-2">Đăng nhập AgriMR</h4>
            <p class="text-muted">Hệ thống quản lý carbon farming</p>
        </div>

        <!-- Error Alert -->
        <div id="errorAlert" class="alert alert-danger d-none" role="alert">
            <span id="errorMessage"></span>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center mb-3 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Login Form -->
        <form id="loginForm" action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" required autocomplete="email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary w-100" id="loginBtn">
                <span id="btnText">Đăng nhập</span>
            </button>
        </form>

        <div class="mt-3 text-center">
            <a href="#" class="text-decoration-none">Quên mật khẩu?</a>
        </div>

        <!-- Demo Accounts Info -->
        <div class="mt-4 p-3 bg-light rounded">
            <small class="text-muted">
                <strong>Demo Accounts (đã có sẵn trong DB):</strong><br>
                • Farmer: farmer1@example.com / 12345678<br>
                • Cooperative: coop1@example.com / 12345678<br>
                • Verifier: verifier1@example.com / 12345678<br>
                • Bank: bank@example.com / 12345678<br>
                • Government: government@example.com / 12345678<br>
                • Buyer: buyer1@example.com / 12345678
            </small>
            <div class="mt-2">
                <small class="text-info">
                    <i class="fas fa-info-circle"></i>
                    Tất cả users đã được tạo sẵn với password: 12345678
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<style>
    .card {
        border-radius: 1rem;
    }
    .form-label {
        font-weight: 500;
    }
    .alert {
        border-radius: 0.5rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorAlert = document.getElementById('errorAlert');
    const errorMessage = document.getElementById('errorMessage');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const loginBtn = document.getElementById('loginBtn');
    const btnText = document.getElementById('btnText');

    // Kiểm tra nếu đã đăng nhập thì redirect
    checkExistingLogin();

    // Handle form submission
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (!email || !password) {
            showError('Vui lòng nhập đầy đủ thông tin');
            return;
        }

        await login(email, password);
    });

    async function login(email, password) {
        try {
            // Show loading state
            setLoading(true);
            hideError();

                           const response = await axios.post('/api/auth/web-login', {
                   email: email,
                   password: password
               }, {
                   headers: {
                       'Content-Type': 'application/json',
                       'Accept': 'application/json'
                   }
               });

            console.log('Login response:', response.data);

            // Store token and user info
            localStorage.setItem('token', response.data.token);
            localStorage.setItem('user', JSON.stringify(response.data.user));

            // Show success message and redirect
            showSuccessAndRedirect(response.data.user.user_type);
        } catch (error) {
            console.error('Login error:', error);
            let errorMsg = 'Đăng nhập thất bại. Vui lòng thử lại.';

            // Hiển thị validation errors nếu có
            if (error.response?.status === 422) {
                const errors = error.response.data.errors;
                if (errors) {
                    errorMsg = Object.values(errors).flat().join(', ');
                } else if (error.response.data.message) {
                    errorMsg = error.response.data.message;
                }
            }
            showError(errorMsg);
        } finally {
            setLoading(false);
        }
    }

    function setLoading(loading) {
        if (loading) {
            loadingSpinner.classList.remove('d-none');
            loginBtn.disabled = true;
            btnText.textContent = 'Đang xử lý...';
        } else {
            loadingSpinner.classList.add('d-none');
            loginBtn.disabled = false;
            btnText.textContent = 'Đăng nhập';
        }
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorAlert.classList.remove('d-none');
    }

    function hideError() {
        errorAlert.classList.add('d-none');
    }

    function showSuccessAndRedirect(userType) {
        const routes = {
            'farmer': '/dashboard',
            'cooperative': '/cooperative',
            'verifier': '/verifier',
            'bank': '/bank',
            'government': '/government',
            'buyer': '/buyer'
        };

        const route = routes[userType] || '/dashboard';
        const userTypeName = getUserTypeName(userType);

        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Đăng nhập thành công!',
            text: `Chào mừng ${userTypeName}`,
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = route;
        });
    }

    function getUserTypeName(userType) {
        const names = {
            'farmer': 'Nông dân',
            'cooperative': 'Hợp tác xã',
            'verifier': 'Đơn vị xác minh',
            'bank': 'Ngân hàng',
            'government': 'Chính phủ',
            'buyer': 'Người mua carbon'
        };
        return names[userType] || 'Người dùng';
    }

    function checkExistingLogin() {
        const token = localStorage.getItem('token');
        const user = localStorage.getItem('user');

        if (token && user) {
            try {
                const userData = JSON.parse(user);
                showSuccessAndRedirect(userData.user_type);
            } catch (e) {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
            }
        }
    }
});
</script>
@endsection
