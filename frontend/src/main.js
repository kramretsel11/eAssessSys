/**
=========================================================
* Vue Soft UI Dashboard - v3.0.0
=========================================================

* Product Page: https://creative-tim.com/product/vue-soft-ui-dashboard
* Copyright 2022 Creative Tim (https://www.creative-tim.com)

Coded by www.creative-tim.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
*/

import { createApp } from "vue";
import axios from 'axios'
import App from "./App.vue";
import store from "./store";
import router from "./router";
import "./assets/css/nucleo-icons.css";
import "./assets/css/nucleo-svg.css";
import SoftUIDashboard from "./soft-ui-dashboard";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

const baseURL = process.env.NODE_ENV === 'development' ? 
'http://localhost:8080' : 
''

const api = axios.create({ baseURL })

api.interceptors.request.use(
  config => {
      // Get the auth token from localStorage
      const token = localStorage.getItem('token') || localStorage.getItem('userToken');
      if (token) {
          config.headers['Authorization'] = 'Bearer ' + token;
      }
      return config;
  },
  error => {
      Promise.reject(error)
  }
);

const appInstance = createApp(App);
appInstance.config.globalProperties.$api = api;
appInstance.use(store);
appInstance.use(router);
appInstance.use(SoftUIDashboard);
appInstance.mount("#app");
