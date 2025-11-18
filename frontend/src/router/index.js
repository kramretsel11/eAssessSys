import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "@/views/Dashboard.vue";
import AdminDashboard from "@/views/AdminDashboard.vue";
import EvaluatorDashboard from "@/views/EvaluatorDashboard.vue";
import SignIn from "@/views/SignIn.vue";
import SignUp from "@/views/SignUp.vue";
import Settings from "@/views/Settings.vue";
import Requests from "@/views/Requests.vue";
import Recommend from "@/views/Recommend.vue";
import Approval from "@/views/Approval.vue";
import Track from "@/views/Track.vue";
import Evaluations from "@/views/Evaluations.vue";
import Certificates from "@/views/Certificates.vue";
import Reports from "@/views/Reports.vue";
import AuthService from "@/services/auth.js";

// Authentication helper function (using AuthService)
const isAuthenticated = () => {
  return AuthService.isAuthenticated();
};

// Sync admin check for immediate use (uses local data only)
const isAdminSync = () => {
  const user = AuthService.getCurrentUser();
  return user && user.role === 'admin';
};

// Role checking helper
const hasRoleSync = (role) => {
  const user = AuthService.getCurrentUser();
  return user && user.role === role;
};

// Multiple roles checking helper
const hasAnyRoleSync = (roles) => {
  const user = AuthService.getCurrentUser();
  return user && roles.includes(user.role);
};

const routes = [
  {
    path: "/",
    name: "/",
    redirect: "/sign-in",
  },
  {
    path: "/dashboard",
    name: "Dashboard",
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: "/evaluator-dashboard",
    name: "Evaluator Dashboard",
    component: EvaluatorDashboard,
    meta: { requiresAuth: true, requiresRole: 'evaluator' }
  },
  {
    path: "/admin",
    name: "Admin Dashboard", 
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: "/evaluations",
    name: "Evaluations",
    component: Evaluations,
    meta: { requiresAuth: true, requiresRole: 'evaluator' }
  },
  {
    path: "/certificates",
    name: "Certificates",
    component: Certificates,
    meta: { requiresAuth: true, allowRoles: ['evaluator', 'admin'] }
  },
  {
    path: "/reports",
    name: "Reports",
    component: Reports,
    meta: { requiresAuth: true, allowRoles: ['admin', 'coordinator'] }
  },
  {
    path: "/sign-in",
    name: "Sign In",
    component: SignIn,
    meta: { redirectIfAuth: true }
  },
  {
    path: "/login", // Alternative login route
    redirect: "/sign-in"
  },
  {
    path: "/sign-up",
    name: "Sign Up",
    component: SignUp,
    meta: { redirectIfAuth: true }
  },
  {
    path: "/track",
    name: "Track Request",
    component: Track,
    meta: { requiresAuth: true }
  },
  {
    path: "/settings",
    name: "Settings",
    component: Settings,
    meta: { requiresAuth: true }
  },
  {
    path: "/requests",
    name: "Requests",
    component: Requests,
    meta: { requiresAuth: true }
  },
  {
    path: "/recommend",
    name: "Recommend",
    component: Recommend,
    meta: { requiresAuth: true }
  },
  {
    path: "/approval",
    name: "Approval",
    component: Approval,
    meta: { requiresAuth: true }
  },
  {
    path: "/track-reference",
    name: "Track Reference",
    component: SignUp,
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  linkActiveClass: "active",
});

// Navigation Guards
router.beforeEach(async (to, from, next) => {
  const authenticated = isAuthenticated();
  
  // Check if route requires authentication
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!authenticated) {
      // Redirect to login if not authenticated
      next({
        path: '/sign-in',
        query: { redirect: to.fullPath } // Save the intended destination
      });
    } else if (to.matched.some(record => record.meta.requiresAdmin)) {
      // Check admin access - use sync version for navigation guards
      const adminUser = isAdminSync();
      if (!adminUser) {
        // User is authenticated but not admin, redirect to dashboard
        next({
          path: '/dashboard',
          query: { error: 'Admin access required' }
        });
      } else {
        next(); // User is admin, proceed
      }
    } else if (to.matched.some(record => record.meta.requiresRole)) {
      // Check specific role access
      const requiredRole = to.matched.find(record => record.meta.requiresRole).meta.requiresRole;
      if (!hasRoleSync(requiredRole)) {
        next({
          path: '/dashboard',
          query: { error: 'Insufficient permissions' }
        });
      } else {
        next(); // User has required role, proceed
      }
    } else if (to.matched.some(record => record.meta.allowRoles)) {
      // Check multiple allowed roles
      const allowedRoles = to.matched.find(record => record.meta.allowRoles).meta.allowRoles;
      if (!hasAnyRoleSync(allowedRoles)) {
        next({
          path: '/dashboard', 
          query: { error: 'Insufficient permissions' }
        });
      } else {
        next(); // User has one of the allowed roles, proceed
      }
    } else {
      next(); // User is authenticated, proceed
    }
  }
  // Check if route should redirect when user is already authenticated
  else if (to.matched.some(record => record.meta.redirectIfAuth)) {
    if (authenticated) {
      // User is already logged in, redirect based on role
      const user = AuthService.getCurrentUser();
      let redirectPath = '/dashboard';
      
      if (user.role === 'admin') {
        redirectPath = '/admin';
      } else if (user.role === 'evaluator') {
        redirectPath = '/evaluator-dashboard';
      }
      
      next(to.query.redirect || redirectPath);
    } else {
      next(); // User is not authenticated, proceed to login/signup
    }
  }
  else {
    next(); // Route doesn't require authentication, proceed
  }
});

export default router;
