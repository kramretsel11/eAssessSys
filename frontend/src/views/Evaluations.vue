<template>
  <div class="container-fluid py-4">
    <!-- Evaluations Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex align-items-center">
              <div>
                <h5 class="mb-0">
                  <i class="fas fa-clipboard-check text-success me-2"></i>
                  Property Evaluations
                </h5>
                <p class="text-sm mb-0">Evaluate and process property assessment requests</p>
              </div>
              <div class="ms-auto">
                <div class="badge bg-gradient-info">
                  <i class="fas fa-tasks me-1"></i>{{ pendingEvaluations }} Pending
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Pending</p>
                  <h5 class="font-weight-bolder mb-0">{{ pendingEvaluations }}</h5>
                  <small class="text-warning">
                    <i class="fas fa-hourglass-half me-1"></i>Awaiting Review
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                  <i class="fas fa-clock text-lg opacity-10"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Approved</p>
                  <h5 class="font-weight-bolder mb-0">{{ approvedEvaluations }}</h5>
                  <small class="text-success">
                    <i class="fas fa-check me-1"></i>This Month
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                  <i class="fas fa-check-circle text-lg opacity-10"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Rejected</p>
                  <h5 class="font-weight-bolder mb-0">{{ rejectedEvaluations }}</h5>
                  <small class="text-danger">
                    <i class="fas fa-times me-1"></i>This Month
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-danger shadow text-center border-radius-md">
                  <i class="fas fa-times-circle text-lg opacity-10"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Total</p>
                  <h5 class="font-weight-bolder mb-0">{{ totalEvaluations }}</h5>
                  <small class="text-info">
                    <i class="fas fa-list me-1"></i>All Time
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                  <i class="fas fa-chart-bar text-lg opacity-10"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Evaluations Table -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h6>Assessment Requests for Evaluation</h6>
              <div>
                <select v-model="statusFilter" class="form-select form-select-sm">
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="in-progress">In Progress</option>
                  <option value="approved">Approved</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Request</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Property</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Priority</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                    <th class="text-secondary opacity-7">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="evaluation in filteredEvaluations" :key="evaluation.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ evaluation.requestNumber }}</h6>
                          <p class="text-xs text-secondary mb-0">{{ evaluation.requestType }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ evaluation.propertyAddress }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ evaluation.municipality }}</p>
                      </div>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="badge badge-sm" :class="getStatusClass(evaluation.status)">
                        {{ evaluation.status }}
                      </span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="badge badge-sm" :class="getPriorityClass(evaluation.priority)">
                        {{ evaluation.priority }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">
                        {{ formatDate(evaluation.submittedDate) }}
                      </span>
                    </td>
                    <td class="align-middle">
                      <div class="d-flex align-items-center">
                        <button 
                          class="btn btn-sm btn-outline-info me-2" 
                          @click="viewEvaluation(evaluation)"
                          title="View Details">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button 
                          v-if="evaluation.status === 'pending'"
                          class="btn btn-sm btn-outline-success me-2" 
                          @click="startEvaluation(evaluation)"
                          title="Start Evaluation">
                          <i class="fas fa-play"></i>
                        </button>
                        <button 
                          v-if="evaluation.status === 'in-progress'"
                          class="btn btn-sm btn-outline-primary me-2" 
                          @click="continueEvaluation(evaluation)"
                          title="Continue">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button 
                          v-if="evaluation.status === 'approved'"
                          class="btn btn-sm btn-outline-warning" 
                          @click="generateCertificate(evaluation)"
                          title="Generate Certificate">
                          <i class="fas fa-certificate"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Evaluation Modal -->
    <div class="modal fade" id="evaluationModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Property Evaluation - {{ selectedEvaluation?.requestNumber }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedEvaluation">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Property Address</label>
                  <input type="text" class="form-control" :value="selectedEvaluation?.propertyAddress || ''" readonly>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Municipality</label>
                  <input type="text" class="form-control" :value="selectedEvaluation?.municipality || ''" readonly>
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-md-4">
                  <label class="form-label">Assessed Value (₱)</label>
                  <input type="number" class="form-control" v-model="evaluationForm.assessedValue" 
                         :readonly="selectedEvaluation?.status === 'approved'">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Market Value (₱)</label>
                  <input type="number" class="form-control" v-model="evaluationForm.marketValue"
                         :readonly="selectedEvaluation?.status === 'approved'">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Classification</label>
                  <select class="form-select" v-model="evaluationForm.classification"
                          :disabled="selectedEvaluation?.status === 'approved'">
                    <option value="Residential">Residential</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Industrial">Industrial</option>
                    <option value="Agricultural">Agricultural</option>
                  </select>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Evaluation Notes</label>
                <textarea class="form-control" rows="4" v-model="evaluationForm.notes"
                          :readonly="selectedEvaluation?.status === 'approved'"
                          placeholder="Add your evaluation notes and recommendations..."></textarea>
              </div>

              <div class="mb-3" v-if="selectedEvaluation?.status !== 'approved'">
                <label class="form-label">Evaluation Decision</label>
                <div class="d-flex gap-2">
                  <button class="btn btn-success" @click="approveEvaluation">
                    <i class="fas fa-check me-2"></i>Approve
                  </button>
                  <button class="btn btn-danger" @click="rejectEvaluation">
                    <i class="fas fa-times me-2"></i>Reject
                  </button>
                  <button class="btn btn-warning" @click="saveProgress">
                    <i class="fas fa-save me-2"></i>Save Progress
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import PermissionService from '@/services/permissions.js';
import AuthService from '@/services/auth.js';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

export default {
  name: 'Evaluations',
  setup() {
    const evaluations = ref([]);
    const statusFilter = ref('');
    const selectedEvaluation = ref(null);
    const evaluationForm = ref({
      assessedValue: 0,
      marketValue: 0,
      classification: '',
      notes: ''
    });

    // Statistics
    const pendingEvaluations = computed(() => 
      evaluations.value.filter(e => e.status === 'pending').length
    );
    const approvedEvaluations = computed(() => 
      evaluations.value.filter(e => e.status === 'approved').length
    );
    const rejectedEvaluations = computed(() => 
      evaluations.value.filter(e => e.status === 'rejected').length
    );
    const totalEvaluations = computed(() => evaluations.value.length);

    const filteredEvaluations = computed(() => {
      if (!statusFilter.value) return evaluations.value;
      return evaluations.value.filter(e => e.status === statusFilter.value);
    });

    const fetchEvaluations = async () => {
      try {
        const response = await fetch('http://localhost:8080/e_assessment/api/v1/evaluations/assigned', {
          headers: {
            ...AuthService.getAuthHeader(),
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          const result = await response.json();
          if (result.status === 'success') {
            evaluations.value = result.data || [];
          } else {
            throw new Error(result.message || 'Failed to fetch evaluations');
          }
        } else if (response.status === 403) {
          Swal.fire({
            title: 'Access Denied',
            text: 'You do not have permission to access evaluations',
            icon: 'error'
          });
          this.$router.push('/dashboard');
        } else {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
      } catch (error) {
        Swal.fire({
          title: 'Error',
          text: 'Failed to load evaluations. Please try again.',
          icon: 'error'
        });
        evaluations.value = [];
      }
    };

    const viewEvaluation = (evaluation) => {
      selectedEvaluation.value = evaluation;
      evaluationForm.value = {
        assessedValue: evaluation.assessedValue || 0,
        marketValue: evaluation.marketValue || 0,
        classification: evaluation.classification || '',
        notes: evaluation.notes || ''
      };
      new Modal(document.getElementById('evaluationModal')).show();
    };

    const startEvaluation = (evaluation) => {
      evaluation.status = 'in-progress';
      viewEvaluation(evaluation);
    };

    const continueEvaluation = (evaluation) => {
      viewEvaluation(evaluation);
    };

    const approveEvaluation = async () => {
      try {
        if (!selectedEvaluation.value) {
          Swal.fire('Error', 'No evaluation selected', 'error');
          return;
        }
        
        const formData = new FormData();
        formData.append('requestId', selectedEvaluation.value.id);
        formData.append('action', 'approve');
        formData.append('assessedValue', evaluationForm.value.assessedValue);
        formData.append('marketValue', evaluationForm.value.marketValue);
        formData.append('classification', evaluationForm.value.classification);
        formData.append('notes', evaluationForm.value.notes);

        const response = await fetch('http://localhost:8080/e_assessment/api/v1/evaluations/update', {
          method: 'POST',
          headers: AuthService.getAuthHeader(),
          body: formData
        });

        const result = await response.json();
        
        if (response.ok && result.status === 'success') {
          const evaluation = selectedEvaluation.value;
          if (evaluation) {
            evaluation.status = 'approved';
            evaluation.assessedValue = evaluationForm.value.assessedValue;
            evaluation.marketValue = evaluationForm.value.marketValue;
            evaluation.classification = evaluationForm.value.classification;
            evaluation.notes = evaluationForm.value.notes;
          }

          Modal.getInstance(document.getElementById('evaluationModal')).hide();
          
          Swal.fire({
            title: 'Evaluation Approved',
            text: 'Property evaluation has been approved successfully',
            icon: 'success',
            timer: 3000
          });
          
          fetchEvaluations(); // Refresh the list
        } else {
          throw new Error(result.message || 'Failed to approve evaluation');
        }
      } catch (error) {
        Swal.fire('Error', 'Failed to approve evaluation', 'error');
      }
    };

    const rejectEvaluation = async () => {
      try {
        const reason = await Swal.fire({
          title: 'Reject Evaluation',
          text: 'Please provide a reason for rejection:',
          input: 'textarea',
          inputPlaceholder: 'Rejection reason...',
          showCancelButton: true,
          inputValidator: (value) => {
            if (!value) return 'Please provide a reason for rejection';
          }
        });

        if (reason.isConfirmed) {
          if (!selectedEvaluation.value) {
            Swal.fire('Error', 'No evaluation selected', 'error');
            return;
          }
          
          const formData = new FormData();
          formData.append('requestId', selectedEvaluation.value.id);
          formData.append('action', 'reject');
          formData.append('notes', reason.value);

          const response = await fetch('http://localhost:8080/e_assessment/api/v1/evaluations/update', {
            method: 'POST',
            headers: AuthService.getAuthHeader(),
            body: formData
          });

          const result = await response.json();
          
          if (response.ok && result.status === 'success') {
            const evaluation = selectedEvaluation.value;
            if (evaluation) {
              evaluation.status = 'rejected';
              evaluation.notes = reason.value;
            }
            
            Modal.getInstance(document.getElementById('evaluationModal')).hide();
            
            Swal.fire({
              title: 'Evaluation Rejected',
              text: 'Property evaluation has been rejected',
              icon: 'info',
              timer: 3000
            });
            
            fetchEvaluations(); // Refresh the list
          } else {
            throw new Error(result.message || 'Failed to reject evaluation');
          }
        }
        } catch (error) {
        Swal.fire('Error', 'Failed to reject evaluation', 'error');
      }
    };

    const saveProgress = async () => {
      try {
        if (!selectedEvaluation.value) {
          Swal.fire('Error', 'No evaluation selected', 'error');
          return;
        }
        
        const formData = new FormData();
        formData.append('requestId', selectedEvaluation.value.id);
        formData.append('action', 'save');
        formData.append('assessedValue', evaluationForm.value.assessedValue);
        formData.append('marketValue', evaluationForm.value.marketValue);
        formData.append('classification', evaluationForm.value.classification);
        formData.append('notes', evaluationForm.value.notes);

        const response = await fetch('http://localhost:8080/e_assessment/api/v1/evaluations/update', {
          method: 'POST',
          headers: AuthService.getAuthHeader(),
          body: formData
        });

        const result = await response.json();
        
        if (response.ok && result.status === 'success') {
          const evaluation = selectedEvaluation.value;
          if (evaluation) {
            evaluation.assessedValue = evaluationForm.value.assessedValue;
            evaluation.marketValue = evaluationForm.value.marketValue;
            evaluation.classification = evaluationForm.value.classification;
            evaluation.notes = evaluationForm.value.notes;
            evaluation.status = 'in-progress';
          }
          
          Swal.fire({
            title: 'Progress Saved',
            text: 'Evaluation progress has been saved',
            icon: 'success',
            timer: 2000
          });
        } else {
          throw new Error(result.message || 'Failed to save progress');
        }
      } catch (error) {
        Swal.fire('Error', 'Failed to save progress', 'error');
      }
    };

    const generateCertificate = (evaluation) => {
      Swal.fire({
        title: 'Generate Certificate',
        text: `Generate assessment certificate for ${evaluation.requestNumber}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Generate',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // Redirect to certificates page or trigger certificate generation
          this.$router.push('/certificates');
        }
      });
    };

    const getStatusClass = (status) => {
      switch (status) {
        case 'pending': return 'bg-gradient-warning';
        case 'in-progress': return 'bg-gradient-info';
        case 'approved': return 'bg-gradient-success';
        case 'rejected': return 'bg-gradient-danger';
        default: return 'bg-gradient-secondary';
      }
    };

    const getPriorityClass = (priority) => {
      switch (priority) {
        case 'high': return 'bg-gradient-danger';
        case 'medium': return 'bg-gradient-warning';
        case 'low': return 'bg-gradient-success';
        default: return 'bg-gradient-secondary';
      }
    };

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString();
    };

    onMounted(() => {
      // Check permissions
      if (!PermissionService.canAccess('evaluations')) {
        Swal.fire({
          title: 'Access Denied',
          text: 'You do not have permission to access evaluations',
          icon: 'error'
        }).then(() => {
          this.$router.push('/dashboard');
        });
        return;
      }
      
      fetchEvaluations();
    });

    return {
      evaluations,
      statusFilter,
      selectedEvaluation,
      evaluationForm,
      pendingEvaluations,
      approvedEvaluations,
      rejectedEvaluations,
      totalEvaluations,
      filteredEvaluations,
      fetchEvaluations,
      viewEvaluation,
      startEvaluation,
      continueEvaluation,
      approveEvaluation,
      rejectEvaluation,
      saveProgress,
      generateCertificate,
      getStatusClass,
      getPriorityClass,
      formatDate
    };
  }
};
</script>

<style scoped>
.card {
  border-radius: 12px;
}

.badge {
  font-size: 0.75rem;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}
</style>