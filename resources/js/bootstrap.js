import axios from 'axios';

// Set axios defaults for Laravel Sanctum
axios.defaults.withCredentials = true; // crucial for cookies
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';

// Set base URL to current domain
axios.defaults.baseURL = window.location.origin;

// Optional: get CSRF token from meta (for Inertia forms)
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

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
        console.error('CSRF token mismatch. Refreshing page...');
        // Optionally refresh the page or redirect to login
        window.location.reload();
    }
    return Promise.reject(error);
});

export default axios;