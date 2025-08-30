@extends('share.Verifier.master')

@section('title')
    <div>
        <h1 class="mb-1">Request Review</h1>
        <p class="text-muted">Chi tiết và xem xét MRV verification requests</p>
    </div>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <!-- Request Status -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span>Request Status</span>
                <div class="stat-card-icon primary"><i class="fa fa-clipboard-check"></i></div>
            </div>
            <div class="stat-value" id="requestStatus">Pending</div>
            <div class="stat-label">Current status</div>
        </div>
    </div>

    <!-- Carbon Claims -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span>Carbon Claims</span>
                <div class="stat-card-icon success"><i class="fa fa-leaf"></i></div>
            </div>
            <div class="stat-value" id="carbonClaims">0</div>
            <div class="stat-label">tCO₂e claimed</div>
        </div>
    </div>

    <!-- Evidence Files -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span>Evidence Files</span>
                <div class="stat-card-icon info"><i class="fa fa-file-alt"></i></div>
            </div>
            <div class="stat-value" id="evidenceCount">0</div>
            <div class="stat-label">Files submitted</div>
        </div>
    </div>

    <!-- Verification Score -->
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span>Verification Score</span>
                <div class="stat-card-icon warning"><i class="fa fa-star"></i></div>
            </div>
            <div class="stat-value" id="verificationScore">0</div>
            <div class="stat-label">Out of 100</div>
        </div>
    </div>
</div>

<!-- Request Details -->
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <!-- Farmer Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa fa-user me-2"></i>Farmer Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Full Name</label>
                        <p id="farmerName">Loading...</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Phone</label>
                        <p id="farmerPhone">Loading...</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Email</label>
                        <p id="farmerEmail">Loading...</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Location</label>
                        <p id="farmerLocation">Loading...</p>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Farm Profile</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <small class="text-muted">Total Area</small>
                                <p class="mb-0" id="totalArea">Loading...</p>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Rice Area</small>
                                <p class="mb-0" id="riceArea">Loading...</p>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Agroforestry Area</small>
                                <p class="mb-0" id="agroArea">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MRV Declaration Details -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa fa-file-text me-2"></i>MRV Declaration Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Declaration Period</label>
                        <p id="declarationPeriod">Loading...</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Submission Date</label>
                        <p id="submissionDate">Loading...</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Rice Sowing Date</label>
                        <p id="riceSowingDate">Loading...</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Rice Harvest Date</label>
                        <p id="riceHarvestDate">Loading...</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">AWD Cycles</label>
                        <p id="awdCycles">Loading...</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Water Management</label>
                        <p id="waterManagement">Loading...</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Straw Management</label>
                        <p id="strawManagement">Loading...</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tree Density</label>
                        <p id="treeDensity">Loading...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Verification Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa fa-tasks me-2"></i>Verification Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="scheduleFieldVisit()">
                        <i class="fa fa-calendar me-2"></i>Schedule Field Visit
                    </button>
                    <button class="btn btn-info" onclick="requestAdditionalEvidence()">
                        <i class="fa fa-file-plus me-2"></i>Request Evidence
                    </button>
                    <button class="btn btn-success" onclick="approveRequest()">
                        <i class="fa fa-check me-2"></i>Approve Request
                    </button>
                    <button class="btn btn-warning" onclick="requestRevision()">
                        <i class="fa fa-edit me-2"></i>Request Revision
                    </button>
                    <button class="btn btn-danger" onclick="rejectRequest()">
                        <i class="fa fa-times me-2"></i>Reject Request
                    </button>
                </div>
            </div>
        </div>

        <!-- Verification Notes -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa fa-sticky-note me-2"></i>Verification Notes</h5>
            </div>
            <div class="card-body">
                <textarea class="form-control" id="verificationNotes" rows="4" placeholder="Add your verification notes here..."></textarea>
                <button class="btn btn-outline-primary btn-sm mt-2" onclick="saveNotes()">
                    <i class="fa fa-save me-1"></i>Save Notes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Evidence Files -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fa fa-images me-2"></i>Evidence Files</h5>
    </div>
    <div class="card-body">
        <div id="evidenceFiles" class="row g-3">
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Analysis Results -->
<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0"><i class="fa fa-robot me-2"></i>AI Analysis Results</h5>
    </div>
    <div class="card-body">
        <div id="aiAnalysis" class="row g-3">
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
let currentRequest = null;
let requestId = null;

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

    // Get request ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    requestId = urlParams.get('id') || window.location.pathname.split('/').pop();

    if (requestId) {
        loadRequestDetails(requestId);
    } else {
        showError('Không tìm thấy Request ID');
    }
});

async function loadRequestDetails(requestId) {
    try {
        // Load MRV declaration details
        const declarationResponse = await axios.get(`/api/mrv-declarations/${requestId}`);
        const declaration = declarationResponse.data.data;

        // Load farmer details
        const farmerResponse = await axios.get(`/api/users/${declaration.user_id}`);
        const farmer = farmerResponse.data.data;

        // Load farm profile
        const farmResponse = await axios.get(`/api/farm-profiles/${declaration.farm_profile_id}`);
        const farmProfile = farmResponse.data.data;

        // Load evidence files
        const evidenceResponse = await axios.get(`/api/evidence-files?mrv_declaration_id=${requestId}`);
        const evidenceFiles = evidenceResponse.data.data;

        // Load AI analysis results
        const aiResponse = await axios.get(`/api/ai/analyses?mrv_declaration_id=${requestId}`);
        const aiResults = aiResponse.data.data;

        currentRequest = {
            declaration,
            farmer,
            farmProfile,
            evidenceFiles,
            aiResults
        };

        updateUI();

    } catch (error) {
        console.error('Error loading request details:', error);
        showError('Không thể tải chi tiết request');
    }
}

function updateUI() {
    if (!currentRequest) return;

    const { declaration, farmer, farmProfile, evidenceFiles, aiResults } = currentRequest;

    // Update farmer information
    document.getElementById('farmerName').textContent = farmer.full_name || 'N/A';
    document.getElementById('farmerPhone').textContent = farmer.phone || 'N/A';
    document.getElementById('farmerEmail').textContent = farmer.email || 'N/A';
    document.getElementById('farmerLocation').textContent = farmer.address || 'N/A';

    // Update farm profile
    document.getElementById('totalArea').textContent = `${farmProfile.total_area_hectares || 0} ha`;
    document.getElementById('riceArea').textContent = `${farmProfile.rice_area_hectares || 0} ha`;
    document.getElementById('agroArea').textContent = `${farmProfile.agroforestry_area_hectares || 0} ha`;

    // Update MRV declaration
    document.getElementById('declarationPeriod').textContent = declaration.declaration_period || 'N/A';
    document.getElementById('submissionDate').textContent = formatDate(declaration.created_at);
    document.getElementById('riceSowingDate').textContent = formatDate(declaration.rice_sowing_date);
    document.getElementById('riceHarvestDate').textContent = formatDate(declaration.rice_harvest_date);
    document.getElementById('awdCycles').textContent = declaration.awd_cycles_per_season || 'N/A';
    document.getElementById('waterManagement').textContent = declaration.water_management_method || 'N/A';
    document.getElementById('strawManagement').textContent = declaration.straw_management || 'N/A';
    document.getElementById('treeDensity').textContent = `${declaration.tree_density_per_hectare || 0} trees/ha`;

    // Update stats
    document.getElementById('carbonClaims').textContent = (declaration.estimated_carbon_credits || 0).toFixed(1);
    document.getElementById('evidenceCount').textContent = evidenceFiles.length;
    document.getElementById('verificationScore').textContent = (declaration.mrv_reliability_score || 0).toFixed(1);

    // Update evidence files
    updateEvidenceFiles(evidenceFiles);

    // Update AI analysis
    updateAIAnalysis(aiResults);
}

function updateEvidenceFiles(files) {
    const container = document.getElementById('evidenceFiles');

    if (files.length === 0) {
        container.innerHTML = '<div class="col-12 text-center text-muted">Không có evidence files</div>';
        return;
    }

    container.innerHTML = files.map(file => `
        <div class="col-md-4 col-lg-3">
            <div class="card h-100">
                <img src="${file.file_url}" class="card-img-top" alt="${file.file_type}" style="height: 150px; object-fit: cover;">
                <div class="card-body">
                    <h6 class="card-title">${file.file_type}</h6>
                    <p class="card-text small">${file.description || 'No description'}</p>
                    <small class="text-muted">${formatDate(file.capture_timestamp)}</small>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewFile('${file.file_url}')">
                        <i class="fa fa-eye"></i> View
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function updateAIAnalysis(results) {
    const container = document.getElementById('aiAnalysis');

    if (!results || results.length === 0) {
        container.innerHTML = '<div class="col-12 text-center text-muted">Không có AI analysis results</div>';
        return;
    }

    container.innerHTML = results.map(result => `
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">${result.analysis_type}</h6>
                    <div class="mb-2">
                        <strong>Confidence:</strong>
                        <span class="badge bg-${getConfidenceClass(result.confidence_score)}">
                            ${(result.confidence_score || 0).toFixed(1)}%
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Findings:</strong>
                        <p class="mb-0">${result.analysis_findings || 'No findings'}</p>
                    </div>
                    <small class="text-muted">Analyzed: ${formatDate(result.analysis_timestamp)}</small>
                </div>
            </div>
        </div>
    `).join('');
}

function getConfidenceClass(score) {
    if (score >= 80) return 'success';
    if (score >= 60) return 'warning';
    return 'danger';
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

// Verification Actions
function scheduleFieldVisit() {
    if (!currentRequest) return;

    Swal.fire({
        title: 'Schedule Field Visit',
        text: `Schedule field visit for ${currentRequest.farmer.full_name}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Schedule',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to schedule page
            window.location.href = `/verifier/schedule?farmer_id=${currentRequest.farmer.id}&request_id=${requestId}`;
        }
    });
}

function requestAdditionalEvidence() {
    if (!currentRequest) return;

    Swal.fire({
        title: 'Request Additional Evidence',
        input: 'textarea',
        inputLabel: 'Evidence Requirements',
        inputPlaceholder: 'Describe what additional evidence is needed...',
        showCancelButton: true,
        confirmButtonText: 'Send Request',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // Here you would send the request to backend
            console.log('Requesting evidence:', result.value);
            showSuccess('Evidence request sent successfully');
        }
    });
}

function approveRequest() {
    if (!currentRequest) return;

    Swal.fire({
        title: 'Approve Request',
        text: 'Are you sure you want to approve this MRV declaration?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Approve',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Here you would send approval to backend
            console.log('Approving request:', requestId);
            showSuccess('Request approved successfully');
        }
    });
}

function requestRevision() {
    if (!currentRequest) return;

    Swal.fire({
        title: 'Request Revision',
        input: 'textarea',
        inputLabel: 'Revision Requirements',
        inputPlaceholder: 'Describe what needs to be revised...',
        showCancelButton: true,
        confirmButtonText: 'Send Request',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // Here you would send revision request to backend
            console.log('Requesting revision:', result.value);
            showSuccess('Revision request sent successfully');
        }
    });
}

function rejectRequest() {
    if (!currentRequest) return;

    Swal.fire({
        title: 'Reject Request',
        input: 'textarea',
        inputLabel: 'Rejection Reason',
        inputPlaceholder: 'Provide reason for rejection...',
        showCancelButton: true,
        confirmButtonText: 'Reject',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // Here you would send rejection to backend
            console.log('Rejecting request:', result.value);
            showSuccess('Request rejected successfully');
        }
    });
}

function saveNotes() {
    const notes = document.getElementById('verificationNotes').value;
    if (!notes.trim()) {
        showError('Vui lòng nhập verification notes');
        return;
    }

    // Here you would save notes to backend
    console.log('Saving notes:', notes);
    showSuccess('Notes saved successfully');
}

function viewFile(fileUrl) {
    window.open(fileUrl, '_blank');
}

function showError(message) {
    Swal.fire('Lỗi', message, 'error');
}

function showSuccess(message) {
    Swal.fire('Thành công', message, 'success');
}
</script>
@endsection


