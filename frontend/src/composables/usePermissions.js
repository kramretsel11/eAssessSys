import { computed, reactive } from 'vue';
import PermissionService from '../services/permissions.js';
import AuthService from '../services/auth.js';

/**
 * Vue 3 Composable for Permission Management
 */
export function usePermissions() {
  
  // Reactive permission state
  const permissions = reactive({
    userRole: computed(() => PermissionService.getCurrentUserRole()),
    isAdmin: computed(() => PermissionService.isAdmin()),
    isSuperAdmin: computed(() => PermissionService.isSuperAdmin()),
    isMunicipalityUser: computed(() => PermissionService.isMunicipalityUser()),
    isEvaluator: computed(() => PermissionService.isEvaluator()),
    isCoordinator: computed(() => PermissionService.isCoordinator()),
    roleDisplayName: computed(() => {
      const role = PermissionService.getCurrentUserRole();
      return PermissionService.getRoleDisplayName(role);
    }),
    userMunicipality: computed(() => PermissionService.getUserMunicipality())
  });

  /**
   * Check if user can access a specific page/route
   */
  const canAccess = (page) => {
    return PermissionService.canAccess(page);
  };

  /**
   * Check if user can perform a specific action
   */
  const canPerform = (action) => {
    return PermissionService.canPerform(action);
  };

  /**
   * Check if user can access specific data
   */
  const canAccessData = (dataType) => {
    return PermissionService.canAccessData(dataType);
  };

  /**
   * Check if user can access specific municipality data
   */
  const canAccessMunicipality = (municipality) => {
    return PermissionService.canAccessMunicipality(municipality);
  };

  /**
   * Check if user has any of the specified permissions
   */
  const hasAnyPermission = (actions) => {
    return PermissionService.hasAnyPermission(actions);
  };

  /**
   * Check if user has all of the specified permissions
   */
  const hasAllPermissions = (actions) => {
    return PermissionService.hasAllPermissions(actions);
  };

  /**
   * Get user's permission list for display
   */
  const getUserPermissions = (role = null) => {
    const userRole = role || permissions.userRole;
    return PermissionService.getUserPermissionsList(userRole);
  };

  /**
   * Navigate with permission check
   */
  const navigateWithPermission = (router, routeName, fallbackRoute = '/sign-in') => {
    if (canAccess(routeName.toLowerCase())) {
      router.push({ name: routeName });
    } else {
      // Optional: Show permission denied message
      console.warn(`Access denied to ${routeName}`);
      router.push(fallbackRoute);
    }
  };

  /**
   * Execute action with permission check
   */
  const executeWithPermission = (action, callback, deniedCallback = null) => {
    if (canPerform(action)) {
      return callback();
    } else {
      console.warn(`Permission denied for action: ${action}`);
      if (deniedCallback) {
        return deniedCallback();
      }
    }
  };

  /**
   * Filter data based on permissions
   */
  const filterByPermissions = (data, filterFn) => {
    return data.filter(filterFn);
  };

  /**
   * Get navigation items based on user permissions
   */
  const getAuthorizedNavItems = () => {
    const navItems = [
      { name: 'Dashboard', route: 'Dashboard', permission: 'dashboard', icon: 'fas fa-tachometer-alt' },
      { name: 'Requests', route: 'Requests', permission: 'requests', icon: 'fas fa-file-alt' },
      { name: 'Evaluations', route: 'Evaluations', permission: 'evaluations', icon: 'fas fa-clipboard-check' },
      { name: 'Certificates', route: 'Certificates', permission: 'certificates', icon: 'fas fa-certificate' },
      { name: 'Reports', route: 'Reports', permission: 'reports', icon: 'fas fa-chart-bar' },
      { name: 'Admin Dashboard', route: 'Admin Dashboard', permission: 'admin-dashboard', icon: 'fas fa-user-shield' },
      { name: 'User Management', route: 'Users', permission: 'users', icon: 'fas fa-users' },
      { name: 'Audit Logs', route: 'AuditLogs', permission: 'audit-logs', icon: 'fas fa-history' },
      { name: 'Configuration', route: 'Configuration', permission: 'configuration', icon: 'fas fa-cogs' },
      { name: 'Settings', route: 'Settings', permission: 'settings', icon: 'fas fa-cog' }
    ];

    return navItems.filter(item => canAccess(item.permission));
  };

  /**
   * Refresh user permissions (useful after role changes)
   */
  const refreshPermissions = async () => {
    try {
      if (AuthService.getToken()) {
        await AuthService.fetchCurrentUser();
      }
    } catch (error) {
      console.error('Error refreshing permissions:', error);
    }
  };

  return {
    // Reactive permission state
    permissions,
    
    // Permission checking functions
    canAccess,
    canPerform,
    canAccessData,
    canAccessMunicipality,
    hasAnyPermission,
    hasAllPermissions,
    
    // Utility functions
    getUserPermissions,
    navigateWithPermission,
    executeWithPermission,
    filterByPermissions,
    getAuthorizedNavItems,
    refreshPermissions,

    // Direct access to permission properties for template use
    userRole: permissions.userRole,
    isAdmin: permissions.isAdmin,
    isSuperAdmin: permissions.isSuperAdmin,
    isMunicipalityUser: permissions.isMunicipalityUser,
    isEvaluator: permissions.isEvaluator,
    isCoordinator: permissions.isCoordinator,
    roleDisplayName: permissions.roleDisplayName,
    userMunicipality: permissions.userMunicipality
  };
}

export default usePermissions;