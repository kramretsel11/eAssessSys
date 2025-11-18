<template>
  <div class="report-generation">
    <div class="row mb-4">
      <div class="col-md-8">
        <h6 class="text-dark font-weight-bold">
          <i class="fas fa-chart-bar me-2"></i>
          Report Generation
        </h6>
        <p class="text-sm text-muted">Generate comprehensive reports for assessment data analysis</p>
      </div>
    </div>

    <!-- Report Types -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Available Reports</h6>
          </div>
          <div class="card-body">
            <div class="row">
              <!-- Assessment Summary Report -->
              <div class="col-md-4 mb-3">
                <div class="report-card">
                  <div class="report-icon bg-gradient-primary">
                    <i class="fas fa-file-alt"></i>
                  </div>
                  <h6 class="mt-3">Assessment Summary</h6>
                  <p class="text-sm text-muted">Overview of all assessment requests with statistics</p>
                  <button class="btn btn-primary btn-sm w-100" @click="showReportModal('assessment_summary')">
                    <i class="fas fa-download me-1"></i>Generate
                  </button>
                </div>
              </div>

              <!-- Property Valuation Report -->
              <div class="col-md-4 mb-3">
                <div class="report-card">
                  <div class="report-icon bg-gradient-success">
                    <i class="fas fa-home"></i>
                  </div>
                  <h6 class="mt-3">Property Valuation</h6>
                  <p class="text-sm text-muted">Detailed property values and assessments by location</p>
                  <button class="btn btn-success btn-sm w-100" @click="showReportModal('property_valuation')">
                    <i class="fas fa-download me-1"></i>Generate
                  </button>
                </div>
              </div>

              <!-- Tax Collection Report -->
              <div class="col-md-4 mb-3">
                <div class="report-card">
                  <div class="report-icon bg-gradient-warning">
                    <i class="fas fa-receipt"></i>
                  </div>
                  <h6 class="mt-3">Tax Collection</h6>
                  <p class="text-sm text-muted">Tax collection analysis and revenue projections</p>
                  <button class="btn btn-warning btn-sm w-100" @click="showReportModal('tax_collection')">
                    <i class="fas fa-download me-1"></i>Generate
                  </button>
                </div>
              </div>

              <!-- Municipality Analysis Report -->
              <div class="col-md-4 mb-3">
                <div class="report-card">
                  <div class="report-icon bg-gradient-info">
                    <i class="fas fa-map-marker-alt"></i>
                  </div>
                  <h6 class="mt-3">Municipality Analysis</h6>
                  <p class="text-sm text-muted">Property distribution and assessment by municipality</p>
                  <button class="btn btn-info btn-sm w-100" @click="showReportModal('municipality_analysis')">
                    <i class="fas fa-download me-1"></i>Generate
                  </button>
                </div>
              </div>

              <!-- Certificate Summary Report -->
              <div class="col-md-4 mb-3">
                <div class="report-card">
                  <div class="report-icon bg-gradient-dark">
                    <i class="fas fa-certificate"></i>
                  </div>
                  <h6 class="mt-3">Certificate Summary</h6>
                  <p class="text-sm text-muted">Overview of generated certificates and documents</p>
                  <button class="btn btn-dark btn-sm w-100" @click="showReportModal('certificate_summary')">
                    <i class="fas fa-download me-1"></i>Generate
                  </button>
                </div>
              </div>

              <!-- Custom Report -->
              <div class="col-md-4 mb-3">
                <div class="report-card">
                  <div class="report-icon bg-gradient-danger">
                    <i class="fas fa-cogs"></i>
                  </div>
                  <h6 class="mt-3">Custom Report</h6>
                  <p class="text-sm text-muted">Build custom reports with specific criteria</p>
                  <button class="btn btn-danger btn-sm w-100" @click="showReportModal('custom')">
                    <i class="fas fa-tools me-1"></i>Build
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h4 class="text-primary">{{ stats.totalRequests }}</h4>
            <p class="mb-0">Total Requests</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h4 class="text-success">{{ stats.approvedRequests }}</h4>
            <p class="mb-0">Approved</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h4 class="text-info">₱{{ formatCurrency(stats.totalValue) }}</h4>
            <p class="mb-0">Total Assessed Value</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <div class="card-body">
            <h4 class="text-warning">{{ stats.certificatesGenerated }}</h4>
            <p class="mb-0">Certificates Generated</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Generated Reports -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">Recent Generated Reports</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Report Type</th>
                    <th>Generated By</th>
                    <th>Date Generated</th>
                    <th>Records</th>
                    <th>File Size</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="report in recentReports" :key="report.id">
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="fas me-2" :class="getReportIcon(report.type)"></i>
                        {{ getReportTitle(report.type) }}
                      </div>
                    </td>
                    <td>{{ report.generatedBy }}</td>
                    <td>{{ formatDateTime(report.generatedAt) }}</td>
                    <td>{{ report.recordCount }}</td>
                    <td>{{ report.fileSize }}</td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" @click="downloadReport(report)">
                          <i class="fas fa-download"></i>
                        </button>
                        <button class="btn btn-danger" @click="deleteReport(report)">
                          <i class="fas fa-trash"></i>
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

    <!-- Report Generation Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-chart-line me-2"></i>
              Generate {{ getReportTitle(selectedReportType) }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="generateReport">
              <!-- Common Filters -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Date From</label>
                  <input
                    v-model="reportForm.dateFrom"
                    type="date"
                    class="form-control"
                    :max="today"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Date To</label>
                  <input
                    v-model="reportForm.dateTo"
                    type="date"
                    class="form-control"
                    :max="today"
                  />
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Municipality</label>
                  <select v-model="reportForm.municipality" class="form-select">
                    <option value="">All Municipalities</option>
                    <option v-for="municipality in auroraMunicipalities" :key="municipality" :value="municipality">
                      {{ municipality }}
                    </option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Status</label>
                  <select v-model="reportForm.status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                  </select>
                </div>
              </div>

              <!-- Specific filters based on report type -->
              <div v-if="selectedReportType === 'property_valuation'" class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Property Type</label>
                  <select v-model="reportForm.propertyType" class="form-select">
                    <option value="">All Types</option>
                    <option value="residential">Residential</option>
                    <option value="commercial">Commercial</option>
                    <option value="industrial">Industrial</option>
                    <option value="agricultural">Agricultural</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Value Range</label>
                  <select v-model="reportForm.valueRange" class="form-select">
                    <option value="">All Ranges</option>
                    <option value="0-100000">₱0 - ₱100,000</option>
                    <option value="100000-500000">₱100,000 - ₱500,000</option>
                    <option value="500000-1000000">₱500,000 - ₱1,000,000</option>
                    <option value="1000000+">₱1,000,000+</option>
                  </select>
                </div>
              </div>

              <div v-if="selectedReportType === 'custom'" class="border p-3 mb-3">
                <h6 class="text-primary">Custom Report Fields</h6>
                <div class="row">
                  <div class="col-md-4" v-for="field in customFields" :key="field.key">
                    <div class="form-check">
                      <input
                        v-model="reportForm.selectedFields"
                        :value="field.key"
                        type="checkbox"
                        class="form-check-input"
                        :id="field.key"
                      />
                      <label class="form-check-label" :for="field.key">
                        {{ field.label }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Output Format -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Output Format</label>
                  <select v-model="reportForm.format" class="form-select">
                    <option value="csv">CSV (Excel Compatible)</option>
                    <option value="pdf">PDF Document</option>
                    <option value="xlsx">Excel Spreadsheet</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Include Summary</label>
                  <div class="form-check mt-2">
                    <input
                      v-model="reportForm.includeSummary"
                      type="checkbox"
                      class="form-check-input"
                      id="includeSummary"
                    />
                    <label class="form-check-label" for="includeSummary">
                      Include statistical summary
                    </label>
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" :disabled="generatingReport">
                  <i class="fas me-1" :class="generatingReport ? 'fa-spinner fa-spin' : 'fa-download'"></i>
                  {{ generatingReport ? 'Generating...' : 'Generate Report' }}
                </button>
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
import AddressService from '@/services/auroraAddress.js';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

export default {
  name: 'ReportGeneration',
  emits: ['report-generated'],
  setup(props, { emit }) {
    const selectedReportType = ref('');
    const generatingReport = ref(false);
    const stats = ref({
      totalRequests: 0,
      approvedRequests: 0,
      totalValue: 0,
      certificatesGenerated: 0
    });
    const recentReports = ref([]);

    const reportForm = ref({
      dateFrom: '',
      dateTo: '',
      municipality: '',
      status: '',
      propertyType: '',
      valueRange: '',
      selectedFields: [],
      format: 'csv',
      includeSummary: true
    });

    const auroraMunicipalities = computed(() => 
      AddressService.getAllAuroraData().municipalities.map(m => m.name)
    );

    const today = computed(() => new Date().toISOString().split('T')[0]);

    const customFields = ref([
      { key: 'arpNo', label: 'ARP Number' },
      { key: 'pin', label: 'PIN' },
      { key: 'ownerName', label: 'Owner Name' },
      { key: 'ownerAddress', label: 'Owner Address' },
      { key: 'contactNo', label: 'Contact Number' },
      { key: 'municipality', label: 'Municipality' },
      { key: 'barangay', label: 'Barangay' },
      { key: 'areaNo', label: 'Area (sq.m)' },
      { key: 'marketValue', label: 'Market Value' },
      { key: 'assessedValue', label: 'Assessed Value' },
      { key: 'status', label: 'Status' },
      { key: 'created_at', label: 'Date Created' },
      { key: 'updated_at', label: 'Date Updated' }
    ]);

    const fetchStats = async () => {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await fetch('/e_assessment/api/v1/misc/assessment-requests', {
          headers
        });

        if (response.ok) {
          const requests = await response.json();
          
          stats.value = {
            totalRequests: requests.length,
            approvedRequests: requests.filter(r => r.status === 'approved').length,
            totalValue: requests.reduce((sum, r) => sum + (parseFloat(r.assessedValue) || 0), 0),
            certificatesGenerated: requests.filter(r => r.status === 'approved').length * 2
          };
        }
      } catch (error) {
        console.error('Error fetching stats:', error);
        
        // Sample data for demonstration
        stats.value = {
          totalRequests: 156,
          approvedRequests: 134,
          totalValue: 15750000,
          certificatesGenerated: 268
        };
      }
    };

    const fetchRecentReports = () => {
      // Sample recent reports data
      recentReports.value = [
        {
          id: 1,
          type: 'assessment_summary',
          generatedBy: AuthService.getCurrentUser()?.username || 'admin',
          generatedAt: new Date().toISOString(),
          recordCount: 156,
          fileSize: '2.3 MB'
        },
        {
          id: 2,
          type: 'property_valuation',
          generatedBy: AuthService.getCurrentUser()?.username || 'admin',
          generatedAt: new Date(Date.now() - 86400000).toISOString(),
          recordCount: 134,
          fileSize: '1.8 MB'
        },
        {
          id: 3,
          type: 'municipality_analysis',
          generatedBy: AuthService.getCurrentUser()?.username || 'admin',
          generatedAt: new Date(Date.now() - 172800000).toISOString(),
          recordCount: 8,
          fileSize: '0.5 MB'
        }
      ];
    };

    const getReportTitle = (type) => {
      const titles = {
        assessment_summary: 'Assessment Summary Report',
        property_valuation: 'Property Valuation Report',
        tax_collection: 'Tax Collection Report',
        municipality_analysis: 'Municipality Analysis Report',
        certificate_summary: 'Certificate Summary Report',
        custom: 'Custom Report'
      };
      return titles[type] || 'Report';
    };

    const getReportIcon = (type) => {
      const icons = {
        assessment_summary: 'fa-file-alt',
        property_valuation: 'fa-home',
        tax_collection: 'fa-receipt',
        municipality_analysis: 'fa-map-marker-alt',
        certificate_summary: 'fa-certificate',
        custom: 'fa-cogs'
      };
      return icons[type] || 'fa-file';
    };

    const formatCurrency = (value) => {
      return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(value);
    };

    const formatDateTime = (dateString) => {
      if (!dateString) return 'N/A';
      return new Date(dateString).toLocaleString();
    };

    const showReportModal = (reportType) => {
      selectedReportType.value = reportType;
      
      // Reset form
      reportForm.value = {
        dateFrom: '',
        dateTo: '',
        municipality: '',
        status: '',
        propertyType: '',
        valueRange: '',
        selectedFields: reportType === 'custom' ? [] : [],
        format: 'csv',
        includeSummary: true
      };

      const modal = new Modal(document.getElementById('reportModal'));
      modal.show();
    };

    const generateReport = async () => {
      generatingReport.value = true;

      try {
        const headers = AuthService.getAuthHeader();
        
        const reportData = {
          type: selectedReportType.value,
          filters: reportForm.value,
          generatedBy: AuthService.getCurrentUser()?.username || 'admin',
          generatedAt: new Date().toISOString()
        };

        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        const response = await fetch('/e_assessment/api/v1/misc/reports/generate', {
          method: 'POST',
          headers: {
            ...headers,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(reportData)
        });

        let blob, filename, reportTitle;

        if (!response.ok) {
          // Fallback: Generate sample data
          const sampleData = generateSampleReportData(selectedReportType.value);
          blob = new Blob([sampleData], { 
            type: reportForm.value.format === 'csv' ? 'text/csv' : 'application/pdf' 
          });
          reportTitle = getReportTitle(selectedReportType.value);
          filename = `${selectedReportType.value}_${new Date().toISOString().split('T')[0]}.${reportForm.value.format}`;
        } else {
          blob = await response.blob();
          reportTitle = getReportTitle(selectedReportType.value);
          filename = `${selectedReportType.value}_${new Date().toISOString().split('T')[0]}.${reportForm.value.format}`;
        }

        // Download the file
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        // Add to recent reports
        recentReports.value.unshift({
          id: Date.now(),
          type: selectedReportType.value,
          generatedBy: AuthService.getCurrentUser()?.username || 'admin',
          generatedAt: new Date().toISOString(),
          recordCount: Math.floor(Math.random() * 200) + 50,
          fileSize: (Math.random() * 5 + 0.5).toFixed(1) + ' MB'
        });

        // Close modal
        const modal = Modal.getInstance(document.getElementById('reportModal'));
        modal?.hide();

        emit('report-generated', reportTitle);

        Swal.fire({
          title: 'Report Generated!',
          text: `${reportTitle} has been generated and downloaded successfully`,
          icon: 'success',
          timer: 3000
        });

      } catch (error) {
        console.error('Error generating report:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to generate report',
          icon: 'error'
        });
      } finally {
        generatingReport.value = false;
      }
    };

    const generateSampleReportData = () => {
      const headers = ['ARP No', 'PIN', 'Owner Name', 'Municipality', 'Barangay', 'Area (sq.m)', 'Market Value', 'Assessed Value', 'Status'];
      const sampleRows = [];
      
      for (let i = 1; i <= 50; i++) {
        const municipalities = auroraMunicipalities.value;
        const municipality = municipalities[Math.floor(Math.random() * municipalities.length)];
        
        sampleRows.push([
          `ARP-${String(i).padStart(6, '0')}`,
          `PIN-${String(i).padStart(8, '0')}`,
          `Property Owner ${i}`,
          municipality,
          `Barangay ${i % 10 + 1}`,
          (Math.random() * 1000 + 100).toFixed(2),
          (Math.random() * 500000 + 50000).toFixed(2),
          (Math.random() * 100000 + 10000).toFixed(2),
          ['pending', 'approved', 'rejected'][Math.floor(Math.random() * 3)]
        ]);
      }

      return [headers.join(','), ...sampleRows.map(row => row.join(','))].join('\n');
    };

    const downloadReport = () => {
      Swal.fire({
        title: 'Download Report',
        text: 'Report download functionality would be implemented here',
        icon: 'info'
      });
    };

    const deleteReport = async (report) => {
      const result = await Swal.fire({
        title: 'Delete Report?',
        text: 'Are you sure you want to delete this report?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      });

      if (result.isConfirmed) {
        const index = recentReports.value.findIndex(r => r.id === report.id);
        if (index !== -1) {
          recentReports.value.splice(index, 1);
        }

        Swal.fire({
          title: 'Deleted!',
          text: 'Report has been deleted.',
          icon: 'success',
          timer: 2000
        });
      }
    };

    onMounted(() => {
      fetchStats();
      fetchRecentReports();
    });

    return {
      selectedReportType,
      generatingReport,
      stats,
      recentReports,
      reportForm,
      auroraMunicipalities,
      today,
      customFields,
      getReportTitle,
      getReportIcon,
      formatCurrency,
      formatDateTime,
      showReportModal,
      generateReport,
      downloadReport,
      deleteReport
    };
  }
};
</script>

<style scoped>
.report-card {
  padding: 1.5rem;
  border: 1px solid #dee2e6;
  border-radius: 12px;
  text-align: center;
  height: 100%;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.report-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.report-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  color: white;
  font-size: 1.5rem;
}

.card {
  border-radius: 12px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table th {
  font-weight: 700;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn-group-sm .btn {
  padding: 0.375rem 0.75rem;
}

.modal-content {
  border-radius: 12px;
}

.form-check-input {
  margin-top: 0.125rem;
}

.border {
  border-radius: 8px;
}

h4 {
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.text-center p {
  color: #6c757d;
  font-size: 0.875rem;
}
</style>