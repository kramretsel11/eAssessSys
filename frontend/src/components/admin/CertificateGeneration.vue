<template>
  <div class="certificate-generation">
    <div class="row mb-4">
      <div class="col-md-8">
        <h6 class="text-dark font-weight-bold">
          <i class="fas fa-certificate me-2"></i>
          Certificate Generation
        </h6>
        <p class="text-sm text-muted">Generate certificates for approved assessment requests</p>
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
            placeholder="Search approved requests..."
          />
        </div>
      </div>
    </div>

    <!-- Certificate Types -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="alert alert-info">
          <h6><i class="fas fa-info-circle me-2"></i>Available Certificate Types:</h6>
          <ul class="mb-0">
            <li><strong>Property Ownership Certificate</strong> - Certifies property ownership details</li>
            <li><strong>Tax Declaration Certificate</strong> - Certifies tax declaration information</li>
            <li><strong>Property Holding Certificate</strong> - Certifies property holdings</li>
            <li><strong>No Property Holding Certificate</strong> - Certifies no property holdings</li>
            <li><strong>Improvement Certificate</strong> - Certifies property improvements</li>
            <li><strong>No Improvement Certificate</strong> - Certifies no property improvements</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Approved Requests Table -->
    <div class="table-responsive">
      <table class="table table-hover align-items-center mb-0">
        <thead>
          <tr class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
            <th>Request Info</th>
            <th>Owner</th>
            <th>Property Location</th>
            <th>Approved Date</th>
            <th>Generated Certificates</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="request in filteredApprovedRequests" :key="request.id">
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
            
            <!-- Approved Date -->
            <td>
              <span class="text-secondary text-xs font-weight-bold">
                {{ formatDate(request.reviewedAt || request.updated_at) }}
              </span>
            </td>
            
            <!-- Generated Certificates -->
            <td>
              <div class="d-flex flex-wrap gap-1">
                <span
                  v-for="cert in getCertificateStatus(request.id)"
                  :key="cert.type"
                  class="badge badge-sm"
                  :class="cert.generated ? 'bg-success' : 'bg-secondary'"
                >
                  {{ cert.name }}
                </span>
              </div>
            </td>
            
            <!-- Actions -->
            <td class="align-middle text-center">
              <div class="dropdown">
                <button
                  class="btn btn-primary btn-sm dropdown-toggle"
                  type="button"
                  :id="`certificateDropdown${request.id}`"
                  data-bs-toggle="dropdown"
                >
                  <i class="fas fa-certificate me-1"></i>Generate
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a class="dropdown-item" @click="generateCertificate(request, 'ownership')">
                      <i class="fas fa-home me-2"></i>Property Ownership
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" @click="generateCertificate(request, 'tax_declaration')">
                      <i class="fas fa-receipt me-2"></i>Tax Declaration
                    </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item" @click="generateCertificate(request, 'property_holding')">
                      <i class="fas fa-building me-2"></i>Property Holding
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" @click="generateCertificate(request, 'no_property_holding')">
                      <i class="fas fa-ban me-2"></i>No Property Holding
                    </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item" @click="generateCertificate(request, 'improvement')">
                      <i class="fas fa-tools me-2"></i>Improvement
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" @click="generateCertificate(request, 'no_improvement')">
                      <i class="fas fa-times me-2"></i>No Improvement
                    </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item" @click="printTaxDeclaration(request)">
                      <i class="fas fa-print me-2"></i>Print TAX DECLARATION
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" @click="printPAAS(request)">
                      <i class="fas fa-print me-2"></i>Print PAAS
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Certificate Preview Modal -->
    <div class="modal fade" id="certificatePreviewModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-certificate me-2"></i>
              Certificate Preview - {{ previewCertificateType }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="certificatePreview" class="certificate-preview">
              <div class="text-center mb-4">
                <h4>REPUBLIC OF THE PHILIPPINES</h4>
                <h5>PROVINCE OF AURORA</h5>
                <h6>{{ selectedRequest?.municipality?.toUpperCase() }}</h6>
                <hr>
                <h5 class="text-primary">{{ getCertificateTitle(previewCertificateType) }}</h5>
              </div>
              
              <div class="certificate-body" v-html="certificateContent"></div>
              
              <div class="signature-section mt-5">
                <div class="row">
                  <div class="col-6">
                    <p class="text-center">
                      <strong>Date Issued:</strong><br>
                      {{ new Date().toLocaleDateString() }}
                    </p>
                  </div>
                  <div class="col-6">
                    <p class="text-center">
                      <strong>Issued by:</strong><br>
                      {{ currentUser?.username || 'Admin' }}<br>
                      <small>Aurora Province Assessor</small>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="downloadCertificate">
              <i class="fas fa-download me-2"></i>Download PDF
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
  name: 'CertificateGeneration',
  emits: ['certificate-generated'],
  setup(props, { emit }) {
    const approvedRequests = ref([]);
    const searchTerm = ref('');
    const generatedCertificates = ref({}); // Track generated certificates
    const selectedRequest = ref(null);
    const previewCertificateType = ref('');
    const certificateContent = ref('');
    const currentUser = ref(AuthService.getCurrentUser());

    const filteredApprovedRequests = computed(() => {
      let filtered = approvedRequests.value;

      if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        filtered = filtered.filter(request =>
          request.arpNo?.toLowerCase().includes(term) ||
          request.pin?.toLowerCase().includes(term) ||
          request.ownerName?.toLowerCase().includes(term) ||
          request.municipality?.toLowerCase().includes(term)
        );
      }

      return filtered;
    });

    const fetchApprovedRequests = async () => {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await fetch('/e_assessment/api/v1/misc/assessment-requests?status=approved', {
          headers
        });

        if (!response.ok) {
          throw new Error('Failed to fetch approved requests');
        }

        const allRequests = await response.json();
        approvedRequests.value = allRequests.filter(request => request.status === 'approved');
      } catch (error) {
        console.error('Error fetching approved requests:', error);
        // Swal.fire({
        //   title: 'Error',
        //   text: 'Failed to load approved requests',
        //   icon: 'error'
        // });
      }
    };

    const getCertificateStatus = (requestId) => {
      const certificates = [
        { type: 'ownership', name: 'Property Ownership' },
        { type: 'tax_declaration', name: 'Tax Declaration' },
        { type: 'property_holding', name: 'Property Holding' },
        { type: 'no_property_holding', name: 'No Property Holding' },
        { type: 'improvement', name: 'Improvement' },
        { type: 'no_improvement', name: 'No Improvement' }
      ];

      return certificates.map(cert => ({
        ...cert,
        generated: generatedCertificates.value[requestId]?.includes(cert.type) || false
      }));
    };

    const formatDate = (dateString) => {
      if (!dateString) return 'N/A';
      return new Date(dateString).toLocaleDateString();
    };

    const getCertificateTitle = (type) => {
      const titles = {
        ownership: 'PROPERTY OWNERSHIP CERTIFICATE',
        tax_declaration: 'TAX DECLARATION CERTIFICATE',
        property_holding: 'PROPERTY HOLDING CERTIFICATE',
        no_property_holding: 'NO PROPERTY HOLDING CERTIFICATE',
        improvement: 'PROPERTY IMPROVEMENT CERTIFICATE',
        no_improvement: 'NO PROPERTY IMPROVEMENT CERTIFICATE'
      };
      return titles[type] || 'CERTIFICATE';
    };

    const generateCertificateContent = (request, type) => {
      const commonInfo = `
        <p><strong>Owner Name:</strong> ${request.ownerName}</p>
        <p><strong>Property Address:</strong> ${request.street || ''} Barangay ${request.barangay}, ${request.municipality}, Aurora</p>
        <p><strong>ARP No:</strong> ${request.arpNo || 'N/A'}</p>
        <p><strong>PIN:</strong> ${request.pin || 'N/A'}</p>
      `;

      const templates = {
        ownership: `
          <p>TO WHOM IT MAY CONCERN:</p>
          <p>This is to certify that based on records available in this office, the property described below is registered under the name of:</p>
          ${commonInfo}
          <p><strong>Property Area:</strong> ${request.areaNo || 'N/A'} square meters</p>
          <p><strong>OCT/TCT/CLOA No:</strong> ${request.octTctCloaNo || 'N/A'}</p>
          <p>This certification is issued for whatever legal purpose it may serve.</p>
        `,
        tax_declaration: `
          <p>TO WHOM IT MAY CONCERN:</p>
          <p>This is to certify that the property described below has a valid Tax Declaration on file:</p>
          ${commonInfo}
          <p><strong>Property Classification:</strong> ${request.categoryId || 'N/A'}</p>
          <p><strong>Market Value:</strong> ₱${request.marketValue || '0.00'}</p>
          <p><strong>Assessed Value:</strong> ₱${request.assessedValue || '0.00'}</p>
          <p>This certification is issued upon request of the interested party for legal purposes.</p>
        `,
        property_holding: `
          <p>TO WHOM IT MAY CONCERN:</p>
          <p>This is to certify that according to our records, the person named below holds property within the jurisdiction of Aurora Province:</p>
          ${commonInfo}
          <p><strong>Property Type:</strong> Real Property</p>
          <p><strong>Current Status:</strong> Property Holder</p>
          <p>This certification is issued for reference and legal purposes.</p>
        `,
        no_property_holding: `
          <p>TO WHOM IT MAY CONCERN:</p>
          <p>This is to certify that according to our records, <strong>${request.ownerName}</strong> does not hold any registered property within the jurisdiction of Aurora Province as of this date.</p>
          <p>This certification is based on available records in this office and is issued for legal purposes.</p>
        `,
        improvement: `
          <p>TO WHOM IT MAY CONCERN:</p>
          <p>This is to certify that the property described below has recorded improvements:</p>
          ${commonInfo}
          <p><strong>Building Type:</strong> ${request.generalDescription?.kindOfBldg || 'N/A'}</p>
          <p><strong>Floor Area:</strong> ${request.generalDescription?.totalFloorArea || 'N/A'} sq.m.</p>
          <p><strong>Date Constructed:</strong> ${request.generalDescription?.dateConstructed || 'N/A'}</p>
          <p>This certification is issued upon request for legal purposes.</p>
        `,
        no_improvement: `
          <p>TO WHOM IT MAY CONCERN:</p>
          <p>This is to certify that according to our records, the property described below has no recorded improvements:</p>
          ${commonInfo}
          <p><strong>Land Classification:</strong> Vacant Lot</p>
          <p><strong>Improvement Status:</strong> No Improvements</p>
          <p>This certification is based on available records and issued for legal purposes.</p>
        `
      };

      return templates[type] || '';
    };

    const generateCertificate = async (request, type) => {
      selectedRequest.value = request;
      previewCertificateType.value = type;
      certificateContent.value = generateCertificateContent(request, type);
      
      const modal = new Modal(document.getElementById('certificatePreviewModal'));
      modal.show();
    };

    const downloadCertificate = async () => {
      try {
        const headers = AuthService.getAuthHeader();
        
        const certificateData = {
          requestId: selectedRequest.value.id,
          type: previewCertificateType.value,
          title: getCertificateTitle(previewCertificateType.value),
          content: certificateContent.value,
          issuedBy: currentUser.value.username,
          issuedDate: new Date().toISOString()
        };

        const response = await fetch('/e_assessment/api/v1/misc/certificates/generate', {
          method: 'POST',
          headers: {
            ...headers,
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(certificateData)
        });

        if (!response.ok) {
          throw new Error('Failed to generate certificate');
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `${previewCertificateType.value}_certificate_${selectedRequest.value.id}_${new Date().toISOString().split('T')[0]}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        // Track generated certificate
        const requestId = selectedRequest.value.id;
        if (!generatedCertificates.value[requestId]) {
          generatedCertificates.value[requestId] = [];
        }
        if (!generatedCertificates.value[requestId].includes(previewCertificateType.value)) {
          generatedCertificates.value[requestId].push(previewCertificateType.value);
        }

        // Close modal
        const modal = Modal.getInstance(document.getElementById('certificatePreviewModal'));
        modal.hide();

        emit('certificate-generated', getCertificateTitle(previewCertificateType.value), selectedRequest.value.id);

        Swal.fire({
          title: 'Success!',
          text: 'Certificate generated and downloaded successfully',
          icon: 'success',
          timer: 3000
        });

      } catch (error) {
        console.error('Error generating certificate:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to generate certificate',
          icon: 'error'
        });
      }
    };

    const printTaxDeclaration = async (request) => {
      try {
        const headers = AuthService.getAuthHeader();
        
        const response = await fetch(`/e_assessment/api/v1/misc/certificates/tax-declaration/${request.id}`, {
          method: 'POST',
          headers
        });

        if (!response.ok) {
          throw new Error('Failed to generate Tax Declaration');
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `TAX_DECLARATION_${request.id}_${new Date().toISOString().split('T')[0]}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        emit('certificate-generated', 'Tax Declaration', request.id);

        Swal.fire({
          title: 'Success!',
          text: 'Tax Declaration generated successfully',
          icon: 'success',
          timer: 3000
        });

      } catch (error) {
        console.error('Error generating Tax Declaration:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to generate Tax Declaration',
          icon: 'error'
        });
      }
    };

    const printPAAS = async (request) => {
      try {
        const headers = AuthService.getAuthHeader();
        
        // Determine PAAS type based on property
        let paasType = 'LAND';
        if (request.generalDescription?.kindOfBldg) {
          paasType = 'BUILDING';
        }
        // Add logic for MACHINERY if applicable
        
        const response = await fetch(`/e_assessment/api/v1/misc/certificates/paas/${request.id}?type=${paasType}`, {
          method: 'POST',
          headers
        });

        if (!response.ok) {
          throw new Error('Failed to generate PAAS');
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `PAAS_${paasType}_${request.id}_${new Date().toISOString().split('T')[0]}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        emit('certificate-generated', `PAAS ${paasType}`, request.id);

        Swal.fire({
          title: 'Success!',
          text: `PAAS ${paasType} generated successfully`,
          icon: 'success',
          timer: 3000
        });

      } catch (error) {
        console.error('Error generating PAAS:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to generate PAAS',
          icon: 'error'
        });
      }
    };

    onMounted(() => {
      fetchApprovedRequests();
    });

    return {
      approvedRequests,
      searchTerm,
      filteredApprovedRequests,
      selectedRequest,
      previewCertificateType,
      certificateContent,
      currentUser,
      getCertificateStatus,
      formatDate,
      getCertificateTitle,
      generateCertificate,
      downloadCertificate,
      printTaxDeclaration,
      printPAAS
    };
  }
};
</script>

<style scoped>
.certificate-preview {
  border: 2px solid #dee2e6;
  padding: 2rem;
  background: white;
  font-family: 'Times New Roman', serif;
}

.certificate-body {
  line-height: 1.8;
  text-align: justify;
}

.signature-section {
  border-top: 1px solid #dee2e6;
  padding-top: 2rem;
}

.dropdown-menu {
  border-radius: 8px;
  border: none;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.dropdown-item {
  padding: 0.75rem 1rem;
}

.dropdown-item:hover {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.badge {
  font-size: 0.6rem;
  padding: 0.25rem 0.5rem;
  margin: 0.125rem;
}

.table th {
  font-weight: 700;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.alert {
  border-radius: 12px;
  border: none;
}
</style>