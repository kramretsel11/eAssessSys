import AuthService from './auth.js';

/**
 * Permission Service for Role-Based Access Control
 */
export const PermissionService = {
  
  /**
   * Permission definitions based on user roles
   */
  PERMISSIONS: {
    // Super Admin (role: 1)
    '1': {
      navigation: ['dashboard', 'admin-dashboard', 'users', 'audit-logs', 'settings', 'configuration', 'reports', 'certificates'],
      actions: ['create_user', 'edit_user', 'delete_user', 'view_audit', 'system_config', 'manage_all'],
      data: ['all_municipalities', 'all_users', 'system_stats']
    },
    
    // Municipality Coordinator (role: 2) 
    '2': {
      navigation: ['dashboard', 'requests', 'reports', 'settings'],
      actions: ['coordinate_assessments', 'review_evaluations', 'generate_reports', 'manage_workflows'],
      data: ['municipality_data', 'assessment_requests']
    },
    
    // Municipality User (role: 3)
    '3': {
      navigation: ['dashboard', 'requests', 'settings'],
      actions: ['submit_requests', 'track_status', 'update_property', 'view_data'],
      data: ['own_municipality', 'own_requests']
    },
    
    // Evaluator (role: 4)
    '4': {
      navigation: ['dashboard', 'evaluator-dashboard', 'evaluations', 'certificates', 'settings'],
      actions: ['evaluate_assessments', 'approve_requests', 'reject_requests', 'generate_certificates'],
      data: ['assigned_evaluations', 'property_details']
    }
  },

  /**
   * Role display names
   */
  ROLE_NAMES: {
    '1': 'Super Admin',
    '2': 'Municipality Coordinator', 
    '3': 'Municipality User',
    '4': 'Evaluator'
  },

  /**
   * Get current user's role
   */
  getCurrentUserRole() {
    const user = AuthService.getUser();
    console.log('Current user data:', user); // Debug log
    
    // Handle different possible role field names and formats
    let role = user?.userType || user?.role || user?.user_type;
    
    // Handle string vs number conversion
    if (role) {
      role = role.toString();
    }
    
    console.log('Detected role:', role); // Debug log
    return role;
  },

  /**
   * Get current user's permissions
   */
  getCurrentUserPermissions() {
    const role = this.getCurrentUserRole();
    return this.PERMISSIONS[role?.toString()] || { navigation: [], actions: [], data: [] };
  },

  /**
   * Check if current user has specific navigation permission
   */
  canAccess(page) {
    const permissions = this.getCurrentUserPermissions();
    const role = this.getCurrentUserRole();
    const user = AuthService.getUser();
    
    console.log(`Permission check for page: ${page}`);
    console.log(`User role: ${role}`);
    console.log(`Available permissions:`, permissions);
    console.log(`Navigation permissions:`, permissions.navigation);
    console.log(`Page included?`, permissions.navigation.includes(page));
    
    const hasPermission = permissions.navigation.includes(page);
    
    // Fallback: If role detection fails but user is authenticated, allow basic pages
    if (!hasPermission && user && AuthService.isAuthenticated()) {
      const basicPages = ['dashboard', 'requests', 'settings'];
      if (basicPages.includes(page)) {
        console.log(`Fallback access granted for ${page}`);
        return true;
      }
    }
    
    return hasPermission;
  },

  /**
   * Check if current user can perform specific action
   */
  canPerform(action) {
    const permissions = this.getCurrentUserPermissions();
    return permissions.actions.includes(action);
  },

  /**
   * Check if current user can access specific data
   */
  canAccessData(dataType) {
    const permissions = this.getCurrentUserPermissions();
    return permissions.data.includes(dataType);
  },

  /**
   * Check if current user is Super Admin
   */
  isSuperAdmin() {
    const role = this.getCurrentUserRole();
    // Handle both string and number formats, and default admin detection
    const user = AuthService.getUser();
    
    // Multiple ways to detect admin/super admin
    return (
      role === '1' || role === 1 || 
      role === 'admin' || 
      user?.role === 'admin' ||
      user?.userType === 1 ||
      user?.userType === '1' ||
      user?.isAdmin === true
    );
  },

  /**
   * Check if current user is Admin (Super Admin)
   */
  isAdmin() {
    return this.isSuperAdmin();
  },

  /**
   * Check if current user is Municipality User
   */
  isMunicipalityUser() {
    const role = this.getCurrentUserRole();
    return role === '3' || role === 3;
  },

  /**
   * Check if current user is Evaluator
   */
  isEvaluator() {
    const role = this.getCurrentUserRole();
    return role === '4' || role === 4;
  },

  /**
   * Check if current user is Municipality Coordinator
   */
  isCoordinator() {
    const role = this.getCurrentUserRole();
    return role === '2' || role === 2;
  },

  /**
   * Get user permissions list for display
   */
  getUserPermissionsList(role) {
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
  },

  /**
   * Get role display name
   */
  getRoleDisplayName(role) {
    return this.ROLE_NAMES[role?.toString()] || 'Unknown Role';
  },

  /**
   * Check multiple permissions at once
   */
  hasAnyPermission(actions) {
    return actions.some(action => this.canPerform(action));
  },

  /**
   * Check if user has all specified permissions
   */
  hasAllPermissions(actions) {
    return actions.every(action => this.canPerform(action));
  },

  /**
   * Get user's municipality restriction
   */
  getUserMunicipality() {
    const user = AuthService.getUser();
    return user?.municipality || null;
  },

  /**
   * Check if user can access specific municipality data
   */
  canAccessMunicipality(municipality) {
    if (this.isSuperAdmin()) {
      return true; // Super admin can access all
    }
    
    const userMunicipality = this.getUserMunicipality();
    return userMunicipality === municipality;
  }
};

export default PermissionService;