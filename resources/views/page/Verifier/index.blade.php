@extends('share.Verifier.master')

@section('title')
    <div>
        <h1 class="mb-1">Verifier Dashboard</h1>
        <p class="text-muted">Tổng quan xác minh MRV và quản lý verification requests</p>
    </div>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <!-- Total Requests -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span>Total Requests</span>
                <div class="stat-card-icon primary"><i class="fa fa-file-alt"></i></div>
            </div>
            <div class="stat-value" id="totalRequests">0</div>
            <div class="stat-label" id="pendingCount">0 pending review</div>
        </div>
    </div>

    <!-- Scheduled Visits -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span>Scheduled Visits</span>
                <div class="stat-card-icon info"><i class="fa fa-calendar"></i></div>
            </div>
            <div class="stat-value" id="scheduledVisits">0</div>
            <div class="stat-label">This week</div>
        </div>
    </div>

    <!-- Completed -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span>Completed</span>
                <div class="stat-card-icon success"><i class="fa fa-check-circle"></i></div>
            </div>
            <div class="stat-value" id="completedCount">0</div>
            <div class="stat-label">This month</div>
        </div>
    </div>

    <!-- Carbon Claims -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span>Carbon Claims</span>
                <div class="stat-card-icon warning"><i class="fa fa-leaf"></i></div>
            </div>
            <div class="stat-value" id="carbonClaims">0</div>
            <div class="stat-label">tCO₂e under review</div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="input-group">
            <span class="input-group-text"><i class="fa fa-search"></i></span>
            <input type="text" class="form-control" id="searchInput" placeholder="Search by farmer name or location...">
        </div>
    </div>
    <div class="col-md-4">
        <select class="form-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="scheduled">Scheduled</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
    </div>
</div>

<!-- Verification Requests -->
<div class="table-card">
    <div class="table-card-header d-flex justify-content-between align-items-center">
        <div>
            <h5>Verification Requests (<span id="requestCount">0</span>)</h5>
            <p class="text-muted mb-0">Review and manage MRV verification requests from farmers</p>
        </div>
        <button @click="loadData" class="btn btn-sm btn-outline-primary">
            <i class="fa fa-rotate"></i> Refresh
        </button>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Farmer</th>
                    <th>Request ID</th>
                    <th>Carbon Claims</th>
                    <th>Farm Size</th>
                    <th>Evidence Files</th>
                    <th>Submitted</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="requestsTableBody">
                <tr>
                    <td colspan="9" class="text-center text-muted">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set up axios with authentication token
    const token = localStorage.getItem('token');
    if (token) {
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
    } else {
        // Redirect to login if no token
        window.location.href = '/login';
        return;
    }

    loadData();

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterRequests();
    });

    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        filterRequests();
    });

    async function loadData() {
        try {
            // Load verification queue
            const queueResponse = await axios.get('/api/verifier/queue');
            const requests = queueResponse.data.data.queue || [];

            // Load my verifications for stats
            const myResponse = await axios.get('/api/verifier/my-verifications');
            const myVerifications = myResponse.data.data.verifications || [];

            updateDashboard(requests, myVerifications);
            updateRequestsTable(requests);

        } catch (error) {
            console.error('Error loading data:', error);
            showError('Không thể tải dữ liệu dashboard');
        }
    }

    function updateDashboard(requests, myVerifications) {
        const totalRequests = requests.length;
        const pendingCount = requests.filter(r => r.status === 'submitted').length;
        const scheduledCount = myVerifications.filter(v => v.verification_status === 'pending').length;
        const completedCount = myVerifications.filter(v => v.verification_status === 'approved').length;

        // Calculate total carbon claims
        const carbonClaims = requests.reduce((sum, r) => {
            return sum + (r.estimated_carbon_credits || 0);
        }, 0);

        document.getElementById('totalRequests').textContent = totalRequests;
        document.getElementById('pendingCount').textContent = `${pendingCount} pending review`;
        document.getElementById('scheduledVisits').textContent = scheduledCount;
        document.getElementById('completedCount').textContent = completedCount;
        document.getElementById('carbonClaims').textContent = carbonClaims.toFixed(1);
        document.getElementById('requestCount').textContent = totalRequests;
    }

    function updateRequestsTable(requests) {
        const tbody = document.getElementById('requestsTableBody');

        if (requests.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">Không có verification requests</td></tr>';
            return;
        }

        tbody.innerHTML = requests.map(request => `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-2" style="width: 35px; height: 35px; font-size: 12px;">
                            ${getInitials(request.user?.full_name || 'Unknown')}
                        </div>
                        <div>
                            <div class="fw-bold">${request.user?.full_name || 'Unknown'}</div>
                            <small class="text-muted">${request.farm_profile?.location || 'Unknown Location'}</small>
                        </div>
                    </div>
                </td>
                <td><span class="badge bg-secondary">VR${String(request.id).padStart(3, '0')}</span></td>
                <td>${(request.estimated_carbon_credits || 0).toFixed(1)} tCO₂e</td>
                <td>${(request.farm_profile?.total_area_hectares || 0).toFixed(1)} ha</td>
                <td>${request.evidence_files_count || 0} files</td>
                <td>${formatDate(request.created_at)}</td>
                <td>
                    <span class="badge ${getPriorityClass(request.priority || 'medium')}">
                        ${request.priority || 'Medium'}
                    </span>
                </td>
                <td>
                    <span class="badge ${getStatusClass(request.status)}">
                        ${getStatusText(request.status)}
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary" onclick="reviewRequest(${request.id})">
                            <i class="fa fa-eye"></i> Review
                        </button>
                        <button class="btn btn-primary" onclick="scheduleVisit(${request.id})">
                            <i class="fa fa-calendar"></i> Schedule
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function getInitials(name) {
        return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 3);
    }

    function getPriorityClass(priority) {
        const classes = {
            'high': 'bg-danger',
            'medium': 'bg-warning',
            'low': 'bg-success'
        };
        return classes[priority] || 'bg-secondary';
    }

    function getStatusClass(status) {
        const classes = {
            'submitted': 'bg-warning',
            'pending': 'bg-info',
            'in_progress': 'bg-primary',
            'completed': 'bg-success'
        };
        return classes[status] || 'bg-secondary';
    }

    function getStatusText(status) {
        const texts = {
            'submitted': 'Submitted',
            'pending': 'Pending',
            'in_progress': 'In Progress',
            'completed': 'Completed'
        };
        return texts[status] || status;
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }

    function filterRequests() {
        // Implement search and filter logic
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;

        // This would typically filter the loaded data
        console.log('Filtering:', { searchTerm, statusFilter });
    }

    function reviewRequest(requestId) {
        window.location.href = `/verifier/request/${requestId}`;
    }

    function scheduleVisit(requestId) {
        window.location.href = `/verifier/schedule/${requestId}`;
    }

    function showError(message) {
        Swal.fire('Lỗi', message, 'error');
    }
});
</script>
@endsection
