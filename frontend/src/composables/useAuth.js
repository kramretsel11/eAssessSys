import { ref, computed, onMounted } from 'vue';
import AuthService from '@/services/auth.js';

/**
 * Composable for authentication and role management
 */
export function useAuth() {
  const user = ref(null);
  const isLoading = ref(false);

  // Reactive computed properties
  const isAuthenticated = computed(() => {
    return AuthService.isAuthenticated();
  });

  const isAdmin = computed(() => {
    const userData = user.value || AuthService.getCurrentUser();
    return userData && userData.role === 'admin';
  });

  const isCoordinator = computed(() => {
    const userData = user.value || AuthService.getCurrentUser();
    return userData && userData.role === 'coordinator';
  });

  const userRole = computed(() => {
    const userData = user.value || AuthService.getCurrentUser();
    return userData ? userData.role : null;
  });

  const userName = computed(() => {
    const userData = user.value || AuthService.getCurrentUser();
    return userData ? userData.fullName : null;
  });

  // Methods
  const refreshUser = async () => {
    if (!AuthService.getToken()) {
      user.value = null;
      return null;
    }

    isLoading.value = true;
    try {
      const userData = await AuthService.fetchCurrentUser();
      user.value = userData;
      return userData;
    } catch (error) {
      console.error('Error refreshing user data:', error);
      user.value = AuthService.getCurrentUser(); // Fallback to localStorage
      return user.value;
    } finally {
      isLoading.value = false;
    }
  };

  const checkAdminStatus = async () => {
    try {
      return await AuthService.isAdminAPI();
    } catch (error) {
      console.error('Error checking admin status:', error);
      return isAdmin.value; // Fallback to local check
    }
  };

  const login = async (username, password) => {
    isLoading.value = true;
    try {
      const result = await AuthService.loginWithRole(username, password);
      if (result.success) {
        user.value = result.user;
      }
      return result;
    } catch (error) {
      console.error('Login error:', error);
      return { success: false, error: 'Login failed' };
    } finally {
      isLoading.value = false;
    }
  };

  const logout = () => {
    AuthService.logout();
    user.value = null;
  };

  // Initialize user data on composable creation
  onMounted(() => {
    if (AuthService.getToken()) {
      user.value = AuthService.getCurrentUser();
      // Optionally refresh from server
      refreshUser().catch(console.error);
    }
  });

  return {
    // State
    user,
    isLoading,
    
    // Computed
    isAuthenticated,
    isAdmin,
    isCoordinator,
    userRole,
    userName,
    
    // Methods
    refreshUser,
    checkAdminStatus,
    login,
    logout
  };
}

// Global auth state (singleton pattern)
let globalAuthState = null;

export function useGlobalAuth() {
  if (!globalAuthState) {
    globalAuthState = useAuth();
  }
  return globalAuthState;
}