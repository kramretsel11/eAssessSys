<template>
  <div class="request-management">
    <div class="row mb-4">
      <div class="col-md-8">
        <h6 class="text-dark font-weight-bold">
          <i class="fas fa-tasks me-2"></i>
          Assessment Request Management
        </h6>
        <p class="text-sm text-muted">Approve, reject, or manage assessment requests</p>
      </div>
      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-text">
            <i class="fas fa-search"></i>
          </span>
          <input
            v-model="searchTerm"
            type="text"
            class="form-control"
            placeholder="Search requests..."
          />
        </div>
      </div>
    </div>

    <!-- Filter Buttons -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="btn-group" role="group">
          <button
            type="button"
            class="btn btn-outline-primary"
            :class="{ active: statusFilter === 'all' }"
            @click="statusFilter = 'all'"
          >
            <i class="fas fa-list me-1"></i>All ({{ totalRequests }})
          </button>
          <button
            type="button"
            class="btn btn-outline-warning"
            :class="{ active: statusFilter === 'pending' }"
            @click="statusFilter = 'pending'"
          >
            <i class="fas fa-clock me-1"></i>Pending ({{ pendingCount }})
          </button>
          <button
            type="button"
            class="btn btn-outline-success"
            :class="{ active: statusFilter === 'approved' }"
            @click="statusFilter = 'approved'"
          >
            <i class="fas fa-check me-1"></i>Approved ({{ approvedCount }})
          </button>
          <button
            type="button"
            class="btn btn-outline-danger"
            :class="{ active: statusFilter === 'rejected' }"
            @click="statusFilter = 'rejected'"
          >
            <i class="fas fa-times me-1"></i>Rejected ({{ rejectedCount }})
          </button>
        </div>
      </div>
    </div>

    <!-- Requests Table -->
    <div class="table-responsive">
      <table class="table table-hover align-items-center mb-0">
        <thead>
          <tr class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
            <th>Request Info</th>
            <th>Owner</th>
            <th>Property Location</th>
            <th>Status</th>
            <th>Date Submitted</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="request in filteredRequests" :key="request.id">
            <!-- Request Info -->
            <td>
              <div class="d-flex px-2 py-1">
                <div class="d-flex flex-column justify-content-center">
                  <h6 class="mb-0 text-sm">{{ request.arpNo || 'N/A' }}</h6>
                  <p class="text-xs text-secondary mb-0">PIN: {{ request.pin || 'N/A' }}</p>
                </div>
              </div>
            </td>
            
            <!-- Owner -->
            <td>
              <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm">{{ request.ownerName }}</h6>
                <p class="text-xs text-secondary mb-0">{{ request.contactNo || 'No contact' }}</p>
              </div>
            </td>
            
            <!-- Location -->
            <td>
              <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm">{{ request.municipality }}, Aurora</h6>
                <p class="text-xs text-secondary mb-0">Barangay {{ request.barangay }}</p>
              </div>
            </td>
            
            <!-- Status -->
            <td>
              <span class="badge badge-sm" :class="getStatusBadgeClass(request.status)">
                <i class="fas me-1" :class="getStatusIcon(request.status)"></i>
                {{ request.status?.charAt(0).toUpperCase() + request.status?.slice(1) || 'Pending' }}
              </span>
            </td>
            
            <!-- Date -->
            <td>
              <span class="text-secondary text-xs font-weight-bold">
                {{ formatDate(request.created_at) }}
              </span>
            </td>
            
            <!-- Actions -->
            <td class="align-middle text-center">
              <div class="btn-group" role="group">
                <button
                  class="btn btn-info btn-sm"
                  @click="viewRequest(request)"
                  title="View Details"
                >
                  <i class="fas fa-eye"></i>
                </button>
                
                <button
                  v-if="request.status === 'pending'"
                  class="btn btn-success btn-sm"
                  @click="approveRequest(request)"
                  title="Approve"
                >
                  <i class="fas fa-check"></i>
                </button>
                
                <button
                  v-if="request.status === 'pending'"
                  class="btn btn-danger btn-sm"
                  @click="rejectRequest(request)"
                  title="Reject"
                >
                  <i class="fas fa-times"></i>
                </button>
                
                <button
                  class="btn btn-warning btn-sm"
                  @click="editRequest(request)"
                  title="Edit"
                >
                  <i class="fas fa-edit"></i>
                </button>
                
                <button
                  class="btn btn-danger btn-sm"
                  @click="deleteRequest(request)"
                  title="Delete"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Request Details Modal -->
    <div class="modal fade" id="requestDetailsModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-file-alt me-2"></i>
              Request Details
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" v-if="selectedRequest">
            <div class="row">
              <div class="col-md-6">
                <h6 class="text-primary">Property Information</h6>
                <table class="table table-borderless table-sm">
                  <tr>
                    <td><strong>ARP No:</strong></td>
                    <td>{{ selectedRequest.arpNo || 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>PIN:</strong></td>
                    <td>{{ selectedRequest.pin || 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Location:</strong></td>
                    <td>{{ selectedRequest.municipality }}, {{ selectedRequest.barangay }}, Aurora</td>
                  </tr>
                  <tr>
                    <td><strong>Area:</strong></td>
                    <td>{{ selectedRequest.areaNo || 'N/A' }} sq.m.</td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <h6 class="text-primary">Owner Information</h6>
                <table class="table table-borderless table-sm">
                  <tr>
                    <td><strong>Owner Name:</strong></td>
                    <td>{{ selectedRequest.ownerName }}</td>
                  </tr>
                  <tr>
                    <td><strong>Contact:</strong></td>
                    <td>{{ selectedRequest.contactNo || 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Address:</strong></td>
                    <td>{{ selectedRequest.ownerAddress || 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>TIN:</strong></td>
                    <td>{{ selectedRequest.tin || 'N/A' }}</td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="row mt-3" v-if="selectedRequest.status === 'pending'">
              <div class="col-12">
                <div class="border-top pt-3">
                  <div class="row">
                    <div class="col-md-6">
                      <button class="btn btn-success w-100" @click="approveRequest(selectedRequest)">
                        <i class="fas fa-check me-2"></i>Approve Request
                      </button>
                    </div>
                    <div class="col-md-6">
                      <button class="btn btn-danger w-100" @click="rejectRequest(selectedRequest)">
                        <i class="fas fa-times me-2"></i>Reject Request
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Approval/Rejection Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ actionType === 'approve' ? 'Approve Request' : 'Reject Request' }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="form-label">
                {{ actionType === 'approve' ? 'Approval Comments:' : 'Rejection Reason:' }}
              </label>
              <textarea
                v-model="actionComments"
                class="form-control"
                rows="4"
                :placeholder="actionType === 'approve' ? 'Optional approval comments...' : 'Please provide reason for rejection...'"
                :required="actionType === 'reject'"
              ></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button
              type="button"
              class="btn"
              :class="actionType === 'approve' ? 'btn-success' : 'btn-danger'"
              @click="confirmAction"
              :disabled="actionType === 'reject' && !actionComments.trim()"
            >
              {{ actionType === 'approve' ? 'Approve' : 'Reject' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import AuthService from '@/services/auth.js';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

export default {
  name: 'RequestManagement',
  emits: ['request-approved', 'request-rejected', 'request-updated'],
  setup(props, { emit }) {
    const requests = ref([]);
    const searchTerm = ref('');
    const statusFilter = ref('all');
    const selectedRequest = ref(null);
    const actionType = ref('approve');
    const actionComments = ref('');

    const filteredRequests = computed(() => {
      let filtered = requests.value;

      // Filter by status
      if (statusFilter.value !== 'all') {
        filtered = filtered.filter(request => request.status === statusFilter.value);
      }

      // Filter by search term
      if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        filtered = filtered.filter(request =>
          request.arpNo?.toLowerCase().includes(term) ||
          request.pin?.toLowerCase().includes(term) ||
          request.ownerName?.toLowerCase().includes(term) ||
          request.municipality?.toLowerCase().includes(term) ||
          request.barangay?.toLowerCase().includes(term)
        );
      }

      return filtered;
    });

    const totalRequests = computed(() => requests.value.length);
    const pendingCount = computed(() => requests.value.filter(r => r.status === 'pending').length);
    const approvedCount = computed(() => requests.value.filter(r => r.status === 'approved').length);
    const rejectedCount = computed(() => requests.value.filter(r => r.status === 'rejected').length);

    const fetchRequests = async () => {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await fetch('/e_assessment/api/v1/misc/assessment-requests', {
          headers
        });

        if (!response.ok) {
          throw new Error('Failed to fetch requests');
        }

        requests.value = await response.json();
      } catch (error) {
        console.error('Error fetching requests:', error);
        // Swal.fire({
        //   title: 'Error',
        //   text: 'Failed to load requests',
        //   icon: 'error'
        // });
      }
    };

    const getStatusBadgeClass = (status) => {
      const classes = {
        pending: 'bg-gradient-warning',
        approved: 'bg-gradient-success',
        rejected: 'bg-gradient-danger'
      };
      return classes[status] || 'bg-gradient-secondary';
    };

    const getStatusIcon = (status) => {
      const icons = {
        pending: 'fa-clock',
        approved: 'fa-check-circle',
        rejected: 'fa-times-circle'
      };
      return icons[status] || 'fa-question-circle';
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      return new Date(dateString).toLocaleDateString();
    };

    const viewRequest = (request) => {
      selectedRequest.value = request;
      const modal = new Modal(document.getElementById('requestDetailsModal'));
      modal.show();
    };

    const approveRequest = (request) => {
      selectedRequest.value = request;
      actionType.value = 'approve';
      actionComments.value = '';
      const modal = new Modal(document.getElementById('actionModal'));
      modal.show();
    };

    const rejectRequest = (request) => {
      selectedRequest.value = request;
      actionType.value = 'reject';
      actionComments.value = '';
      const modal = new Modal(document.getElementById('actionModal'));
      modal.show();
    };

    const confirmAction = async () => {
      if (actionType.value === 'reject' && !actionComments.value.trim()) {
        Swal.fire({
          title: 'Missing Information',
          text: 'Please provide a reason for rejection',
          icon: 'warning'
        });
        return;
      }

      try {
        const headers = AuthService.getAuthHeader();
        const updateData = {
          id: selectedRequest.value.id,
          status: actionType.value === 'approve' ? 'approved' : 'rejected',
          comments: actionComments.value.trim(),
          reviewedBy: AuthService.getCurrentUser().username,
          reviewedAt: new Date().toISOString()
        };

        const response = await fetch(`/e_assessment/api/v1/misc/assessment-requests/status`, {
          method: 'PUT',
          headers: {
            ...headers,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(updateData)
        });

        if (!response.ok) {
          throw new Error('Failed to update request status');
        }

        // Update local data
        const requestIndex = requests.value.findIndex(r => r.id === selectedRequest.value.id);
        if (requestIndex !== -1) {
          requests.value[requestIndex].status = updateData.status;
          requests.value[requestIndex].comments = updateData.comments;
          requests.value[requestIndex].reviewedBy = updateData.reviewedBy;
          requests.value[requestIndex].reviewedAt = updateData.reviewedAt;
        }

        // Close modal
        const modal = Modal.getInstance(document.getElementById('actionModal'));
        modal.hide();

        // Emit appropriate event
        if (actionType.value === 'approve') {
          emit('request-approved', selectedRequest.value.id);
        } else {
          emit('request-rejected', selectedRequest.value.id, actionComments.value);
        }

        emit('request-updated');

      } catch (error) {
        console.error('Error updating request:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to update request status',
          icon: 'error'
        });
      }
    };

    const editRequest = () => {
      // Implement edit functionality
      Swal.fire({
        title: 'Edit Request',
        text: 'Edit functionality will be implemented',
        icon: 'info'
      });
    };

    const deleteRequest = async (request) => {
      const result = await Swal.fire({
        title: 'Delete Request?',
        text: `Are you sure you want to delete request ${request.arpNo}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      });

      if (result.isConfirmed) {
        try {
          const headers = AuthService.getAuthHeader();
          const response = await fetch(`/e_assessment/api/v1/misc/assessment-requests/${request.id}`, {
            method: 'DELETE',
            headers
          });

          if (!response.ok) {
            throw new Error('Failed to delete request');
          }

          // Remove from local data
          const index = requests.value.findIndex(r => r.id === request.id);
          if (index !== -1) {
            requests.value.splice(index, 1);
          }

          emit('request-updated');

          Swal.fire({
            title: 'Deleted!',
            text: 'Request has been deleted.',
            icon: 'success',
            timer: 2000
          });

        } catch (error) {
          console.error('Error deleting request:', error);
          Swal.fire({
            title: 'Error',
            text: 'Failed to delete request',
            icon: 'error'
          });
        }
      }
    };

    onMounted(() => {
      fetchRequests();
    });

    return {
      requests,
      searchTerm,
      statusFilter,
      selectedRequest,
      actionType,
      actionComments,
      filteredRequests,
      totalRequests,
      pendingCount,
      approvedCount,
      rejectedCount,
      fetchRequests,
      getStatusBadgeClass,
      getStatusIcon,
      formatDate,
      viewRequest,
      approveRequest,
      rejectRequest,
      confirmAction,
      editRequest,
      deleteRequest
    };
  }
};
</script>

<style scoped>
.btn-group .btn {
  margin-right: 0.25rem;
}

.btn-group .btn:last-child {
  margin-right: 0;
}

.table th {
  font-weight: 700;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.badge {
  font-size: 0.75rem;
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
}

.modal-content {
  border-radius: 12px;
}

.table-responsive {
  border-radius: 12px;
  overflow: hidden;
}

.btn-sm {
  padding: 0.375rem 0.75rem;
  font-size: 0.875rem;
}
</style>