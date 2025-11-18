// Audit Service for frontend logging
import AuthService from './auth.js';

class AuditService {
  constructor() {
    this.baseURL = process.env.VUE_APP_API_BASE_URL || 'http://localhost/e_assessment/api/v1';
  }

  /**
   * Log an audit action
   */
  async logAction(action, resourceType, resourceId = null, details = '', additionalData = {}) {
    try {
      const user = AuthService.getCurrentUser();
      if (!user) {
        console.warn('Cannot log audit action: User not authenticated');
        return false;
      }

      const auditData = {
        user_id: user.userId || user.id,
        action: action,
        resource_type: resourceType,
        resource_id: resourceId,
        details: details,
        ip_address: additionalData.ip_address || '',
        user_agent: navigator.userAgent,
        ...additionalData
      };

      const headers = AuthService.getAuthHeader();
      const response = await fetch(`${this.baseURL}/misc/audit-logs/create`, {
        method: 'POST',
        headers: {
          ...headers,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(auditData)
      });

      if (!response.ok) {
        console.error('Failed to log audit action:', response.statusText);
        return false;
      }

      return true;
    } catch (error) {
      console.error('Error logging audit action:', error);
      return false;
    }
  }

  /**
   * Get audit logs with pagination and filtering
   */
  async getLogs(params = {}) {
    try {
      const headers = AuthService.getAuthHeader();
      const queryString = new URLSearchParams(params).toString();
      
      const response = await fetch(`${this.baseURL}/misc/audit-logs?${queryString}`, {
        headers
      });

      if (response.ok) {
        return await response.json();
      } else {
        throw new Error(`Failed to fetch audit logs: ${response.statusText}`);
      }
    } catch (error) {
      console.error('Error fetching audit logs:', error);
      throw error;
    }
  }

  /**
   * Export audit logs as CSV
   */
  async exportLogs(params = {}) {
    try {
      const headers = AuthService.getAuthHeader();
      const queryString = new URLSearchParams(params).toString();
      
      const response = await fetch(`${this.baseURL}/misc/audit-logs/export?${queryString}`, {
        headers
      });

      if (response.ok) {
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `audit_logs_${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
        return true;
      } else {
        throw new Error(`Failed to export audit logs: ${response.statusText}`);
      }
    } catch (error) {
      console.error('Error exporting audit logs:', error);
      throw error;
    }
  }

  /**
   * Get audit log statistics
   */
  async getStatistics() {
    try {
      const headers = AuthService.getAuthHeader();
      const response = await fetch(`${this.baseURL}/misc/audit-logs/statistics`, {
        headers
      });

      if (response.ok) {
        return await response.json();
      } else {
        throw new Error(`Failed to fetch audit statistics: ${response.statusText}`);
      }
    } catch (error) {
      console.error('Error fetching audit statistics:', error);
      throw error;
    }
  }

  // Convenience methods for common audit actions
  async logLogin(username) {
    return this.logAction('Login', 'Authentication', null, `User ${username} logged in successfully`);
  }

  async logLogout(username) {
    return this.logAction('Logout', 'Authentication', null, `User ${username} logged out`);
  }

  async logRequestCreate(requestId, details) {
    return this.logAction('Create Request', 'Assessment Request', requestId, details);
  }

  async logRequestUpdate(requestId, details) {
    return this.logAction('Update Request', 'Assessment Request', requestId, details);
  }

  async logRequestApprove(requestId, details) {
    return this.logAction('Approve Request', 'Assessment Request', requestId, details);
  }

  async logRequestReject(requestId, details) {
    return this.logAction('Reject Request', 'Assessment Request', requestId, details);
  }

  async logCertificateGenerate(requestId, certificateType) {
    return this.logAction('Generate Certificate', 'Certificate', requestId, `${certificateType} certificate generated`);
  }

  async logConfigUpdate(configType, details) {
    return this.logAction('Update Configuration', configType, null, details);
  }

  async logReportGenerate(reportType, recordCount) {
    return this.logAction('Generate Report', 'Report', null, `${reportType} report generated with ${recordCount} records`);
  }

  async logDataExport(exportType, recordCount) {
    return this.logAction('Export Data', 'Export', null, `${exportType} exported with ${recordCount} records`);
  }

  async logSystemBackup() {
    return this.logAction('Backup Database', 'System', null, 'Database backup created successfully');
  }
}

export default new AuditService();