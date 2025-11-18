import axios from 'axios';

const API_URL = 'http://localhost:8080/e_assessment/api/v1';

class UserService {
  constructor() {
    // Create axios instance with default config
    this.api = axios.create({
      baseURL: API_URL,
      headers: {
        'Content-Type': 'application/json'
      }
    });

    // Add request interceptor for authentication
    this.api.interceptors.request.use((config) => {
      const token = localStorage.getItem('token');
      if (token) {
        config.headers.Authorization = `Bearer ${token}`;
      }
      return config;
    });

    // Add response interceptor for error handling
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          // Token expired or invalid
          localStorage.removeItem('token');
          localStorage.removeItem('user');
          window.location.href = '/signin';
        }
        return Promise.reject(error);
      }
    );
  }

  /**
   * Get all users with optional filtering
   */
  async getUsers(filters = {}) {
    try {
      const response = await this.api.post('/users/admin/list', filters);
      return response.data;
    } catch (error) {
      console.error('Error fetching users:', error);
      throw new Error(error.response?.data?.message || 'Failed to fetch users');
    }
  }

  /**
   * Create a new user
   */
  async createUser(userData) {
    try {
      const response = await this.api.post('/users/admin/create', userData);
      return response.data;
    } catch (error) {
      console.error('Error creating user:', error);
      throw new Error(error.response?.data?.message || 'Failed to create user');
    }
  }

  /**
   * Update an existing user
   */
  async updateUser(userData) {
    try {
      const response = await this.api.post('/users/admin/update', userData);
      return response.data;
    } catch (error) {
      console.error('Error updating user:', error);
      throw new Error(error.response?.data?.message || 'Failed to update user');
    }
  }

  /**
   * Delete a user (soft delete)
   */
  async deleteUser(userId) {
    try {
      const response = await this.api.post('/users/admin/delete', { id: userId });
      return response.data;
    } catch (error) {
      console.error('Error deleting user:', error);
      throw new Error(error.response?.data?.message || 'Failed to delete user');
    }
  }

  /**
   * Get user types for dropdown
   */
  async getUserTypes() {
    try {
      const response = await this.api.get('/users/admin/types');
      return response.data;
    } catch (error) {
      console.error('Error fetching user types:', error);
      throw new Error(error.response?.data?.message || 'Failed to fetch user types');
    }
  }

  /**
   * Get available municipalities
   */
  getMunicipalities() {
    return [
      'BALER',
      'CASIGURAN',
      'DIPACULAO',
      'DILASAG',
      'DINALUNGAN',
      'DINGALAN',
      'MARIA AURORA',
      'SAN LUIS'
    ];
  }

  /**
   * Get role display name
   */
  getRoleDisplayName(role) {
    const roleMap = {
      '1': 'Super Admin',
      '2': 'Coordinator',
      '3': 'Municipality User',
      '4': 'Evaluator'
    };
    return roleMap[role?.toString()] || 'Unknown';
  }

  /**
   * Get role badge CSS class
   */
  getRoleBadgeClass(role) {
    const classMap = {
      '1': 'bg-gradient-danger',    // Super Admin - Red
      '2': 'bg-gradient-warning',   // Coordinator - Orange
      '3': 'bg-gradient-info',      // Municipality User - Blue
      '4': 'bg-gradient-success'    // Evaluator - Green
    };
    return classMap[role?.toString()] || 'bg-secondary';
  }

  /**
   * Get user permissions based on role
   */
  getUserPermissions(role) {
    const permissions = {
      '1': [
        'Full system access',
        'Manage all users',
        'View audit logs',
        'System configuration',
        'All municipality permissions'
      ],
      '2': [
        'Coordinate assessments',
        'Review evaluations',
        'Generate reports',
        'Manage workflows'
      ],
      '3': [
        'Submit assessment requests',
        'View municipality data',
        'Track request status',
        'Update property information'
      ],
      '4': [
        'Evaluate assessments',
        'Approve/reject requests',
        'Generate certificates',
        'Review property details'
      ]
    };
    return permissions[role?.toString()] || [];
  }
}

export default new UserService();