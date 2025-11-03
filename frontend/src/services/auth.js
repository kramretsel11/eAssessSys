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
  }
};

export default AuthService;