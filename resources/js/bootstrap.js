import axios from 'axios';

// Set axios defaults for Laravel Sanctum
axios.defaults.withCredentials = true; // crucial for cookies
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

// Set base URL to current domain
axios.defaults.baseURL = window.location.origin;

// Don't set X-CSRF-TOKEN from meta tag - let Sanctum handle CSRF cookies
// This prevents conflicts between traditional CSRF and Sanctum SPA CSRF

// Add a request interceptor to handle CSRF tokens
axios.interceptors.request.use(function (config) {
    return config;
}, function (error) {
    return Promise.reject(error);
});

// Add a response interceptor to handle CSRF errors
axios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (error.response?.status === 419) {
        console.warn('CSRF token mismatch detected. Auth store will handle retry...');
        // Don't automatically reload - let auth store handle retries
        // Only reload as absolute last resort if auth store fails
    }
    return Promise.reject(error);
});

export default axios;