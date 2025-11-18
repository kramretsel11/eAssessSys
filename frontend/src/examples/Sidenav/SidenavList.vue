<template>
  <div>
    <div
      class="w-auto h-auto collapse navbar-collapse max-height-vh-100 h-100"
      id="sidenav-collapse-main"
    >
      <ul class="navbar-nav">
        <!-- Regular Dashboard (for municipality users and evaluators) -->
        <li class="nav-item" v-if="canAccess('dashboard') || (isAuthenticated && !isAdmin)">
          <sidenav-collapse navText="Dashboard" :to="{ name: 'Dashboard' }">
            <template #icon>
              <shop />
            </template>
          </sidenav-collapse>
        </li>

        <!-- Requests (for municipality users and coordinators) -->
        <li class="nav-item" v-if="canAccess('requests') || (isAuthenticated && !isAdmin)">
          <sidenav-collapse navText="Requests" :to="{ name: 'Requests' }">
            <template #icon>
              <i class="fas fa-file-alt text-primary text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>

        <!-- Evaluations (for evaluators) -->
        <li class="nav-item" v-if="canAccess('evaluations')">
          <sidenav-collapse navText="Evaluations" :to="{ name: 'Evaluations' }">
            <template #icon>
              <i class="fas fa-clipboard-check text-success text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>

        <!-- Certificates (for evaluators) -->
        <li class="nav-item" v-if="canAccess('certificates')">
          <sidenav-collapse navText="Certificates" :to="{ name: 'Certificates' }">
            <template #icon>
              <i class="fas fa-certificate text-warning text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>

        <!-- Reports (for coordinators and admins) -->
        <li class="nav-item" v-if="canAccess('reports')">
          <sidenav-collapse navText="Reports" :to="{ name: 'Reports' }">
            <template #icon>
              <i class="fas fa-chart-bar text-info text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>
        
        <!-- Admin Dashboard (only for super admin) -->
        <li class="nav-item" v-if="canAccess('admin-dashboard')">
          <sidenav-collapse navText="Admin Dashboard" :to="{ name: 'Admin Dashboard' }">
            <template #icon>
              <i class="fas fa-user-shield text-primary text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>

        <!-- Evaluator Dashboard (only for evaluators) -->
        <li class="nav-item" v-if="canAccess('evaluator-dashboard')">
          <sidenav-collapse navText="Evaluator Dashboard" :to="{ name: 'Evaluator Dashboard' }">
            <template #icon>
              <i class="fas fa-user-check text-info text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>

        <!-- Evaluations (only for evaluators) -->
        <li class="nav-item" v-if="canAccess('evaluations')">
          <sidenav-collapse navText="Evaluations" :to="{ name: 'Evaluations' }">
            <template #icon>
              <i class="fas fa-clipboard-check text-success text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>

        <!-- Certificates (for evaluators and admins) -->
        <li class="nav-item" v-if="canAccess('certificates')">
          <sidenav-collapse navText="Certificates" :to="{ name: 'Certificates' }">
            <template #icon>
              <i class="fas fa-certificate text-warning text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>

        <!-- Reports (for admins and coordinators) -->
        <li class="nav-item" v-if="canAccess('reports')">
          <sidenav-collapse navText="Reports" :to="{ name: 'Reports' }">
            <template #icon>
              <i class="fas fa-chart-bar text-info text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>

        <!-- User Management (only for super admin) -->
        <li class="nav-item" v-if="canAccess('users')">
          <sidenav-collapse navText="User Management" :to="{ name: 'Users' }">
            <template #icon>
              <i class="fas fa-users text-primary text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>

        <!-- Audit Logs (only for super admin) -->
        <li class="nav-item" v-if="canAccess('audit-logs')">
          <sidenav-collapse navText="Audit Logs" :to="{ name: 'AuditLogs' }">
            <template #icon>
              <i class="fas fa-history text-secondary text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>

        <!-- Configuration (only for super admin) -->
        <li class="nav-item" v-if="canAccess('configuration')">
          <sidenav-collapse navText="Configuration" :to="{ name: 'Configuration' }">
            <template #icon>
              <i class="fas fa-cogs text-dark text-sm opacity-10"></i>
            </template>
          </sidenav-collapse>
        </li>
        
        <!-- Settings (visible for all authenticated users) -->
        <li class="nav-item" v-if="canAccess('settings') || isAuthenticated">
          <sidenav-collapse navText="Settings" :to="{ name: 'Settings' }">
            <template #icon>
              <settings />
            </template>
          </sidenav-collapse>
        </li>
        
        <!-- Logout button (visible for all users with enhanced styling) -->
        <li class="nav-item mt-3">
          <div class="nav-link text-dark cursor-pointer bg-light rounded" @click="logout" style="padding: 12px 16px;">
            <div class="d-flex align-items-center">
              <div class="text-center icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger d-flex align-items-center justify-content-center me-2">
                <i class="fas fa-sign-out-alt text-white text-sm"></i>
              </div>
              <span class="nav-link-text ms-1 font-weight-bold text-danger">Logout</span>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div class="pt-3 mx-3 mt-3 sidenav-footer">
      <sidenav-card
        :class="cardBg"
        textPrimary="Need Help?"
        textSecondary="Please check our manual"
        route="https://www.creative-tim.com/learning-lab/vue/overview/soft-ui-dashboard/"
        label="E-ASSESSMENT"
        icon="ni ni-diamond"
      />
    </div>
  </div>
</template>
<script>
import SidenavCollapse from "./SidenavCollapse.vue";
import SidenavCard from "./SidenavCard.vue";
import Shop from "../../components/Icon/Shop.vue";
import Settings from "../../components/Icon/Settings.vue";
import { computed, onMounted } from 'vue';
import AuthService from '../../services/auth.js';
import PermissionService from '../../services/permissions.js';
import Swal from 'sweetalert2';

export default {
  name: "SidenavList",
  props: {
    cardBg: String,
  },
  data() {
    return {
      title: "Soft UI Dashboard PRO",
      controls: "dashboardsExamples",
      isActive: "active",
    };
  },
  components: {
    SidenavCollapse,
    SidenavCard,
    Shop,
    Settings
  },
  setup() {
    // Get user role and permissions with reactive updates
    const isAdmin = computed(() => {
      return PermissionService.isAdmin();
    });

    const isAuthenticated = computed(() => {
      return AuthService.isAuthenticated();
    });

    const canAccess = (page) => {
      const result = PermissionService.canAccess(page);
      const user = AuthService.getUser();
      const role = PermissionService.getCurrentUserRole();
      
      // Debug logging
      console.log(`=== MENU ACCESS CHECK ===`);
      console.log(`Page: ${page}`);
      console.log(`User:`, user);
      console.log(`Role:`, role);
      console.log(`Can Access:`, result);
      console.log(`=========================`);
      
      return result;
    };

    const canPerform = (action) => {
      return PermissionService.canPerform(action);
    };

    const getUserRole = computed(() => {
      return PermissionService.getCurrentUserRole();
    });

    const getRoleDisplayName = computed(() => {
      const role = PermissionService.getCurrentUserRole();
      return PermissionService.getRoleDisplayName(role);
    });
    
    // Refresh user data on component mount to ensure latest role info
    onMounted(async () => {
      try {
        // Fetch latest user data from backend if token exists
        if (AuthService.getToken()) {
          await AuthService.fetchCurrentUser();
        }
      } catch (error) {
        console.error('Error refreshing user data:', error);
      }
    });
    
    return {
      isAdmin,
      isAuthenticated,
      canAccess,
      canPerform,
      getUserRole,
      getRoleDisplayName
    };
  },
  methods: {
    getRoute() {
      const routeArr = this.$route.path.split("/");
      return routeArr[1];
    },
    async logout() {
      const result = await Swal.fire({
        title: 'Confirm Logout',
        text: 'Are you sure you want to logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Logout',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33'
      });
      
      if (result.isConfirmed) {
        AuthService.logout();
        this.$router.push('/sign-in');
      }
    }
  },
};
</script>
