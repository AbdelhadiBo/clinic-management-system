import axios from "axios";

const API_URL = 'http://localhost:8000/api';

const api = axios.create({
    baseURL: API_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    withCredentials: true
});

// Intercepteur pour ajouter le token à chaque requête
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// ==================== AUTH ====================
export const login = (credentials) => api.post('/admin/login', credentials);
export const logout = () => api.post('/admin/logout');
export const getUser = () => api.get('/admin/user');

// ==================== PATIENTS ====================
export const getPatients = () => api.get('/admin/patients');
export const addPatient = (data) => api.post('/admin/patients', data);
export const updatePatient = (id, data) => api.put(`/admin/patients/${id}`, data);
export const deletePatient = (id) => api.delete(`/admin/patients/${id}`);

// ==================== MEDECINS ====================
export const getMedecins = () => api.get('/admin/medecins');
export const addMedecin = (data) => api.post('/admin/medecins', data);
export const updateMedecin = (id, data) => api.put(`/admin/medecins/${id}`, data);
export const deleteMedecin = (id) => api.delete(`/admin/medecins/${id}`);

// ==================== SECRETAIRES ====================
export const getSecretaires = () => api.get('/admin/secretaires');
export const addSecretaire = (data) => api.post('/admin/secretaires', data);
export const updateSecretaire = (id, data) => api.put(`/admin/secretaires/${id}`, data);
export const deleteSecretaire = (id) => api.delete(`/admin/secretaires/${id}`);

// ==================== INFIRMIERS ====================
export const getInfirmiers = () => api.get('/admin/infirmiers');
export const addInfirmier = (data) => api.post('/admin/infirmiers', data);
export const updateInfirmier = (id, data) => api.put(`/admin/infirmiers/${id}`, data);
export const deleteInfirmier = (id) => api.delete(`/admin/infirmiers/${id}`);

// ==================== RENDEZ-VOUS ====================
export const getRendezVous = () => api.get('/admin/rendez-vous');
export const addRendezVous = (data) => api.post('/admin/rendez-vous', data);
export const updateRendezVous = (id, data) => api.put(`/admin/rendez-vous/${id}`, data);
export const deleteRendezVous = (id) => api.delete(`/admin/rendez-vous/${id}`);
export const updateRendezVousStatus = (id, status) => api.put(`/admin/rendez-vous/${id}/status`, { statut: status });

// ==================== CONSULTATIONS ====================
export const getConsultations = () => api.get('/admin/consultations');
export const addConsultation = (data) => api.post('/admin/consultations', data);

// ==================== FACTURES ====================
export const getFactures = () => api.get('/admin/factures');
export const addFacture = (data) => api.post('/admin/factures', data);
export const updateFacture = (id, data) => api.put(`/admin/factures/${id}`, data);
export const deleteFacture = (id) => api.delete(`/admin/factures/${id}`);

// ==================== DASHBOARD ====================
export const getDashboardStats = () => api.get('/admin/dashboard');

export default api;