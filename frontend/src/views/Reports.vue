<template>
  <div class="container-fluid py-4">
    <!-- Reports Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex align-items-center">
              <div>
                <h5 class="mb-0">
                  <i class="fas fa-chart-bar text-info me-2"></i>
                  Assessment Reports
                </h5>
                <p class="text-sm mb-0">Generate and view property assessment reports</p>
              </div>
              <div class="ms-auto">
                <button class="btn btn-primary" @click="generateReport">
                  <i class="fas fa-plus me-2"></i>Generate Report
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Report Filters -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <h6>Report Filters</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <label class="form-label">Report Type</label>
                <select v-model="filters.type" class="form-select">
                  <option value="">All Types</option>
                  <option value="monthly">Monthly Summary</option>
                  <option value="quarterly">Quarterly Report</option>
                  <option value="annual">Annual Report</option>
                  <option value="municipality">Municipality Report</option>
                  <option value="evaluator">Evaluator Performance</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Municipality</label>
                <select v-model="filters.municipality" class="form-select">
                  <option value="">All Municipalities</option>
                  <option value="Baler">Baler</option>
                  <option value="Casiguran">Casiguran</option>
                  <option value="Dilasag">Dilasag</option>
                  <option value="Dinalungan">Dinalungan</option>
                  <option value="Dingalan">Dingalan</option>
                  <option value="Dipaculao">Dipaculao</option>
                  <option value="Maria Aurora">Maria Aurora</option>
                  <option value="San Luis">San Luis</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Date From</label>
                <input type="date" v-model="filters.dateFrom" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">Date To</label>
                <input type="date" v-model="filters.dateTo" class="form-control">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Overview -->
    <div class="row mb-4">
      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-body p-3">
            <div class="row">
              <div class="col-8">
                <div class="numbers">
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Assessments</p>
                  <h5 class="font-weight-bolder mb-0">{{ stats.totalAssessments }}</h5>
                  <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>+12% from last month
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                  <i class="fas fa-home text-lg opacity-10"></i>
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
                  <h5 class="font-weight-bolder mb-0">{{ stats.approvedAssessments }}</h5>
                  <small class="text-success">
                    <i class="fas fa-check me-1"></i>{{ stats.approvalRate }}% approval rate
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
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Pending</p>
                  <h5 class="font-weight-bolder mb-0">{{ stats.pendingAssessments }}</h5>
                  <small class="text-warning">
                    <i class="fas fa-clock me-1"></i>Awaiting evaluation
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                  <i class="fas fa-hourglass-half text-lg opacity-10"></i>
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
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Value</p>
                  <h5 class="font-weight-bolder mb-0">₱{{ formatCurrency(stats.totalValue) }}</h5>
                  <small class="text-info">
                    <i class="fas fa-peso-sign me-1"></i>Assessed properties
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                  <i class="fas fa-chart-line text-lg opacity-10"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Available Reports -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h6>Generated Reports</h6>
              <div>
                <button class="btn btn-sm btn-outline-info me-2" @click="refreshReports">
                  <i class="fas fa-sync me-1"></i>Refresh
                </button>
              </div>
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Report</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Period</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Generated</th>
                    <th class="text-secondary opacity-7">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="report in filteredReports" :key="report.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ report.name }}</h6>
                          <p class="text-xs text-secondary mb-0">{{ report.description }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ report.type }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ report.municipality || 'All Municipalities' }}</p>
                      </div>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="text-secondary text-xs font-weight-bold">
                        {{ report.period }}
                      </span>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="badge badge-sm" :class="getStatusClass(report.status)">
                        {{ report.status }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">
                        {{ formatDate(report.generatedDate) }}
                      </span>
                    </td>
                    <td class="align-middle">
                      <div class="d-flex align-items-center">
                        <button 
                          class="btn btn-sm btn-outline-info me-2" 
                          @click="viewReport(report)"
                          title="View Report">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button 
                          v-if="report.status === 'completed'"
                          class="btn btn-sm btn-outline-success me-2" 
                          @click="downloadReport(report)"
                          title="Download PDF">
                          <i class="fas fa-download"></i>
                        </button>
                        <button 
                          class="btn btn-sm btn-outline-primary" 
                          @click="shareReport()"
                          title="Share Report">
                          <i class="fas fa-share"></i>
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

    <!-- Report Charts Section -->
    <div class="row mt-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header pb-0">
            <h6>Monthly Assessment Trends</h6>
          </div>
          <div class="card-body">
            <div class="chart">
              <!-- Chart placeholder - in real app would use Chart.js or similar -->
              <div class="text-center py-5">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <p class="text-muted">Assessment trends chart will be displayed here</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
          <div class="card-header pb-0">
            <h6>Municipality Distribution</h6>
          </div>
          <div class="card-body">
            <div class="chart">
              <!-- Chart placeholder -->
              <div class="text-center py-5">
                <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
                <p class="text-muted">Municipality distribution chart will be displayed here</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Report Generation Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Generate New Report</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createReport">
              <div class="mb-3">
                <label class="form-label">Report Name</label>
                <input type="text" v-model="newReport.name" class="form-control" required>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Report Type</label>
                <select v-model="newReport.type" class="form-select" required>
                  <option value="">Select Type</option>
                  <option value="monthly">Monthly Summary</option>
                  <option value="quarterly">Quarterly Report</option>
                  <option value="annual">Annual Report</option>
                  <option value="municipality">Municipality Report</option>
                  <option value="evaluator">Evaluator Performance</option>
                </select>
              </div>

              <div class="mb-3" v-if="newReport.type === 'municipality'">
                <label class="form-label">Municipality</label>
                <select v-model="newReport.municipality" class="form-select">
                  <option value="">Select Municipality</option>
                  <option value="Baler">Baler</option>
                  <option value="Casiguran">Casiguran</option>
                  <option value="Dilasag">Dilasag</option>
                  <option value="Dinalungan">Dinalungan</option>
                  <option value="Dingalan">Dingalan</option>
                  <option value="Dipaculao">Dipaculao</option>
                  <option value="Maria Aurora">Maria Aurora</option>
                  <option value="San Luis">San Luis</option>
                </select>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Date From</label>
                  <input type="date" v-model="newReport.dateFrom" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Date To</label>
                  <input type="date" v-model="newReport.dateTo" class="form-control">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea v-model="newReport.description" class="form-control" rows="3"></textarea>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Generate Report</button>
              </div>
            </form>
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
  name: 'Reports',
  setup() {
    const reports = ref([]);
    const filters = ref({
      type: '',
      municipality: '',
      dateFrom: '',
      dateTo: ''
    });

    const newReport = ref({
      name: '',
      type: '',
      municipality: '',
      dateFrom: '',
      dateTo: '',
      description: ''
    });

    const stats = ref({
      totalAssessments: 1250,
      approvedAssessments: 1050,
      pendingAssessments: 125,
      totalValue: 15750000000,
      approvalRate: 84
    });

    const filteredReports = computed(() => {
      let filtered = reports.value;
      
      if (filters.value.type) {
        filtered = filtered.filter(r => r.type === filters.value.type);
      }
      if (filters.value.municipality) {
        filtered = filtered.filter(r => r.municipality === filters.value.municipality);
      }
      if (filters.value.dateFrom) {
        filtered = filtered.filter(r => new Date(r.generatedDate) >= new Date(filters.value.dateFrom));
      }
      if (filters.value.dateTo) {
        filtered = filtered.filter(r => new Date(r.generatedDate) <= new Date(filters.value.dateTo));
      }
      
      return filtered;
    });

    const fetchReports = async () => {
      try {
        const response = await fetch('http://localhost:8080/e_assessment/api/v1/misc/reports/recent', {
          headers: {
            ...AuthService.getAuthHeader(),
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          const result = await response.json();
          if (result.status === 'success') {
            reports.value = result.data || [];
          } else {
            throw new Error(result.message || 'Failed to fetch reports');
          }
        } else if (response.status === 403) {
          Swal.fire({
            title: 'Access Denied',
            text: 'You do not have permission to access reports',
            icon: 'error'
          });
          this.$router.push('/dashboard');
        } else {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
      } catch (error) {
        Swal.fire({
          title: 'Warning',
          text: 'Could not load reports from server. Showing demo data.',
          icon: 'warning',
          timer: 3000
        });
        // Fallback demo data only if API fails
        reports.value = [
          {
            id: 1,
            name: 'January 2025 Assessment Report',
            type: 'monthly',
            municipality: '',
            period: 'Jan 2025',
            status: 'completed',
            generatedDate: '2025-01-15',
            description: 'Monthly assessment summary for all municipalities'
          },
          {
            id: 2,
            name: 'Baler Municipality Q4 2024',
            type: 'municipality',
            municipality: 'Baler',
            period: 'Q4 2024',
            status: 'completed',
            generatedDate: '2025-01-10',
            description: 'Quarterly report for Baler municipality'
          }
        ];
      }
    };

    const generateReport = () => {
      newReport.value = {
        name: '',
        type: '',
        municipality: '',
        dateFrom: '',
        dateTo: '',
        description: ''
      };
      new Modal(document.getElementById('reportModal')).show();
    };

    const createReport = async () => {
      try {
        const formData = new FormData();
        formData.append('name', newReport.value.name);
        formData.append('type', newReport.value.type);
        formData.append('municipality', newReport.value.municipality);
        formData.append('dateFrom', newReport.value.dateFrom);
        formData.append('dateTo', newReport.value.dateTo);
        formData.append('description', newReport.value.description);

        const response = await fetch('http://localhost:8080/e_assessment/api/v1/misc/reports/generate', {
          method: 'POST',
          headers: AuthService.getAuthHeader(),
          body: formData
        });

        const result = await response.json();
        
        if (response.ok && result.status === 'success') {
          Modal.getInstance(document.getElementById('reportModal')).hide();
          
          Swal.fire({
            title: 'Report Generation Started',
            text: 'Your report is being generated and will be ready shortly',
            icon: 'success',
            timer: 3000
          });

          fetchReports(); // Refresh the list
        } else {
          throw new Error(result.message || 'Failed to generate report');
        }
      } catch (error) {
        Swal.fire('Error', 'Failed to generate report', 'error');
      }
    };

    const viewReport = (report) => {
      Swal.fire({
        title: report.name,
        html: `
          <div class="text-start">
            <p><strong>Type:</strong> ${report.type}</p>
            <p><strong>Period:</strong> ${report.period}</p>
            <p><strong>Status:</strong> ${report.status}</p>
            <p><strong>Description:</strong> ${report.description}</p>
            ${report.municipality ? `<p><strong>Municipality:</strong> ${report.municipality}</p>` : ''}
          </div>
        `,
        icon: 'info',
        showCloseButton: true,
        focusConfirm: false,
        confirmButtonText: 'Close'
      });
    };

    const downloadReport = (report) => {
      Swal.fire({
        title: 'Download Started',
        text: `Downloading ${report.name}...`,
        icon: 'success',
        timer: 2000
      });
    };

    const shareReport = () => {
      Swal.fire({
        title: 'Share Report',
        text: 'Share functionality will be available soon',
        icon: 'info'
      });
    };

    const refreshReports = () => {
      fetchReports();
      Swal.fire({
        title: 'Reports Refreshed',
        text: 'Report list has been updated',
        icon: 'success',
        timer: 2000
      });
    };

    const getStatusClass = (status) => {
      switch (status) {
        case 'completed': return 'bg-gradient-success';
        case 'processing': return 'bg-gradient-warning';
        case 'failed': return 'bg-gradient-danger';
        default: return 'bg-gradient-secondary';
      }
    };

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('en-PH', { 
        minimumFractionDigits: 0,
        maximumFractionDigits: 0 
      }).format(amount);
    };

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString();
    };

    onMounted(() => {
      // Check permissions
      const user = AuthService.getCurrentUser();
      if (!user || !['1', '2', 'admin', 'coordinator'].includes(user.role)) {
        Swal.fire({
          title: 'Access Denied',
          text: 'You do not have permission to access reports',
          icon: 'error'
        }).then(() => {
          this.$router.push('/dashboard');
        });
        return;
      }
      
      fetchReports();
    });

    return {
      reports,
      filters,
      newReport,
      stats,
      filteredReports,
      generateReport,
      createReport,
      viewReport,
      downloadReport,
      shareReport,
      refreshReports,
      getStatusClass,
      formatCurrency,
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

.chart {
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>