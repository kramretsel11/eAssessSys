import { createRouter, createWebHistory } from "vue-router";
import Dashboard from "@/views/Dashboard.vue";
import SignIn from "@/views/SignIn.vue";
import SignUp from "@/views/SignUp.vue";
import Settings from "@/views/Settings.vue";
import Requests from "@/views/Requests.vue";
import Recommend from "@/views/Recommend.vue";
import Approval from "@/views/Approval.vue";
import Track from "@/views/Track.vue";
import AuthService from "@/services/auth.js";

// Authentication helper function (using AuthService)
const isAuthenticated = () => {
  return AuthService.isAuthenticated();
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
router.beforeEach((to, from, next) => {
  const authenticated = isAuthenticated();
  
  // Check if route requires authentication
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!authenticated) {
      // Redirect to login if not authenticated
      next({
        path: '/sign-in',
        query: { redirect: to.fullPath } // Save the intended destination
      });
    } else {
      next(); // User is authenticated, proceed
    }
  }
  // Check if route should redirect when user is already authenticated
  else if (to.matched.some(record => record.meta.redirectIfAuth)) {
    if (authenticated) {
      // User is already logged in, redirect to dashboard
      const redirectPath = to.query.redirect || '/dashboard';
      next(redirectPath);
    } else {
      next(); // User is not authenticated, proceed to login/signup
    }
  }
  else {
    next(); // Route doesn't require authentication, proceed
  }
});

export default router;
