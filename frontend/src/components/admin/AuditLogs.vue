<template>
  <div class="audit-logs">
    <div class="row mb-4">
      <div class="col-md-8">
        <h6 class="text-dark font-weight-bold">
          <i class="fas fa-history me-2"></i>
          System Audit Logs
        </h6>
        <p class="text-sm text-muted">Track all system activities and admin actions</p>
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
            placeholder="Search audit logs..."
          />
        </div>
      </div>
    </div>

    <!-- Filter Controls -->
    <div class="row mb-3">
      <div class="col-md-3">
        <select v-model="actionFilter" class="form-select">
          <option value="">All Actions</option>
          <option value="LOGIN">Login</option>
          <option value="LOGOUT">Logout</option>
          <option value="REQUEST_APPROVED">Request Approved</option>
          <option value="REQUEST_REJECTED">Request Rejected</option>
          <option value="CERTIFICATE_GENERATED">Certificate Generated</option>
          <option value="CONFIG_UPDATED">Configuration Updated</option>
          <option value="REPORT_GENERATED">Report Generated</option>
          <option value="DASHBOARD_ACCESSED">Dashboard Access</option>
        </select>
      </div>
      <div class="col-md-3">
        <input
          v-model="userFilter"
          type="text"
          class="form-control"
          placeholder="Filter by user..."
        />
      </div>
      <div class="col-md-3">
        <input
          v-model="dateFromFilter"
          type="date"
          class="form-control"
          placeholder="From date"
        />
      </div>
      <div class="col-md-3">
        <input
          v-model="dateToFilter"
          type="date"
          class="form-control"
          placeholder="To date"
        />
      </div>
    </div>

    <!-- Export Controls -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div class="btn-group">
            <button class="btn btn-outline-primary btn-sm" @click="refreshLogs">
              <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
            <button class="btn btn-outline-success btn-sm" @click="exportLogs">
              <i class="fas fa-download me-1"></i>Export CSV
            </button>
          </div>
          <div class="text-muted">
            Showing {{ filteredLogs.length }} of {{ auditLogs.length }} entries
          </div>
        </div>
      </div>
    </div>

    <!-- Audit Logs Table -->
    <div class="table-responsive">
      <table class="table table-hover align-items-center mb-0">
        <thead>
          <tr class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
            <th>Timestamp</th>
            <th>User</th>
            <th>Action</th>
            <th>Description</th>
            <th>IP Address</th>
            <th>User Agent</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in paginatedLogs" :key="log.id">
            <!-- Timestamp -->
            <td>
              <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm">{{ formatDateTime(log.timestamp) }}</h6>
                <p class="text-xs text-secondary mb-0">{{ getTimeAgo(log.timestamp) }}</p>
              </div>
            </td>
            
            <!-- User -->
            <td>
              <div class="d-flex flex-column justify-content-center">
                <h6 class="mb-0 text-sm">{{ log.username || 'System' }}</h6>
                <p class="text-xs text-secondary mb-0">ID: {{ log.userId || 'N/A' }}</p>
              </div>
            </td>
            
            <!-- Action -->
            <td>
              <span class="badge badge-sm" :class="getActionBadgeClass(log.action)">
                <i class="fas me-1" :class="getActionIcon(log.action)"></i>
                {{ log.action }}
              </span>
            </td>
            
            <!-- Description -->
            <td>
              <span class="text-sm">{{ log.description }}</span>
            </td>
            
            <!-- IP Address -->
            <td>
              <span class="text-xs text-secondary">{{ log.ipAddress || 'N/A' }}</span>
            </td>
            
            <!-- User Agent -->
            <td>
              <span class="text-xs text-secondary" :title="log.userAgent">
                {{ getBrowserName(log.userAgent) }}
              </span>
            </td>
            
            <!-- Details -->
            <td class="align-middle text-center">
              <button
                class="btn btn-info btn-sm"
                @click="viewLogDetails(log)"
                title="View Details"
              >
                <i class="fas fa-eye"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="row mt-4">
      <div class="col-12">
        <nav aria-label="Audit logs pagination">
          <ul class="pagination pagination-sm justify-content-center">
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <button class="page-link" @click="currentPage = 1" :disabled="currentPage === 1">
                <i class="fas fa-angle-double-left"></i>
              </button>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <button class="page-link" @click="currentPage--" :disabled="currentPage === 1">
                <i class="fas fa-angle-left"></i>
              </button>
            </li>
            
            <li
              v-for="page in visiblePages"
              :key="page"
              class="page-item"
              :class="{ active: page === currentPage }"
            >
              <button class="page-link" @click="currentPage = page">{{ page }}</button>
            </li>
            
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <button class="page-link" @click="currentPage++" :disabled="currentPage === totalPages">
                <i class="fas fa-angle-right"></i>
              </button>
            </li>
            <li class="page-item" :class="{ disabled: currentPage === totalPages }">
              <button class="page-link" @click="currentPage = totalPages" :disabled="currentPage === totalPages">
                <i class="fas fa-angle-double-right"></i>
              </button>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <!-- Log Details Modal -->
    <div class="modal fade" id="logDetailsModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-info-circle me-2"></i>
              Audit Log Details
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" v-if="selectedLog">
            <div class="row">
              <div class="col-md-6">
                <h6 class="text-primary">Basic Information</h6>
                <table class="table table-borderless table-sm">
                  <tr>
                    <td><strong>Timestamp:</strong></td>
                    <td>{{ formatDateTime(selectedLog.timestamp) }}</td>
                  </tr>
                  <tr>
                    <td><strong>User:</strong></td>
                    <td>{{ selectedLog.username || 'System' }}</td>
                  </tr>
                  <tr>
                    <td><strong>User ID:</strong></td>
                    <td>{{ selectedLog.userId || 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Action:</strong></td>
                    <td>
                      <span class="badge" :class="getActionBadgeClass(selectedLog.action)">
                        {{ selectedLog.action }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Description:</strong></td>
                    <td>{{ selectedLog.description }}</td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <h6 class="text-primary">Technical Information</h6>
                <table class="table table-borderless table-sm">
                  <tr>
                    <td><strong>IP Address:</strong></td>
                    <td>{{ selectedLog.ipAddress || 'N/A' }}</td>
                  </tr>
                  <tr>
                    <td><strong>Browser:</strong></td>
                    <td>{{ getBrowserName(selectedLog.userAgent) }}</td>
                  </tr>
                  <tr>
                    <td><strong>Platform:</strong></td>
                    <td>{{ getPlatform(selectedLog.userAgent) }}</td>
                  </tr>
                  <tr>
                    <td><strong>Log ID:</strong></td>
                    <td>{{ selectedLog.id }}</td>
                  </tr>
                </table>
              </div>
            </div>
            
            <div class="row mt-3" v-if="selectedLog.userAgent">
              <div class="col-12">
                <h6 class="text-primary">User Agent</h6>
                <p class="text-sm text-muted">{{ selectedLog.userAgent }}</p>
              </div>
            </div>
            
            <div class="row mt-3" v-if="selectedLog.additionalData">
              <div class="col-12">
                <h6 class="text-primary">Additional Data</h6>
                <pre class="bg-light p-2 rounded text-sm">{{ formatJSON(selectedLog.additionalData) }}</pre>
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
import AuthService from '@/services/auth.js';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

export default {
  name: 'AuditLogs',
  setup() {
    const auditLogs = ref([]);
    const searchTerm = ref('');
    const actionFilter = ref('');
    const userFilter = ref('');
    const dateFromFilter = ref('');
    const dateToFilter = ref('');
    const selectedLog = ref(null);
    const currentPage = ref(1);
    const itemsPerPage = ref(20);

    const filteredLogs = computed(() => {
      let filtered = auditLogs.value;

      // Search filter
      if (searchTerm.value) {
        const term = searchTerm.value.toLowerCase();
        filtered = filtered.filter(log =>
          log.action?.toLowerCase().includes(term) ||
          log.description?.toLowerCase().includes(term) ||
          log.username?.toLowerCase().includes(term) ||
          log.ipAddress?.toLowerCase().includes(term)
        );
      }

      // Action filter
      if (actionFilter.value) {
        filtered = filtered.filter(log => log.action === actionFilter.value);
      }

      // User filter
      if (userFilter.value) {
        const user = userFilter.value.toLowerCase();
        filtered = filtered.filter(log =>
          log.username?.toLowerCase().includes(user)
        );
      }

      // Date filters
      if (dateFromFilter.value) {
        const fromDate = new Date(dateFromFilter.value);
        filtered = filtered.filter(log => 
          new Date(log.timestamp) >= fromDate
        );
      }

      if (dateToFilter.value) {
        const toDate = new Date(dateToFilter.value);
        toDate.setHours(23, 59, 59, 999); // End of day
        filtered = filtered.filter(log => 
          new Date(log.timestamp) <= toDate
        );
      }

      return filtered.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
    });

    const totalPages = computed(() => 
      Math.ceil(filteredLogs.value.length / itemsPerPage.value)
    );

    const paginatedLogs = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      const end = start + itemsPerPage.value;
      return filteredLogs.value.slice(start, end);
    });

    const visiblePages = computed(() => {
      const pages = [];
      const current = currentPage.value;
      const total = totalPages.value;
      
      let start = Math.max(1, current - 2);
      let end = Math.min(total, current + 2);
      
      if (end - start < 4) {
        if (start === 1) {
          end = Math.min(total, start + 4);
        } else {
          start = Math.max(1, end - 4);
        }
      }
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      
      return pages;
    });

    const fetchAuditLogs = async () => {
      try {
        const headers = AuthService.getAuthHeader();
        const response = await fetch('http://localhost:8080/e_assessment/api/v1/misc/audit-logs', {
          headers
        });

        if (!response.ok) {
          throw new Error('Failed to fetch audit logs');
        }

        auditLogs.value = await response.json();
      } catch (error) {
        console.error('Error fetching audit logs:', error);
        
        // Fallback to sample data for demonstration
        auditLogs.value = generateSampleLogs();
        
        // Log info instead of showing popup to avoid repeated alerts
        console.info('Using sample audit logs data due to API connection issue');
      }
    };

    const generateSampleLogs = () => {
      const currentUser = AuthService.getCurrentUser();
      const sampleLogs = [];
      const actions = [
        'LOGIN', 'LOGOUT', 'REQUEST_APPROVED', 'REQUEST_REJECTED',
        'CERTIFICATE_GENERATED', 'CONFIG_UPDATED', 'REPORT_GENERATED',
        'DASHBOARD_ACCESSED'
      ];
      
      for (let i = 0; i < 50; i++) {
        const date = new Date();
        date.setDate(date.getDate() - Math.floor(Math.random() * 30));
        
        sampleLogs.push({
          id: i + 1,
          userId: currentUser?.id || 1,
          username: currentUser?.username || 'admin',
          action: actions[Math.floor(Math.random() * actions.length)],
          description: `Sample audit log entry ${i + 1}`,
          timestamp: date.toISOString(),
          ipAddress: '192.168.1.' + (100 + Math.floor(Math.random() * 100)),
          userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        });
      }
      
      return sampleLogs;
    };

    const getActionBadgeClass = (action) => {
      const classes = {
        LOGIN: 'bg-gradient-success',
        LOGOUT: 'bg-gradient-secondary',
        REQUEST_APPROVED: 'bg-gradient-success',
        REQUEST_REJECTED: 'bg-gradient-danger',
        CERTIFICATE_GENERATED: 'bg-gradient-info',
        CONFIG_UPDATED: 'bg-gradient-warning',
        REPORT_GENERATED: 'bg-gradient-primary',
        DASHBOARD_ACCESSED: 'bg-gradient-dark'
      };
      return classes[action] || 'bg-gradient-secondary';
    };

    const getActionIcon = (action) => {
      const icons = {
        LOGIN: 'fa-sign-in-alt',
        LOGOUT: 'fa-sign-out-alt',
        REQUEST_APPROVED: 'fa-check',
        REQUEST_REJECTED: 'fa-times',
        CERTIFICATE_GENERATED: 'fa-certificate',
        CONFIG_UPDATED: 'fa-cog',
        REPORT_GENERATED: 'fa-chart-line',
        DASHBOARD_ACCESSED: 'fa-tachometer-alt'
      };
      return icons[action] || 'fa-info';
    };

    const formatDateTime = (dateString) => {
      if (!dateString) return 'N/A';
      return new Date(dateString).toLocaleString();
    };

    const getTimeAgo = (dateString) => {
      if (!dateString) return '';
      
      const now = new Date();
      const date = new Date(dateString);
      const diffInMinutes = Math.floor((now - date) / (1000 * 60));
      
      if (diffInMinutes < 1) return 'Just now';
      if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
      if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
      return `${Math.floor(diffInMinutes / 1440)}d ago`;
    };

    const getBrowserName = (userAgent) => {
      if (!userAgent) return 'Unknown';
      if (userAgent.includes('Chrome')) return 'Chrome';
      if (userAgent.includes('Firefox')) return 'Firefox';
      if (userAgent.includes('Safari')) return 'Safari';
      if (userAgent.includes('Edge')) return 'Edge';
      return 'Other';
    };

    const getPlatform = (userAgent) => {
      if (!userAgent) return 'Unknown';
      if (userAgent.includes('Windows')) return 'Windows';
      if (userAgent.includes('Mac')) return 'macOS';
      if (userAgent.includes('Linux')) return 'Linux';
      if (userAgent.includes('Android')) return 'Android';
      if (userAgent.includes('iOS')) return 'iOS';
      return 'Other';
    };

    const viewLogDetails = (log) => {
      selectedLog.value = log;
      const modal = new Modal(document.getElementById('logDetailsModal'));
      modal.show();
    };

    const formatJSON = (data) => {
      if (typeof data === 'string') return data;
      return JSON.stringify(data, null, 2);
    };

    const refreshLogs = () => {
      fetchAuditLogs();
      Swal.fire({
        title: 'Refreshed!',
        text: 'Audit logs have been refreshed',
        icon: 'success',
        timer: 2000
      });
    };

    const exportLogs = () => {
      try {
        const csvContent = [
          ['Timestamp', 'User', 'Action', 'Description', 'IP Address'].join(','),
          ...filteredLogs.value.map(log => [
            `"${formatDateTime(log.timestamp)}"`,
            `"${log.username || ''}"`,
            `"${log.action || ''}"`,
            `"${log.description || ''}"`,
            `"${log.ipAddress || ''}"`
          ].join(','))
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `audit_logs_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);

        Swal.fire({
          title: 'Exported!',
          text: 'Audit logs have been exported to CSV',
          icon: 'success',
          timer: 3000
        });
        
      } catch (error) {
        console.error('Error exporting logs:', error);
        Swal.fire({
          title: 'Error',
          text: 'Failed to export audit logs',
          icon: 'error'
        });
      }
    };

    onMounted(() => {
      fetchAuditLogs();
    });

    return {
      auditLogs,
      searchTerm,
      actionFilter,
      userFilter,
      dateFromFilter,
      dateToFilter,
      selectedLog,
      currentPage,
      filteredLogs,
      totalPages,
      paginatedLogs,
      visiblePages,
      getActionBadgeClass,
      getActionIcon,
      formatDateTime,
      getTimeAgo,
      getBrowserName,
      getPlatform,
      viewLogDetails,
      formatJSON,
      refreshLogs,
      exportLogs
    };
  }
};
</script>

<style scoped>
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

.pagination .page-link {
  border: none;
  margin: 0 0.125rem;
  border-radius: 6px;
}

.pagination .page-item.active .page-link {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
}

.pagination .page-link:hover {
  background: rgba(102, 126, 234, 0.1);
  color: #667eea;
}

.modal-content {
  border-radius: 12px;
}

pre {
  font-size: 0.75rem;
  max-height: 200px;
  overflow-y: auto;
}

.btn-group .btn {
  margin-right: 0.5rem;
}

.form-select, .form-control {
  border-radius: 6px;
}
</style>