import axios from 'axios';

const baseURL = import.meta.env.VITE_API_BASE_URL;

const axiosInstance = axios.create({
    baseURL,
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('accessToken') || ''}`,
    },
    timeout: 10000,
    retry: 3,
});


export default axiosInstance;