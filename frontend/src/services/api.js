import axios from "axios";

const API_URL = '/api';

const api = axios.create({
    baseURL: API_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Intercepteur pour ajouter le token à chaque requête
api.interceptors.request.use((config) => {
    // Check for admin or secretaire token
    const token = localStorage.getItem('admin_token') || localStorage.getItem('secretaire_token');
    
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    
    return config;
});

// Intercepteur pour gérer les erreurs globalement
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            console.error('🚫 401 Unauthorized - Token invalide ou expiré');
            // Clear tokens and redirect to login
            localStorage.removeItem('admin_token');
            localStorage.removeItem('secretaire_token');
            localStorage.removeItem('admin_user');
            localStorage.removeItem('secretaire_user');
            localStorage.removeItem('user_role');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

// ==================== AUTH (Dynamic based on role) ====================
export const loginAdmin = (credentials) => api.post('/admin/login', credentials);
export const loginSecretaire = (credentials) => api.post('/secretaire/login', credentials);

export const logout = () => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? '/admin/logout' : '/secretaire/logout';
    return api.post(endpoint);
};

export const getUser = () => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? '/admin/user' : '/secretaire/profile';
    return api.get(endpoint);
};

// ==================== PROFILE & PASSWORD (Admin only) ====================
export const updateProfile = (data) => api.put('/admin/profile', data);
export const changePassword = (data) => api.put('/admin/password', data);

// ==================== PATIENTS ====================
export const getPatients = () => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? '/admin/patients' : '/secretaire/patients';
    return api.get(endpoint);
};
export const addPatient = (data) => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? '/admin/patients' : '/secretaire/patients';
    return api.post(endpoint, data);
};
export const updatePatient = (id, data) => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? `/admin/patients/${id}` : `/secretaire/patients/${id}`;
    return api.put(endpoint, data);
};
export const deletePatient = (id) => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? `/admin/patients/${id}` : `/secretaire/patients/${id}`;
    return api.delete(endpoint);
};

// ==================== MEDECINS (Admin only) ====================
export const getMedecins = () => api.get('/admin/medecins');
export const addMedecin = (data) => api.post('/admin/medecins', data);
export const updateMedecin = (id, data) => api.put(`/admin/medecins/${id}`, data);
export const deleteMedecin = (id) => api.delete(`/admin/medecins/${id}`);

// ==================== SECRETAIRES (Admin only) ====================
export const getSecretaires = () => api.get('/admin/secretaires');
export const addSecretaire = (data) => api.post('/admin/secretaires', data);
export const updateSecretaire = (id, data) => api.put(`/admin/secretaires/${id}`, data);
export const deleteSecretaire = (id) => api.delete(`/admin/secretaires/${id}`);

// ==================== INFIRMIERS (Admin only) ====================
export const getInfirmiers = () => api.get('/admin/infirmiers');
export const addInfirmier = (data) => api.post('/admin/infirmiers', data);
export const updateInfirmier = (id, data) => api.put(`/admin/infirmiers/${id}`, data);
export const deleteInfirmier = (id) => api.delete(`/admin/infirmiers/${id}`);

// ==================== RENDEZ-VOUS ====================
export const getRendezVous = () => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? '/admin/rendez-vous' : '/secretaire/rendez-vous';
    return api.get(endpoint);
};
export const addRendezVous = (data) => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? '/admin/rendez-vous' : '/secretaire/rendez-vous';
    return api.post(endpoint, data);
};
export const updateRendezVous = (id, data) => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? `/admin/rendez-vous/${id}` : `/secretaire/rendez-vous/${id}`;
    return api.put(endpoint, data);
};
export const deleteRendezVous = (id) => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? `/admin/rendez-vous/${id}` : `/secretaire/rendez-vous/${id}`;
    return api.delete(endpoint);
};
export const updateRendezVousStatus = (id, status) => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? `/admin/rendez-vous/${id}/status` : `/secretaire/rendez-vous/${id}/status`;
    return api.patch(endpoint, { statut: status });
};

// ==================== CONSULTATIONS (Admin only) ====================
export const getConsultations = () => api.get('/admin/consultations');
export const addConsultation = (data) => api.post('/admin/consultations', data);

// ==================== FACTURES ====================
export const getFactures = () => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? '/admin/factures' : '/secretaire/factures';
    return api.get(endpoint);
};
export const addFacture = (data) => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? '/admin/factures' : '/secretaire/factures';
    return api.post(endpoint, data);
};
export const updateFacture = (id, data) => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? `/admin/factures/${id}` : `/secretaire/factures/${id}`;
    return api.put(endpoint, data);
};
export const deleteFacture = (id) => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? `/admin/factures/${id}` : `/secretaire/factures/${id}`;
    return api.delete(endpoint);
};

// ==================== DASHBOARD ====================
export const getDashboardStats = () => {
    const role = localStorage.getItem('user_role');
    const endpoint = role === 'admin' ? '/admin/dashboard' : '/secretaire/dashboard';
    return api.get(endpoint);
};

export default api;