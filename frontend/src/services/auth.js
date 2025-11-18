// Authentication Service
export const AuthService = {
  // Token storage keys (you can adjust these based on your implementation)
  TOKEN_KEYS: ['userToken', 'token', 'authToken'],
  EXPIRY_KEY: 'tokenExpiry',
  USER_KEY: 'user',

  /**
   * Check if user is authenticated
   * @returns {boolean}
   */
  isAuthenticated() {
    const token = this.getToken();
    
    if (!token) return false;
    
    // Check token expiration if stored
    const tokenExpiry = localStorage.getItem(this.EXPIRY_KEY);
    if (tokenExpiry && new Date().getTime() > parseInt(tokenExpiry)) {
      this.clearAuth();
      return false;
    }
    
    return true;
  },

  /**
   * Get the authentication token
   * @returns {string|null}
   */
  getToken() {
    for (const key of this.TOKEN_KEYS) {
      const token = localStorage.getItem(key);
      if (token) return token;
    }
    return null;
  },

  /**
   * Set authentication token
   * @param {string} token 
   * @param {number} expiresIn - Expiration time in milliseconds (optional)
   */
  setToken(token, expiresIn = null) {
    localStorage.setItem('userToken', token);
    
    if (expiresIn) {
      const expiryTime = new Date().getTime() + expiresIn;
      localStorage.setItem(this.EXPIRY_KEY, expiryTime.toString());
    }
  },

  /**
   * Set user data
   * @param {object} user 
   */
  setUser(user) {
    localStorage.setItem(this.USER_KEY, JSON.stringify(user));
  },

  /**
   * Get user data
   * @returns {object|null}
   */
  getUser() {
    const user = localStorage.getItem(this.USER_KEY);
    return user ? JSON.parse(user) : null;
  },

  /**
   * Clear all authentication data
   */
  clearAuth() {
    this.TOKEN_KEYS.forEach(key => localStorage.removeItem(key));
    localStorage.removeItem(this.EXPIRY_KEY);
    localStorage.removeItem(this.USER_KEY);
  },

  /**
   * Login user with token and user data
   * @param {string} token 
   * @param {object} user 
   * @param {number} expiresIn 
   */
  login(token, user = null, expiresIn = null) {
    this.setToken(token, expiresIn);
    if (user) {
      this.setUser(user);
    }
  },

  /**
   * Logout user
   */
  logout() {
    this.clearAuth();
  },

  /**
   * Get authorization header for API requests
   * @returns {object}
   */
  getAuthHeader() {
    const token = this.getToken();
    return token ? { 'Authorization': `Bearer ${token}` } : {};
  },

  /**
   * Get current user (alias for getUser for compatibility)
   * @returns {object|null}
   */
  getCurrentUser() {
    return this.getUser();
  },

  /**
   * Fetch current user from backend API
   * @returns {Promise<object|null>}
   */
  async fetchCurrentUser() {
    try {
      const response = await fetch('http://localhost:8080/e_assessment/api/v1/auth/user', {
        method: 'GET',
        headers: {
          ...this.getAuthHeader(),
          'Content-Type': 'application/json'
        }
      });

      if (response.ok) {
        const userData = await response.json();
        this.setUser(userData);
        return userData;
      } else {
        // If API call fails, fall back to localStorage data
        return this.getUser();
      }
    } catch (error) {
      console.error('Error fetching current user:', error);
      // Fall back to localStorage data if API fails
      return this.getUser();
    }
  },

  /**
   * Check if current user is admin (using backend API)
   * @returns {Promise<boolean>}
   */
  async isAdminAPI() {
    try {
      const response = await fetch('http://localhost:8080/e_assessment/api/v1/auth/isAdmin', {
        method: 'GET',
        headers: {
          ...this.getAuthHeader(),
          'Content-Type': 'application/json'
        }
      });

      if (response.ok) {
        const result = await response.json();
        return result.isAdmin || false;
      }
      return false;
    } catch (error) {
      console.error('Error checking admin status:', error);
      return false;
    }
  },

  /**
   * Check if current user is admin (local check)
   * @returns {boolean}
   */
  isAdmin() {
    const user = this.getUser();
    return user && user.role === 'admin';
  },

  /**
   * Enhanced login method that stores user role information
   * @param {string} username 
   * @param {string} password 
   * @returns {Promise<object>}
   */
  async loginWithRole(username, password) {
    try {
      const response = await fetch('http://localhost:8080/e_assessment/api/v1/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ username, password })
      });

      // First check if response is ok
      if (response.ok) {
        // Check if response is actually JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
          throw new Error('Server returned non-JSON response');
        }
        
        const loginResult = await response.json();
        
        // Store token
        this.setToken(loginResult.jwt);
        
        // Create user object with role information
        const userData = {
          id: loginResult.userId,
          fullName: loginResult.fullName,
          userType: loginResult.userType,
          role: loginResult.role,
          username: username
        };
        
        // Store user data
        this.setUser(userData);
        
        return {
          success: true,
          user: userData,
          token: loginResult.jwt
        };
      } else {
        // Handle different HTTP status codes
        let errorMessage = 'Login failed';
        try {
          const errorData = await response.json();
          errorMessage = errorData.message || errorData.title || 'Login failed';
        } catch (e) {
          // If response is not JSON, use status text
          errorMessage = response.statusText || `Server error (${response.status})`;
        }
        
        return {
          success: false,
          error: errorMessage
        };
      }
    } catch (error) {
      console.error('Login error:', error);
      return {
        success: false,
        error: 'Network error occurred'
      };
    }
  }
};

export default AuthService;