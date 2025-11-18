<template>
  <div class="py-4 container-fluid">
    <!-- Loading State -->
    <div v-if="isLoading" class="d-flex justify-content-center align-items-center" style="min-height: 400px;">
      <div class="text-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading dashboard data...</p>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div v-else>
    <!-- Statistics Cards Row -->
    <div class="row mb-4">
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow border-0 bg-gradient-primary">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-2">
                  <i class="fas fa-file-alt text-white me-2" style="font-size: 1.2rem;"></i>
                  <h6 class="text-white mb-0 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    Total RPUS Count
                  </h6>
                </div>
                <h2 class="text-white font-weight-bold mb-0" style="font-size: 2.5rem;">
                  {{ dashboardData.totalRpus || 0 }}
                </h2>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow border-0 bg-gradient-danger">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-2">
                  <i class="fas fa-times-circle text-white me-2" style="font-size: 1.2rem;"></i>
                  <h6 class="text-white mb-0 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    Total Cancelled
                  </h6>
                </div>
                <h2 class="text-white font-weight-bold mb-0" style="font-size: 2.5rem;">
                  {{ dashboardData.totalCancelled || 0 }}
                </h2>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow border-0 bg-gradient-success">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-2">
                  <i class="fas fa-plus-circle text-white me-2" style="font-size: 1.2rem;"></i>
                  <h6 class="text-white mb-0 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    Total Newly Declared
                  </h6>
                </div>
                <h2 class="text-white font-weight-bold mb-0" style="font-size: 2.5rem;">
                  {{ dashboardData.totalNewlyDeclared || 0 }}
                </h2>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card shadow border-0 bg-gradient-warning">
          <div class="card-body p-4">
            <div class="d-flex align-items-center">
              <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-2">
                  <i class="fas fa-home text-white me-2" style="font-size: 1.2rem;"></i>
                  <h6 class="text-white mb-0 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    Total Property
                  </h6>
                </div>
                <h2 class="text-white font-weight-bold mb-0" style="font-size: 2.5rem;">
                  {{ dashboardData.totalProperty || 0 }}
                </h2>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions Section -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card shadow border-0">
          <div class="card-header bg-light border-0">
            <h6 class="text-dark font-weight-bold mb-0">
              <i class="fas fa-bolt me-2 text-warning"></i>
              Quick Actions
            </h6>
          </div>
          <div class="card-body p-4">
            <div class="row">
              <!-- Pending Approvals -->
              <div class="col-lg-3 col-md-6 mb-3">
                <div class="quick-action-card bg-warning-light p-3 rounded-3 h-100" @click="goToPendingApprovals">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="quick-action-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-clock" style="font-size: 1.2rem;"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-1 text-dark font-weight-bold">Pending Approvals</h6>
                      <p class="mb-1 text-muted small">{{ pendingApprovalsCount }} requests waiting</p>
                      <span class="badge bg-warning text-dark">Review Now</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Validation Queue -->
              <div class="col-lg-3 col-md-6 mb-3">
                <div class="quick-action-card bg-info-light p-3 rounded-3 h-100" @click="openTransactionModal">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="quick-action-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-cogs" style="font-size: 1.2rem;"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-1 text-dark font-weight-bold">Quick Actions</h6>
                      <p class="mb-1 text-muted small">Validate, approve, or reject requests</p>
                      <span class="badge bg-info text-white">Manage</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- New Requests -->
              <div class="col-lg-3 col-md-6 mb-3">
                <div class="quick-action-card bg-success-light p-3 rounded-3 h-100" @click="goToNewRequests">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="quick-action-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-plus-circle" style="font-size: 1.2rem;"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-1 text-dark font-weight-bold">New Requests</h6>
                      <p class="mb-1 text-muted small">{{ newRequestsCount }} new submissions</p>
                      <span class="badge bg-success text-white">Process</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Create New Assessment -->
              <div class="col-lg-3 col-md-6 mb-3">
                <div class="quick-action-card bg-primary-light p-3 rounded-3 h-100" @click="createNewAssessment">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="quick-action-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-file-plus" style="font-size: 1.2rem;"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-1 text-dark font-weight-bold">New Assessment</h6>
                      <p class="mb-1 text-muted small">Create new property assessment</p>
                      <span class="badge bg-primary text-white">Create</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Generate Certificates -->
              <div class="col-lg-3 col-md-6 mb-3">
                <div class="quick-action-card bg-warning-light p-3 rounded-3 h-100" @click="openCertificateModal">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="quick-action-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center">
                        <i class="fas fa-certificate" style="font-size: 1.2rem;"></i>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-1 text-dark font-weight-bold">Generate Certificates</h6>
                      <p class="mb-1 text-muted small">Create official property certificates</p>
                      <span class="badge bg-warning text-dark">Generate</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Filter Buttons -->
            <div class="row mt-3">
              <div class="col-12">
                <div class="d-flex flex-wrap gap-2">
                  <button class="btn btn-outline-primary btn-sm" @click="filterByStatus('pending')">
                    <i class="fas fa-filter me-1"></i>
                    Show Pending Only
                  </button>
                  <button class="btn btn-outline-success btn-sm" @click="filterByStatus('approved')">
                    <i class="fas fa-check me-1"></i>
                    Show Approved
                  </button>
                  <button class="btn btn-outline-danger btn-sm" @click="filterByStatus('rejected')">
                    <i class="fas fa-times me-1"></i>
                    Show Rejected
                  </button>
                  <button class="btn btn-outline-warning btn-sm" @click="filterByStatus('under-review')">
                    <i class="fas fa-eye me-1"></i>
                    Under Review
                  </button>
                  <button class="btn btn-outline-secondary btn-sm" @click="clearFilters">
                    <i class="fas fa-refresh me-1"></i>
                    Clear Filters
                  </button>
                  <button class="btn btn-outline-info btn-sm" @click="openCertificateModal">
                    <i class="fas fa-certificate me-1"></i>
                    Generate Certificates
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
      <!-- Municipality Cards Section -->
      <div class="col-lg-7 mb-4">
        <div class="row">
          <div v-for="municipality in municipalities" :key="municipality.id" class="col-md-6 mb-4">
            <div class="card shadow border-0 h-100">
              <div class="card-body text-center p-4">
                <div class="mb-3">
                  <div class="mx-auto d-flex align-items-center justify-content-center" 
                       style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; border: 3px solid #667eea;">
                    <img :src="getMunicipalityImage(municipality.name)" 
                         :alt="municipality.name"
                         style="width: 100%; height: 100%; object-fit: cover;"
                         @error="onImageError">
                  </div>
                </div>
                <h6 class="text-dark font-weight-bold mb-1" style="font-size: 0.9rem;">
                  <i class="fas fa-map-pin me-1 text-primary"></i>
                  {{ municipality.name }}
                </h6>
                <p class="text-muted mb-2" style="font-size: 0.75rem;">Total Properties</p>
                <h3 class="text-primary font-weight-bold mb-0" style="font-size: 2rem;">
                  {{ municipality.totalProperties || 0 }}
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="col-lg-5">
        <!-- Assets Chart -->
        <div class="card shadow border-0 mb-4">
          <div class="card-header bg-light border-0 pb-0">
            <h6 class="text-dark font-weight-bold mb-0">
              <i class="fas fa-chart-bar me-2 text-primary"></i>
              Assets per Municipality
            </h6>
            <div class="d-flex mt-2">
              <div class="me-3">
                <span class="badge" style="background-color: #26D0CE; color: white;">Land</span>
              </div>
              <div class="me-3">
                <span class="badge" style="background-color: #1A73E8; color: white;">Buildings</span>
              </div>
              <div>
                <span class="badge" style="background-color: #FB8500; color: white;">Machineries</span>
              </div>
            </div>
          </div>
          <div class="card-body">
            <canvas id="assetsChart" height="200"></canvas>
          </div>
        </div>

        <!-- Yearly Chart -->
        <div class="card shadow border-0">
          <div class="card-header bg-light border-0 pb-0">
            <h6 class="text-dark font-weight-bold mb-0">
              <i class="fas fa-chart-line me-2 text-success"></i>
              Yearly Records by Property Type
            </h6>
            <div class="d-flex mt-2">
              <div class="me-3">
                <span class="badge" style="background-color: #26D0CE; color: white;">Land</span>
              </div>
              <div class="me-3">
                <span class="badge" style="background-color: #1A73E8; color: white;">Buildings</span>
              </div>
              <div>
                <span class="badge" style="background-color: #FB8500; color: white;">Machineries</span>
              </div>
            </div>
          </div>
          <div class="card-body">
            <canvas id="yearlyChart" height="200"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mt-4">
      <div class="col-12">
        <div class="card shadow border-0">
          <div class="card-header bg-light border-0">
            <div class="d-flex align-items-center justify-content-between">
              <h6 class="text-dark font-weight-bold mb-0">
                <i class="fas fa-clock me-2 text-info"></i>
                Recent Assessment Activities
              </h6>
              <router-link to="/requests" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-eye me-1"></i>
                View All
              </router-link>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th class="border-0 ps-4">ARP No</th>
                    <th class="border-0">Owner</th>
                    <th class="border-0">Municipality</th>
                    <th class="border-0">Type</th>
                    <th class="border-0">Status</th>
                    <th class="border-0 pe-4">Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="activity in recentActivities" :key="activity.id">
                    <td class="ps-4">
                      <strong class="text-primary">{{ activity.arpNo }}</strong>
                    </td>
                    <td>{{ activity.ownerName }}</td>
                    <td>{{ activity.municipality }}</td>
                    <td>
                      <span class="badge bg-info text-white">{{ activity.type }}</span>
                    </td>
                    <td>
                      <span :class="getStatusBadgeClass(activity.status)" class="badge">
                        {{ activity.status }}
                      </span>
                    </td>
                    <td class="pe-4">{{ formatDate(activity.createdAt) }}</td>
                  </tr>
                  <tr v-if="recentActivities.length === 0">
                    <td colspan="6" class="text-center py-4 text-muted">
                      No recent activities
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div> <!-- End of v-else dashboard content -->

    <!-- Transaction Status Modal -->
    <div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="transactionModalLabel">
              <i class="fas fa-tasks me-2"></i>
              Quick Transaction Actions
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="transactionLoading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Processing transaction...</p>
            </div>
            
            <div v-else>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="card h-100 transaction-action-card" @click="openStatusUpdateModal('validate')">
                    <div class="card-body text-center">
                      <div class="mb-3">
                        <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                          <i class="fas fa-check-circle" style="font-size: 1.5rem;"></i>
                        </div>
                      </div>
                      <h6 class="card-title">Validate Requests</h6>
                      <p class="card-text text-muted small">Review and validate pending assessment requests</p>
                      <span class="badge bg-info">{{ validationQueueCount }} pending</span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6 mb-3">
                  <div class="card h-100 transaction-action-card" @click="openStatusUpdateModal('approve')">
                    <div class="card-body text-center">
                      <div class="mb-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                          <i class="fas fa-thumbs-up" style="font-size: 1.5rem;"></i>
                        </div>
                      </div>
                      <h6 class="card-title">Approve Requests</h6>
                      <p class="card-text text-muted small">Approve validated assessment requests</p>
                      <span class="badge bg-success">{{ pendingApprovalsCount }} waiting</span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6 mb-3">
                  <div class="card h-100 transaction-action-card" @click="openStatusUpdateModal('reject')">
                    <div class="card-body text-center">
                      <div class="mb-3">
                        <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                          <i class="fas fa-times-circle" style="font-size: 1.5rem;"></i>
                        </div>
                      </div>
                      <h6 class="card-title">Reject Requests</h6>
                      <p class="card-text text-muted small">Reject requests that don't meet criteria</p>
                      <span class="badge bg-danger">Action</span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6 mb-3">
                  <div class="card h-100 transaction-action-card" @click="goToAllRequests">
                    <div class="card-body text-center">
                      <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                          <i class="fas fa-list" style="font-size: 1.5rem;"></i>
                        </div>
                      </div>
                      <h6 class="card-title">View All Requests</h6>
                      <p class="card-text text-muted small">Browse all assessment requests</p>
                      <span class="badge bg-primary">{{ quickStats.totalRequests }} total</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Update Confirmation Modal -->
    <div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="statusUpdateModalLabel">
              Confirm Status Update
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="statusUpdateData.action">
              <p><strong>Action:</strong> {{ capitalize(statusUpdateData.action) }} Request</p>
              <p><strong>This will {{ statusUpdateData.action }} the selected request(s).</strong></p>
              
              <div class="mb-3">
                <label for="remarksTextarea" class="form-label">Remarks (Optional)</label>
                <textarea 
                  class="form-control" 
                  id="remarksTextarea" 
                  rows="3" 
                  v-model="statusUpdateData.remarks"
                  placeholder="Enter any additional comments or reasons...">
                </textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button 
              type="button" 
              class="btn" 
              :class="getActionButtonClass(statusUpdateData.action)"
              @click="confirmStatusUpdate"
              :disabled="transactionLoading">
              <span v-if="transactionLoading" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ capitalize(statusUpdateData.action) }} Request
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Certificate Generation Modal -->
    <div class="modal fade" id="certificateModal" tabindex="-1" aria-labelledby="certificateModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="certificateModalLabel">
              <i class="fas fa-certificate me-2"></i>
              Generate Certificates
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="certificateLoading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Generating certificate...</span>
              </div>
              <p class="mt-2">Generating certificate...</p>
            </div>
            
            <div v-else>
              <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                Generate official certificates for approved assessment requests. Certificates will be downloaded as PDF files.
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <div class="card h-100 certificate-type-card" @click="generateCertificate('ownership')">
                    <div class="card-body text-center">
                      <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                          <i class="fas fa-home" style="font-size: 1.5rem;"></i>
                        </div>
                      </div>
                      <h6 class="card-title">Property Ownership Certificate</h6>
                      <p class="card-text text-muted small">Official certification of property ownership with complete property details</p>
                      <button class="btn btn-primary btn-sm">
                        <i class="fas fa-download me-1"></i>
                        Generate
                      </button>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6 mb-3">
                  <div class="card h-100 certificate-type-card" @click="generateCertificate('tax_declaration')">
                    <div class="card-body text-center">
                      <div class="mb-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                          <i class="fas fa-file-invoice-dollar" style="font-size: 1.5rem;"></i>
                        </div>
                      </div>
                      <h6 class="card-title">Tax Declaration Certificate</h6>
                      <p class="card-text text-muted small">Official tax declaration certification for legal and administrative purposes</p>
                      <button class="btn btn-success btn-sm">
                        <i class="fas fa-download me-1"></i>
                        Generate
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mt-4">
                <h6 class="mb-3">Recent Approved Requests (Available for Certificate Generation)</h6>
                <div class="table-responsive" style="max-height: 300px;">
                  <table class="table table-sm table-hover">
                    <thead class="table-light sticky-top">
                      <tr>
                        <th>Request ID</th>
                        <th>Owner Name</th>
                        <th>Property Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="request in approvedRequests" :key="request.id" class="cursor-pointer">
                        <td>{{ request.id }}</td>
                        <td>{{ request.owner_name || request.ownerName }}</td>
                        <td>{{ request.property_location || request.propertyLocation }}</td>
                        <td>
                          <span class="badge bg-success">{{ request.status || request.requestStatus }}</span>
                        </td>
                        <td>
                          <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary btn-sm" @click="generateRequestCertificate(request.id, 'ownership')" title="Property Certificate">
                              <i class="fas fa-home"></i>
                            </button>
                            <button class="btn btn-outline-success btn-sm" @click="generateRequestCertificate(request.id, 'tax_declaration')" title="Tax Certificate">
                              <i class="fas fa-file-invoice-dollar"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                      <tr v-if="approvedRequests.length === 0">
                        <td colspan="5" class="text-center text-muted py-3">
                          <i class="fas fa-info-circle me-2"></i>
                          No approved requests available for certificate generation
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
import AuthService from '@/services/auth.js';
import { Modal } from 'bootstrap';
import Swal from 'sweetalert2';

Chart.register(...registerables);

export default {
  name: 'Dashboard',
  data() {
    return {
      isLoading: true,
      dashboardData: {
        totalRpus: 0,
        totalCancelled: 0,
        totalNewlyDeclared: 0,
        totalProperty: 0
      },
      municipalities: [],
      recentActivities: [],
      assetsChart: null,
      yearlyChart: null,
      chartData: [],
      quickStats: {
        pendingApprovals: 0,
        validationQueue: 0,
        newRequests: 0,
        totalRequests: 0
      },
      transactionLoading: false,
      statusUpdateData: {
        action: '',
        remarks: '',
        requestId: null
      },
      certificateLoading: false,
      approvedRequests: []
    };
  },
  computed: {
    pendingApprovalsCount() {
      return this.quickStats.pendingApprovals || 
             this.recentActivities.filter(activity => 
               activity.status === 'Pending' || activity.status === 'Under Review'
             ).length;
    },
    validationQueueCount() {
      return this.quickStats.validationQueue || 
             this.recentActivities.filter(activity => 
               activity.status === 'Pending'
             ).length;
    },
    newRequestsCount() {
      return this.quickStats.newRequests || 
             this.recentActivities.filter(activity => {
               const today = new Date();
               const activityDate = new Date(activity.created_at);
               const diffTime = Math.abs(today - activityDate);
               const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
               return diffDays <= 7; // Within last 7 days
             }).length;
    }
  },
  async created() {
    this.checkAuthentication();
    try {
      this.isLoading = true;
      await Promise.all([
        this.fetchDashboardData(),
        this.fetchChartData(),
        this.fetchRecentActivities(),
        this.fetchQuickStats()
      ]);
    } finally {
      this.isLoading = false;
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.initializeCharts();
    });
  },
  beforeUnmount() {
    if (this.assetsChart) {
      this.assetsChart.destroy();
    }
    if (this.yearlyChart) {
      this.yearlyChart.destroy();
    }
  },
  methods: {
    getMunicipalityImage(municipalityName) {
      const imageMap = {
        'Maria Aurora': '/images/Maria Aurora.png',
        'San Luis': '/images/SAN LUIS.png', 
        'Baler': '/images/BALER.jpg',
        'Dipaculao': '/images/DIPACULAO.jpg',
        'Dingalan': '/images/DINGALAN.jpg',
        'Dinalungan': '/images/DINALUNGAN.jpg',
        'Casiguran': '/images/CASIGURAN.jpg',
        'Dilasag': '/images/DILASAG.jpg'
      };
      
      return imageMap[municipalityName] || '/images/PROVINCIAL OF AURORA.jpg';
    },
    
    onImageError(event) {
      // Fallback to default provincial image if municipality image fails to load
      event.target.src = '/images/PROVINCIAL OF AURORA.jpg';
    },
    capitalize(value) {
      if (!value) return '';
      return value.charAt(0).toUpperCase() + value.slice(1);
    },

    // Quick Action Methods
    goToPendingApprovals() {
      this.$router.push({ 
        name: 'Requests', 
        query: { status: 'pending' } 
      });
    },
    
    goToValidationQueue() {
      this.$router.push({ 
        name: 'Requests', 
        query: { status: 'under-review' } 
      });
    },
    
    goToNewRequests() {
      const oneWeekAgo = new Date();
      oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
      this.$router.push({ 
        name: 'Requests', 
        query: { 
          date_from: oneWeekAgo.toISOString().split('T')[0],
          date_to: new Date().toISOString().split('T')[0]
        } 
      });
    },
    
    createNewAssessment() {
      this.$router.push({ name: 'NewAssessment' });
    },
    
    filterByStatus(status) {
      this.$router.push({ 
        name: 'Requests', 
        query: { status: status } 
      });
    },
    
    clearFilters() {
      this.$router.push({ name: 'Requests' });
    },

    async fetchQuickStats() {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await this.$api.get('/e_assessment/api/v1/transactions/quick-stats', { headers });
        
        if (response.data.success) {
          this.quickStats = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching quick stats:', error);
        // Keep default values
      }
    },

    openTransactionModal() {
      const modal = new Modal(document.getElementById('transactionModal'));
      modal.show();
    },

    openStatusUpdateModal(action) {
      this.statusUpdateData = {
        action: action,
        remarks: '',
        requestId: null
      };
      
      const modal = new Modal(document.getElementById('statusUpdateModal'));
      modal.show();
    },

    confirmStatusUpdate() {
      // This would typically show a list of requests to select from
      // For now, we'll just redirect to the requests page with the appropriate filter
      const statusUpdateModal = Modal.getInstance(document.getElementById('statusUpdateModal'));
      statusUpdateModal.hide();
      
      // Map frontend actions to backend status values
      const statusMap = {
        'validate': 'Under Review',
        'approve': 'Approved', 
        'reject': 'Rejected'
      };
      
      const status = statusMap[this.statusUpdateData.action];
      if (status) {
        this.filterByStatus(status.toLowerCase().replace(' ', '-'));
      }
    },

    getActionButtonClass(action) {
      const classes = {
        'validate': 'btn-info',
        'approve': 'btn-success',
        'reject': 'btn-danger'
      };
      return classes[action] || 'btn-primary';
    },

    goToAllRequests() {
      this.$router.push({ name: 'Requests' });
    },

    async openCertificateModal() {
      const modal = new Modal(document.getElementById('certificateModal'));
      modal.show();
      
      // Fetch approved requests when modal opens
      await this.fetchApprovedRequests();
    },

    async fetchApprovedRequests() {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await this.$api.get('/e_assessment/api/v1/transactions/status/approved', { headers });
        
        if (response.data.success) {
          this.approvedRequests = response.data.data || [];
        }
      } catch (error) {
        console.error('Error fetching approved requests:', error);
        this.approvedRequests = [];
      }
    },

    async generateCertificate(type) {
      if (this.approvedRequests.length === 0) {
        Swal.fire({
          title: 'No Approved Requests',
          text: 'There are no approved requests available for certificate generation.',
          icon: 'info',
          confirmButtonText: 'OK'
        });
        return;
      }

      // For now, generate certificate for the first approved request
      // In a real implementation, you might want to show a selection dialog
      const firstRequest = this.approvedRequests[0];
      await this.generateRequestCertificate(firstRequest.id, type);
    },

    async generateRequestCertificate(requestId, type) {
      try {
        this.certificateLoading = true;
        
        const headers = AuthService.getAuthHeader();
        const endpoint = type === 'ownership' 
          ? `/e_assessment/api/v1/certificates/ownership/${requestId}`
          : `/e_assessment/api/v1/certificates/tax-declaration/${requestId}`;
        
        const response = await this.$api.get(endpoint, { 
          headers,
          responseType: 'blob' // Important for PDF downloads
        });
        
        // Create blob URL and trigger download
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        
        // Create temporary download link
        const link = document.createElement('a');
        link.href = url;
        link.download = `${type}_certificate_${requestId}_${new Date().toISOString().split('T')[0]}.pdf`;
        document.body.appendChild(link);
        link.click();
        
        // Cleanup
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
        
        Swal.fire({
          title: 'Certificate Generated!',
          text: `${this.capitalize(type.replace('_', ' '))} certificate has been downloaded successfully.`,
          icon: 'success',
          timer: 3000,
          showConfirmButton: false
        });
        
      } catch (error) {
        console.error('Error generating certificate:', error);
        
        let errorMessage = 'Failed to generate certificate. Please try again.';
        if (error.response?.status === 404) {
          errorMessage = 'Assessment request not found.';
        } else if (error.response?.status === 400) {
          errorMessage = 'Certificate can only be generated for approved requests.';
        }
        
        Swal.fire({
          title: 'Error',
          text: errorMessage,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      } finally {
        this.certificateLoading = false;
      }
    },
    checkAuthentication() {
      if (!AuthService.isAuthenticated()) {
        this.$router.push('/sign-in');
      }
    },
    
    async fetchDashboardData() {
      try {
        const headers = AuthService.getAuthHeader();
        
        const statsResponse = await this.$api.get('/e_assessment/api/v1/dashboard/statistics', { headers });
        if (statsResponse.data.success) {
          this.dashboardData = statsResponse.data.data;
        }
        
        const municipalityResponse = await this.$api.get('/e_assessment/api/v1/dashboard/municipalities', { headers });
        if (municipalityResponse.data.success) {
          this.municipalities = municipalityResponse.data.data;
        }
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
        if (error.response && (error.response.status === 401 || error.response.status === 403)) {
          AuthService.logout();
          this.$router.push('/sign-in');
        } else {
          // Show user-friendly error message
          this.$toast?.error?.('Failed to load dashboard data. Using offline data.');
        }
        
        // Fallback data
        this.dashboardData = {
          totalRpus: 1,
          totalCancelled: 0,
          totalNewlyDeclared: 1,
          totalProperty: 1
        };
        
        // Fallback municipality data
        if (this.municipalities.length === 0) {
          this.municipalities = [
            { id: 1, name: 'Maria Aurora', totalProperties: 1 },
            { id: 2, name: 'San Luis', totalProperties: 0 },
            { id: 3, name: 'Baler', totalProperties: 0 },
            { id: 4, name: 'Dipaculao', totalProperties: 0 },
            { id: 5, name: 'Dingalan', totalProperties: 0 },
            { id: 6, name: 'Dinalungan', totalProperties: 0 },
            { id: 7, name: 'Casiguran', totalProperties: 0 },
            { id: 8, name: 'Dilasag', totalProperties: 0 }
          ];
        }
      }
    },
    
    async fetchRecentActivities() {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await this.$api.get('/e_assessment/api/v1/dashboard/recent-activities', { headers });
        
        if (response.data.success) {
          this.recentActivities = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching recent activities:', error);
        
        // Fallback data
        this.recentActivities = [
          {
            id: 1,
            arpNo: 'ARP-2024-001',
            ownerName: 'Juan Dela Cruz',
            municipality: 'Maria Aurora',
            type: 'Assessment',
            status: 'Pending',
            createdAt: new Date().toISOString()
          },
          {
            id: 2,
            arpNo: 'ARP-2024-002',
            ownerName: 'Maria Santos',
            municipality: 'Baler',
            type: 'Assessment',
            status: 'Approved',
            createdAt: new Date(Date.now() - 86400000).toISOString()
          }
        ];
      }
    },
    
    async fetchChartData() {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await this.$api.get('/e_assessment/api/v1/dashboard/chart-data', { headers });
        
        if (response.data.success) {
          this.chartData = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching chart data:', error);
        // Fallback to generating data from municipalities
        this.chartData = this.municipalities.map(municipality => ({
          municipality: municipality.name,
          land: Math.floor(Math.random() * 10),
          buildings: Math.floor(Math.random() * 5),
          machineries: Math.floor(Math.random() * 3)
        }));
      }
    },
    
    initializeCharts() {
      this.createAssetsChart();
      this.createYearlyChart();
    },
    
    createAssetsChart() {
      const ctx = document.getElementById('assetsChart');
      if (!ctx) return;
      
      const municipalityNames = this.chartData.map(item => item.municipality);
      const landData = this.chartData.map(item => item.land);
      const buildingsData = this.chartData.map(item => item.buildings);
      const machineriesData = this.chartData.map(item => item.machineries);
      
      this.assetsChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: municipalityNames,
          datasets: [
            {
              label: 'Land',
              data: landData,
              backgroundColor: '#26D0CE',
              borderWidth: 0,
              borderRadius: 4
            },
            {
              label: 'Buildings',
              data: buildingsData,
              backgroundColor: '#1A73E8',
              borderWidth: 0,
              borderRadius: 4
            },
            {
              label: 'Machineries',
              data: machineriesData,
              backgroundColor: '#FB8500',
              borderWidth: 0,
              borderRadius: 4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  size: 10
                }
              }
            },
            y: {
              beginAtZero: true,
              grid: {
                color: '#f1f1f1'
              }
            }
          }
        }
      });
    },
    
    createYearlyChart() {
      const ctx = document.getElementById('yearlyChart');
      if (!ctx) return;
      
      const years = ['2021', '2022', '2023', '2024', '2025'];
      const landData = [5, 6, 7, 8, 10];
      const buildingsData = [3, 4, 5, 6, 7];
      const machineriesData = [1, 1, 2, 2, 3];
      
      this.yearlyChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: years,
          datasets: [
            {
              label: 'Land',
              data: landData,
              borderColor: '#26D0CE',
              backgroundColor: 'rgba(38, 208, 206, 0.1)',
              borderWidth: 3,
              fill: true,
              tension: 0.4
            },
            {
              label: 'Buildings',
              data: buildingsData,
              borderColor: '#1A73E8',
              backgroundColor: 'rgba(26, 115, 232, 0.1)',
              borderWidth: 3,
              fill: false,
              tension: 0.4
            },
            {
              label: 'Machineries',
              data: machineriesData,
              borderColor: '#FB8500',
              backgroundColor: 'rgba(251, 133, 0, 0.1)',
              borderWidth: 3,
              fill: false,
              tension: 0.4
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              grid: {
                display: false
              }
            },
            y: {
              beginAtZero: true,
              grid: {
                color: '#f1f1f1'
              }
            }
          }
        }
      });
    },
    
    getStatusBadgeClass(status) {
      const statusClasses = {
        'Pending': 'bg-warning text-dark',
        'Under Review': 'bg-info text-white',
        'Approved': 'bg-success text-white',
        'Rejected': 'bg-danger text-white',
        'Completed': 'bg-primary text-white'
      };
      return statusClasses[status] || 'bg-secondary text-white';
    },
    
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });
    }
  }
};
</script>

<style scoped>
/* Quick Actions Styles */
.quick-action-card {
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.quick-action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.quick-action-icon {
  width: 45px;
  height: 45px;
}

.bg-warning-light {
  background-color: rgba(255, 193, 7, 0.1);
  border-color: rgba(255, 193, 7, 0.2);
}

.bg-info-light {
  background-color: rgba(23, 162, 184, 0.1);
  border-color: rgba(23, 162, 184, 0.2);
}

.bg-success-light {
  background-color: rgba(40, 167, 69, 0.1);
  border-color: rgba(40, 167, 69, 0.2);
}

.bg-primary-light {
  background-color: rgba(0, 123, 255, 0.1);
  border-color: rgba(0, 123, 255, 0.2);
}

.quick-action-card:hover.bg-warning-light {
  background-color: rgba(255, 193, 7, 0.15);
  border-color: #ffc107;
}

.quick-action-card:hover.bg-info-light {
  background-color: rgba(23, 162, 184, 0.15);
  border-color: #17a2b8;
}

.quick-action-card:hover.bg-success-light {
  background-color: rgba(40, 167, 69, 0.15);
  border-color: #28a745;
}

.quick-action-card:hover.bg-primary-light {
  background-color: rgba(0, 123, 255, 0.15);
  border-color: #007bff;
}

/* Transaction Modal Styles */
.transaction-action-card {
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #e9ecef;
}

.transaction-action-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  border-color: #007bff;
}

.transaction-action-card .card-body {
  padding: 1.5rem;
}

/* Certificate Modal Styles */
.certificate-type-card {
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #e9ecef;
}

.certificate-type-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  border-color: #007bff;
}

.certificate-type-card .card-body {
  padding: 1.5rem;
}

.cursor-pointer {
  cursor: pointer;
}

/* Chart Responsiveness */
.chart-container {
  position: relative;
  height: 300px;
  width: 100%;
}

@media (max-width: 768px) {
  .chart-container {
    height: 250px;
  }
  
  .quick-action-card {
    margin-bottom: 1rem !important;
  }
}
</style>