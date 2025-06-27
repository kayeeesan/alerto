import axios from 'axios';


axios.interceptors.request.use(config => {
    const tenantPath = localStorage.getItem('tenant_db_path');
    if (tenantPath) {
        config.headers['X-Tenant-DB'] = tenantPath;
    }


    const token = localStorage.getItem('token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }


    return config;
});
