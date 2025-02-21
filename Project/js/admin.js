// Global variables
let bloodGroupChart = null;
let dashboardUpdateInterval = null;

// Show loading overlay
function showLoading() {
    $('.loading-overlay').fadeIn();
}

// Hide loading overlay
function hideLoading() {
    $('.loading-overlay').fadeOut();
}

// Show toast notification
function showToast(title, message, type = 'info') {
    const toast = `
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-${type} text-white">
                <strong class="me-auto">${title}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">${message}</div>
        </div>
    `;
    $('.toast-container').append(toast);
    $('.toast').toast('show');
}

// Handle login form submission
$('#loginForm').on('submit', function(e) {
    e.preventDefault();
    showLoading();

    const username = $('#username').val();
    const password = $('#password').val();

    $.ajax({
        url: '/Project/php/login.php',
        type: 'POST',
        contentType: 'application/json',
        dataType: 'json',
        data: JSON.stringify({ username, password }),
        success: function(response) {
            if (response.status === 'success') {
                showToast('Success', 'Login successful', 'success');
                $('.login-container').fadeOut(function() {
                    $('.dashboard-container').fadeIn();
                    initializeDashboard();
                });
            } else {
                showToast('Error', response.message || 'Login failed', 'danger');
            }
        },
        error: function(xhr, status, error) {
            showToast('Error', 'An error occurred. Please try again.', 'danger');
            console.error('Login error:', error);
        },
        complete: hideLoading
    });
});

// Handle logout
$('#logoutBtn').on('click', function() {
    showLoading();
    $.ajax({
        url: '/Project/php/logout.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                location.reload();
            }
        },
        error: function() {
            showToast('Error', 'Logout failed', 'danger');
        },
        complete: hideLoading
    });
});

// Initialize dashboard
function initializeDashboard() {
    loadDashboardData();
    // Set up auto-refresh
    if (dashboardUpdateInterval) {
        clearInterval(dashboardUpdateInterval);
    }
    dashboardUpdateInterval = setInterval(loadDashboardData, 30000); // Refresh every 30 seconds
}

// Load dashboard data
function loadDashboardData() {
    $.ajax({
        url: '/Project/php/get_dashboard_data.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.data) {
                const dashboardContent = `
                    <div class="row">
                        <div class="col-md-3 mb-4">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase mb-1">Total Donors</h6>
                                            <h2 class="mb-0">${response.data.counts.donors}</h2>
                                        </div>
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase mb-1">Blood Requests</h6>
                                            <h2 class="mb-0">${response.data.counts.requests}</h2>
                                        </div>
                                        <i class="fas fa-hand-holding-medical fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card bg-danger text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-uppercase mb-1">Emergency Cases</h6>
                                            <h2 class="mb-0">${response.data.counts.emergency}</h2>
                                        </div>
                                        <i class="fas fa-ambulance fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Blood Group Distribution</h5>
                                    <canvas id="bloodGroupChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Recent Activity</h5>
                                    <div class="activity-list">
                                        ${response.data.recent_activity.map(activity => `
                                            <div class="activity-item">
                                                <i class="fas fa-${activity.type === 'donor' ? 'user-plus' : 
                                                                   activity.type === 'request' ? 'hand-holding-medical' : 
                                                                   'ambulance'} text-${activity.type === 'donor' ? 'success' : 
                                                                                      activity.type === 'request' ? 'primary' : 
                                                                                      'danger'}"></i>
                                                <span>${activity.message}</span>
                                                <small class="text-muted">${activity.time}</small>
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('#mainContent').html(dashboardContent);
                
                // Initialize blood group chart
                const ctx = document.getElementById('bloodGroupChart').getContext('2d');
                const labels = Object.keys(response.data.blood_groups);
                const data = Object.values(response.data.blood_groups);
                
                if (bloodGroupChart) {
                    bloodGroupChart.destroy();
                }
                
                bloodGroupChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: [
                                '#dc3545', // red
                                '#28a745', // green
                                '#ffc107', // yellow
                                '#17a2b8', // cyan
                                '#6c757d', // gray
                                '#fd7e14', // orange
                                '#6f42c1', // purple
                                '#20c997'  // teal
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right'
                            }
                        }
                    }
                });
            } else {
                showToast('Error', response.message || 'Failed to load dashboard data', 'danger');
            }
        },
        error: function(xhr, status, error) {
            showToast('Error', 'Failed to load dashboard data', 'danger');
            console.error('Dashboard error:', error);
        }
    });
}

// Check login status on page load
$(document).ready(function() {
    $.get('/Project/php/check_login.php', function(response) {
        if (response.status === 'success' && response.logged_in) {
            $('.login-container').hide();
            $('.dashboard-container').show();
            initializeDashboard();
        }
    });
});

// Handle sidebar navigation
$('.sidebar a[data-section]').on('click', function(e) {
    e.preventDefault();
    const section = $(this).data('section');
    loadSection(section);
});

function loadSection(section) {
    // Remove active class from all links
    $('.sidebar a').removeClass('active');
    // Add active class to clicked link
    $(`.sidebar a[data-section="${section}"]`).addClass('active');

    // Hide all sections
    $('.section-content').hide();

    // Load and show the selected section
    switch(section) {
        case 'dashboard':
            $('#dashboardSection').show();
            loadDashboardData();
            break;
        case 'donors':
            loadDonorsSection();
            break;
        case 'requests':
            loadRequestsSection();
            break;
        case 'emergency':
            loadEmergencySection();
            break;
    }
}

function loadDonorsSection() {
    $.ajax({
        url: '/Project/php/get_donors.php?full=true',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const content = `
                    <div class="section-content" id="donorsSection">
                        <h2 class="mb-4">Donors Management</h2>
                        <div class="mb-4">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#addDonorModal">
                                <i class="fas fa-plus"></i> Add New Donor
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Blood Group</th>
                                        <th>Age</th>
                                        <th>Gender</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${response.data.map(donor => `
                                        <tr>
                                            <td>${donor.name}</td>
                                            <td><span class="badge bg-danger">${donor.blood_group}</span></td>
                                            <td>${donor.age}</td>
                                            <td>${donor.gender || 'N/A'}</td>
                                            <td>${donor.mobile_no}</td>
                                            <td>${donor.address}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="editDonor(${donor.id})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteDonor(${donor.id})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                $('#mainContent').html(content);
            } else {
                showToast('Error', response.message || 'Failed to load donors', 'danger');
            }
        },
        error: function(xhr, status, error) {
            showToast('Error', 'Failed to load donors list', 'danger');
            console.error('Donors error:', error);
            $('#mainContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Error loading donors: ${error}
                </div>
            `);
        }
    });
}

function loadRequestsSection() {
    $.ajax({
        url: '/Project/php/get_requests.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const content = `
                    <div class="section-content" id="requestsSection">
                        <h2 class="mb-4">Blood Requests</h2>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Blood Required</th>
                                        <th>Blood Type</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${response.data.map(request => `
                                        <tr class="${request.status === 'Urgent' ? 'table-danger' : ''}">
                                            <td>${request.name}</td>
                                            <td>${request.phone}</td>
                                            <td>${request.email}</td>
                                            <td>${request.blood_required}</td>
                                            <td><span class="badge bg-danger">${request.blood_type}</span></td>
                                            <td>${request.message}</td>
                                            <td>
                                                <span class="badge bg-${request.status === 'Urgent' ? 'danger' : 'primary'}">
                                                    ${request.status}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success" onclick="approveRequest(${request.id})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteRequest(${request.id})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                $('#mainContent').html(content);
            } else {
                showToast('Error', response.message || 'Failed to load requests', 'danger');
            }
        },
        error: function(xhr, status, error) {
            showToast('Error', 'Failed to load requests list', 'danger');
            console.error('Requests error:', error);
            $('#mainContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Error loading requests: ${error}
                </div>
            `);
        }
    });
}

function loadEmergencySection() {
    $.ajax({
        url: '/Project/php/get_emergency.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const content = `
                    <div class="section-content" id="emergencySection">
                        <h2 class="mb-4">Emergency Requests</h2>
                        <div class="alert alert-danger">
                            <i class="fas fa-ambulance"></i>
                            <strong>Note:</strong> These requests require immediate attention!
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Blood Required</th>
                                        <th>Blood Type</th>
                                        <th>Hospital</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${response.data.map(emergency => `
                                        <tr>
                                            <td>${emergency.name}</td>
                                            <td>${emergency.phone}</td>
                                            <td>${emergency.email}</td>
                                            <td>${emergency.blood_required}</td>
                                            <td><span class="badge bg-danger">${emergency.blood_type}</span></td>
                                            <td>${emergency.hospital}</td>
                                            <td>
                                                <span class="badge bg-danger">URGENT</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="findDonors(${emergency.id}, '${emergency.blood_type}')">
                                                    <i class="fas fa-search"></i> Find Donors
                                                </button>
                                                <button class="btn btn-sm btn-success" onclick="markCompleted(${emergency.id})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteEmergency(${emergency.id})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                $('#mainContent').html(content);
            } else {
                showToast('Error', response.message || 'Failed to load emergency requests', 'danger');
            }
        },
        error: function(xhr, status, error) {
            showToast('Error', 'Failed to load emergency requests', 'danger');
            console.error('Emergency error:', error);
            $('#mainContent').html(`
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Error loading emergency requests: ${error}
                </div>
            `);
        }
    });
}

function findMatchingDonors(bloodType) {
    $('#matchingDonorsList').html(`
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading donors...</span>
            </div>
        </div>
    `);

    $.get(`/Project/php/get_donors.php?blood_type=${bloodType}&available=true`, function(response) {
        const donors = JSON.parse(response);
        let content = '';
        
        if (donors.length === 0) {
            content = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    No matching donors found for blood type ${bloodType}.
                </div>
            `;
        } else {
            content = `
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle mr-2"></i>
                    Found ${donors.length} matching donors for blood type ${bloodType}
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Blood Group</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${donors.map(donor => `
                                <tr>
                                    <td>${donor.name}</td>
                                    <td><span class="badge badge-danger">${donor.blood_group}</span></td>
                                    <td>${donor.age}</td>
                                    <td>${donor.gender}</td>
                                    <td>
                                        <div><i class="fas fa-phone mr-1"></i> ${donor.mobile_no}</div>
                                        <div><i class="fas fa-envelope mr-1"></i> ${donor.email}</div>
                                    </td>
                                    <td>${donor.address}</td>
                                    <td>
                                        <a href="tel:${donor.mobile_no}" class="btn btn-sm btn-success">
                                            <i class="fas fa-phone-alt"></i> Call
                                        </a>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        }
        
        $('#matchingDonorsList').html(content);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        $('#matchingDonorsList').html(`
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle mr-2"></i>
                Error finding donors: ${errorThrown}
            </div>
        `);
    });
}

function loadRecentDonors() {
    $.get('/Project/php/get_donors.php', function(response) {
        const donors = JSON.parse(response);
        const tbody = $('#donorsTable tbody');
        tbody.empty();

        donors.forEach(donor => {
            tbody.append(`
                <tr>
                    <td>${donor.name}</td>
                    <td>${donor.blood_group}</td>
                    <td>${donor.age}</td>
                    <td>${donor.mobile_no}</td>
                    <td>${donor.address}</td>
                </tr>
            `);
        });
    });
}
