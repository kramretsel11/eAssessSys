<template>
  <div class="card">
    <div class="card-header">
      <h5>API Connection Test</h5>
    </div>
    <div class="card-body">
      <div class="mb-3">
        <button @click="testConnection" class="btn btn-primary me-2">Test API Connection</button>
        <button @click="testLogin" class="btn btn-success">Test Login</button>
      </div>
      
      <div v-if="results.length > 0" class="mt-3">
        <h6>Test Results:</h6>
        <div v-for="(result, index) in results" :key="index" class="alert" :class="result.success ? 'alert-success' : 'alert-danger'">
          <strong>{{ result.test }}:</strong> {{ result.message }}
          <pre v-if="result.data" class="mt-2 text-sm">{{ JSON.stringify(result.data, null, 2) }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ApiTest',
  data() {
    return {
      results: []
    };
  },
  methods: {
    async testConnection() {
      this.addResult('Connection Test', 'Testing API connection...', false);
      
      try {
        const response = await fetch('http://localhost/Thesis/2025/ascot-realestate/backend/public/index.php/e_assessment/api/v1/test', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });

        if (response.ok) {
          const data = await response.json();
          this.addResult('Connection Test', 'API connection successful!', true, data);
        } else {
          this.addResult('Connection Test', `Connection failed: ${response.status} ${response.statusText}`, false);
        }
      } catch (error) {
        this.addResult('Connection Test', `Connection error: ${error.message}`, false);
      }
    },

    async testLogin() {
      this.addResult('Login Test', 'Testing login endpoint...', false);
      
      try {
        const response = await fetch('http://localhost/Thesis/2025/ascot-realestate/backend/public/index.php/e_assessment/api/v1/auth/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            username: 'test',
            password: 'test'
          })
        });

        const responseText = await response.text();
        console.log('Raw response:', responseText);

        // Try to parse as JSON
        let data;
        try {
          data = JSON.parse(responseText);
        } catch (e) {
          this.addResult('Login Test', `Response is not JSON: ${responseText.substring(0, 200)}...`, false);
          return;
        }

        if (response.ok) {
          this.addResult('Login Test', 'Login endpoint responded successfully!', true, data);
        } else {
          this.addResult('Login Test', `Login failed: ${response.status}`, false, data);
        }
      } catch (error) {
        this.addResult('Login Test', `Login error: ${error.message}`, false);
      }
    },

    addResult(test, message, success, data = null) {
      this.results.unshift({
        test,
        message,
        success,
        data,
        timestamp: new Date().toLocaleTimeString()
      });
    }
  }
};
</script>