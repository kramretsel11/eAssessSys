<template>
  <div class="container-fluid py-4">
    <!-- Certificates Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="d-flex align-items-center">
              <div>
                <h5 class="mb-0">
                  <i class="fas fa-certificate text-warning me-2"></i>
                  Assessment Certificates
                </h5>
                <p class="text-sm mb-0">Generate and manage property assessment certificates</p>
              </div>
              <div class="ms-auto">
                <button class="btn btn-primary" @click="showGenerateModal">
                  <i class="fas fa-plus me-2"></i>Generate Certificate
                </button>
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
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Generated</p>
                  <h5 class="font-weight-bolder mb-0">{{ generatedCertificates }}</h5>
                  <small class="text-success">
                    <i class="fas fa-file-alt me-1"></i>This Month
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                  <i class="fas fa-file-alt text-lg opacity-10"></i>
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
                  <h5 class="font-weight-bolder mb-0">{{ pendingCertificates }}</h5>
                  <small class="text-warning">
                    <i class="fas fa-clock me-1"></i>Awaiting
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
                  <p class="text-sm mb-0 text-capitalize font-weight-bold">Downloaded</p>
                  <h5 class="font-weight-bolder mb-0">{{ downloadedCertificates }}</h5>
                  <small class="text-info">
                    <i class="fas fa-download me-1"></i>This Month
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                  <i class="fas fa-download text-lg opacity-10"></i>
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
                  <h5 class="font-weight-bolder mb-0">{{ totalCertificates }}</h5>
                  <small class="text-dark">
                    <i class="fas fa-certificate me-1"></i>All Time
                  </small>
                </div>
              </div>
              <div class="col-4 text-end">
                <div class="icon icon-shape bg-gradient-dark shadow text-center border-radius-md">
                  <i class="fas fa-certificate text-lg opacity-10"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-3">
                <label class="form-label">Status</label>
                <select v-model="filters.status" class="form-select">
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="generated">Generated</option>
                  <option value="downloaded">Downloaded</option>
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

    <!-- Certificates Table -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header pb-0">
            <h6>Assessment Certificates</h6>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Certificate</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Property</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Value</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                    <th class="text-secondary opacity-7">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="certificate in filteredCertificates" :key="certificate.id">
                    <td>
                      <div class="d-flex px-2 py-1">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm">{{ certificate.certificateNumber }}</h6>
                          <p class="text-xs text-secondary mb-0">{{ certificate.requestNumber }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{ certificate.propertyAddress }}</h6>
                        <p class="text-xs text-secondary mb-0">{{ certificate.municipality }}</p>
                      </div>
                    </td>
                    <td class="align-middle text-center text-sm">
                      <span class="badge badge-sm" :class="getStatusClass(certificate.status)">
                        {{ certificate.status }}
                      </span>
                    </td>
                    <td class="align-middle text-center">
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">₱{{ formatCurrency(certificate.assessedValue) }}</h6>
                        <p class="text-xs text-secondary mb-0">Assessed Value</p>
                      </div>
                    </td>
                    <td class="align-middle text-center">
                      <span class="text-secondary text-xs font-weight-bold">
                        {{ formatDate(certificate.dateGenerated) }}
                      </span>
                    </td>
                    <td class="align-middle">
                      <div class="d-flex align-items-center">
                        <button 
                          class="btn btn-sm btn-outline-info me-2" 
                          @click="viewCertificate(certificate)"
                          title="View Certificate">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button 
                          v-if="certificate.status === 'generated'"
                          class="btn btn-sm btn-outline-success me-2" 
                          @click="downloadCertificate(certificate)"
                          title="Download PDF">
                          <i class="fas fa-download"></i>
                        </button>
                        <button 
                          v-if="certificate.status === 'pending'"
                          class="btn btn-sm btn-outline-primary me-2" 
                          @click="generateCertificate(certificate)"
                          title="Generate">
                          <i class="fas fa-cogs"></i>
                        </button>
                        <button 
                          class="btn btn-sm btn-outline-warning" 
                          @click="printCertificate()"
                          title="Print">
                          <i class="fas fa-print"></i>
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

    <!-- Generate Certificate Modal -->
    <div class="modal fade" id="generateModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Generate Assessment Certificate</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createCertificate">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Request Number *</label>
                  <input type="text" v-model="certificateForm.requestNumber" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Property Owner *</label>
                  <input type="text" v-model="certificateForm.propertyOwner" class="form-control" required>
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-md-12">
                  <label class="form-label">Property Address *</label>
                  <input type="text" v-model="certificateForm.propertyAddress" class="form-control" required>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Municipality *</label>
                  <select v-model="certificateForm.municipality" class="form-select" required>
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
                <div class="col-md-6">
                  <label class="form-label">Classification *</label>
                  <select v-model="certificateForm.classification" class="form-select" required>
                    <option value="">Select Classification</option>
                    <option value="Residential">Residential</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Industrial">Industrial</option>
                    <option value="Agricultural">Agricultural</option>
                  </select>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Assessed Value (₱) *</label>
                  <input type="number" v-model="certificateForm.assessedValue" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Market Value (₱) *</label>
                  <input type="number" v-model="certificateForm.marketValue" class="form-control" required>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Assessment Notes</label>
                <textarea v-model="certificateForm.notes" class="form-control" rows="3" 
                          placeholder="Additional notes or remarks..."></textarea>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Generate Certificate</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Certificate Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Certificate Preview - {{ selectedCertificate?.certificateNumber }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div id="certificate-content" class="certificate-preview">
              <div class="certificate-header text-center mb-4">
                <img src="/images/PROVINCIAL OF AURORA.jpg" alt="Provincial Seal" class="seal mb-3" style="width: 100px;">
                <h3>REPUBLIC OF THE PHILIPPINES</h3>
                <h4>PROVINCE OF AURORA</h4>
                <h5>PROVINCIAL ASSESSOR'S OFFICE</h5>
                <hr class="my-4">
                <h4 class="text-primary">CERTIFICATE OF REAL PROPERTY ASSESSMENT</h4>
              </div>

              <div v-if="selectedCertificate" class="certificate-body">
                <div class="row mb-4">
                  <div class="col-md-6">
                    <p><strong>Certificate No.:</strong> {{ selectedCertificate.certificateNumber }}</p>
                    <p><strong>Date Issued:</strong> {{ formatDate(selectedCertificate.dateGenerated) }}</p>
                  </div>
                  <div class="col-md-6 text-end">
                    <p><strong>Request No.:</strong> {{ selectedCertificate.requestNumber }}</p>
                    <p><strong>Municipality:</strong> {{ selectedCertificate.municipality }}</p>
                  </div>
                </div>

                <div class="property-details mb-4">
                  <h5 class="border-bottom pb-2">PROPERTY INFORMATION</h5>
                  <p><strong>Property Owner:</strong> {{ selectedCertificate.propertyOwner }}</p>
                  <p><strong>Property Address:</strong> {{ selectedCertificate.propertyAddress }}</p>
                  <p><strong>Classification:</strong> {{ selectedCertificate.classification }}</p>
                </div>

                <div class="assessment-details mb-4">
                  <h5 class="border-bottom pb-2">ASSESSMENT DETAILS</h5>
                  <div class="row">
                    <div class="col-md-6">
                      <p><strong>Assessed Value:</strong> ₱{{ formatCurrency(selectedCertificate.assessedValue) }}</p>
                    </div>
                    <div class="col-md-6">
                      <p><strong>Market Value:</strong> ₱{{ formatCurrency(selectedCertificate.marketValue) }}</p>
                    </div>
                  </div>
                  <p v-if="selectedCertificate.notes"><strong>Notes:</strong> {{ selectedCertificate.notes }}</p>
                </div>

                <div class="certification mt-5">
                  <p>This is to certify that the above information is true and correct based on the records of this office.</p>
                  
                  <div class="row mt-5">
                    <div class="col-md-6">
                      <div class="signature-block text-center">
                        <div class="signature-line mb-2"></div>
                        <p class="mb-0"><strong>EVALUATOR</strong></p>
                        <p class="text-sm">{{ currentUser?.name || 'Authorized Evaluator' }}</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="signature-block text-center">
                        <div class="signature-line mb-2"></div>
                        <p class="mb-0"><strong>PROVINCIAL ASSESSOR</strong></p>
                        <p class="text-sm">Province of Aurora</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="downloadCertificate(selectedCertificate)">
              <i class="fas fa-download me-2"></i>Download PDF
            </button>
            <button type="button" class="btn btn-success" @click="printCertificate()">
              <i class="fas fa-print me-2"></i>Print
            </button>
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
  name: 'Certificates',
  setup() {
    const certificates = ref([]);
    const selectedCertificate = ref(null);
    const currentUser = ref(AuthService.getCurrentUser());

    const filters = ref({
      status: '',
      municipality: '',
      dateFrom: '',
      dateTo: ''
    });

    const certificateForm = ref({
      requestNumber: '',
      propertyOwner: '',
      propertyAddress: '',
      municipality: '',
      classification: '',
      assessedValue: 0,
      marketValue: 0,
      notes: ''
    });

    // Statistics
    const generatedCertificates = computed(() => 
      certificates.value.filter(c => c.status === 'generated').length
    );
    const pendingCertificates = computed(() => 
      certificates.value.filter(c => c.status === 'pending').length
    );
    const downloadedCertificates = computed(() => 
      certificates.value.filter(c => c.status === 'downloaded').length
    );
    const totalCertificates = computed(() => certificates.value.length);

    const filteredCertificates = computed(() => {
      let filtered = certificates.value;
      
      if (filters.value.status) {
        filtered = filtered.filter(c => c.status === filters.value.status);
      }
      if (filters.value.municipality) {
        filtered = filtered.filter(c => c.municipality === filters.value.municipality);
      }
      if (filters.value.dateFrom) {
        filtered = filtered.filter(c => new Date(c.dateGenerated) >= new Date(filters.value.dateFrom));
      }
      if (filters.value.dateTo) {
        filtered = filtered.filter(c => new Date(c.dateGenerated) <= new Date(filters.value.dateTo));
      }
      
      return filtered;
    });

    const fetchCertificates = async () => {
      try {
        const response = await fetch('http://localhost:8080/e_assessment/api/v1/evaluations/certificates', {
          headers: {
            ...AuthService.getAuthHeader(),
            'Content-Type': 'application/json'
          }
        });
        
        if (response.ok) {
          const result = await response.json();
          if (result.status === 'success') {
            certificates.value = result.data || [];
          } else {
            throw new Error(result.message || 'Failed to fetch certificates');
          }
        } else if (response.status === 403) {
          Swal.fire({
            title: 'Access Denied',
            text: 'You do not have permission to access certificates',
            icon: 'error'
          });
          this.$router.push('/dashboard');
        } else {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
      } catch (error) {
        console.error('Error fetching certificates:', error);
        Swal.fire({
          title: 'Error', 
          text: 'Failed to load certificates. Please try again.',
          icon: 'error'
        });
        certificates.value = [];
      }
    };

    const showGenerateModal = () => {
      certificateForm.value = {
        requestNumber: '',
        propertyOwner: '',
        propertyAddress: '',
        municipality: '',
        classification: '',
        assessedValue: 0,
        marketValue: 0,
        notes: ''
      };
      new Modal(document.getElementById('generateModal')).show();
    };

    const createCertificate = async () => {
      try {
        const formData = new FormData();
        formData.append('requestNumber', certificateForm.value.requestNumber);
        formData.append('propertyOwner', certificateForm.value.propertyOwner);
        formData.append('propertyAddress', certificateForm.value.propertyAddress);
        formData.append('municipality', certificateForm.value.municipality);
        formData.append('classification', certificateForm.value.classification);
        formData.append('assessedValue', certificateForm.value.assessedValue);
        formData.append('marketValue', certificateForm.value.marketValue);
        formData.append('notes', certificateForm.value.notes);

        const response = await fetch('http://localhost:8080/e_assessment/api/v1/evaluations/certificate/generate', {
          method: 'POST',
          headers: AuthService.getAuthHeader(),
          body: formData
        });

        const result = await response.json();
        
        if (response.ok && result.status === 'success') {
          Modal.getInstance(document.getElementById('generateModal')).hide();
          
          Swal.fire({
            title: 'Certificate Generated',
            text: `Certificate ${result.data.certificateNumber} has been created successfully`,
            icon: 'success',
            timer: 3000
          });
          
          fetchCertificates(); // Refresh the list
        } else {
          throw new Error(result.message || 'Failed to generate certificate');
        }
      } catch (error) {
        console.error('Error generating certificate:', error);
        Swal.fire('Error', 'Failed to generate certificate', 'error');
      }
    };

    const viewCertificate = (certificate) => {
      selectedCertificate.value = certificate;
      new Modal(document.getElementById('previewModal')).show();
    };

    const generateCertificate = async (certificate) => {
      try {
        certificate.status = 'generated';
        certificate.dateGenerated = new Date().toISOString().split('T')[0];
        
        Swal.fire({
          title: 'Certificate Generated',
          text: 'Certificate has been generated successfully',
          icon: 'success',
          timer: 3000
        });
      } catch (error) {
        Swal.fire('Error', 'Failed to generate certificate', 'error');
      }
    };

    const downloadCertificate = (certificate) => {
      // In a real implementation, this would generate and download a PDF
      certificate.status = 'downloaded';
      
      Swal.fire({
        title: 'Download Started',
        text: 'Certificate PDF download has started',
        icon: 'success',
        timer: 2000
      });
    };

    const printCertificate = () => {
      // Print the certificate content
      const printContent = document.getElementById('certificate-content');
      const originalContent = document.body.innerHTML;
      
      document.body.innerHTML = printContent.outerHTML;
      window.print();
      document.body.innerHTML = originalContent;
      
      // Reload the page to restore functionality
      window.location.reload();
    };

    const getStatusClass = (status) => {
      switch (status) {
        case 'pending': return 'bg-gradient-warning';
        case 'generated': return 'bg-gradient-success';
        case 'downloaded': return 'bg-gradient-info';
        default: return 'bg-gradient-secondary';
      }
    };

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('en-PH').format(amount);
    };

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString();
    };

    onMounted(() => {
      // Check permissions
      if (!PermissionService.canAccess('certificates')) {
        Swal.fire({
          title: 'Access Denied',
          text: 'You do not have permission to access certificates',
          icon: 'error'
        }).then(() => {
          this.$router.push('/dashboard');
        });
        return;
      }
      
      fetchCertificates();
    });

    return {
      certificates,
      selectedCertificate,
      currentUser,
      filters,
      certificateForm,
      generatedCertificates,
      pendingCertificates,
      downloadedCertificates,
      totalCertificates,
      filteredCertificates,
      fetchCertificates,
      showGenerateModal,
      createCertificate,
      viewCertificate,
      generateCertificate,
      downloadCertificate,
      printCertificate,
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

.certificate-preview {
  background: white;
  padding: 2rem;
  border: 1px solid #dee2e6;
}

.seal {
  max-width: 100px;
  height: auto;
}

.signature-block {
  margin-top: 3rem;
}

.signature-line {
  border-bottom: 1px solid #000;
  width: 200px;
  margin: 0 auto;
  height: 50px;
}

@media print {
  .modal-header, .modal-footer {
    display: none !important;
  }
  
  .certificate-preview {
    border: none;
    box-shadow: none;
  }
}
</style>