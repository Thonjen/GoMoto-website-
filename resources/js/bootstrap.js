import axios from 'axios';

axios.defaults.withCredentials = true; // crucial for cookies
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Optional: get CSRF token from meta (for Inertia forms)
const token = document.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

export default axios;