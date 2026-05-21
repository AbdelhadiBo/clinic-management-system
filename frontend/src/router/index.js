import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '@/views/LoginView.vue';
import AccueilView from '@/views/AccueilView.vue';

// Admin views (your existing ones)
import DashboardView from '@/views/admin/DashboardView.vue';
import PatientsView from '@/views/admin/PatientsView.vue';
import MedecinsView from '@/views/admin/MedecinsView.vue';
import SecretairesView from '@/views/admin/SecretairesView.vue';
import InfirmiersView from '@/views/admin/InfirmiersView.vue';
import RendezVousView from '@/views/admin/RendezVousView.vue';
import ConsultationsView from '@/views/admin/ConsultationsView.vue';
import FacturesView from '@/views/admin/FacturesView.vue';
import MedicalRecordsView from '@/views/admin/MedicalRecordsView.vue';
import SettingsView from '@/views/admin/SettingsView.vue';

// Secretaire views
import SecretaireDashboard from '@/views/secretaire/DashboardView.vue';
import SecretairePatients from '@/views/secretaire/PatientsView.vue';
import SecretaireAppointments from '@/views/secretaire/AppointmentsView.vue';
import SecretaireInvoices from '@/views/secretaire/InvoicesView.vue';
import SecretaireDoctors from '@/views/secretaire/DoctorsView.vue';
// router/index.js
const routes = [
  // Public pages
  { path: '/', name: 'Accueil', component: AccueilView, meta: { public: true } },
  { path: '/login', name: 'Login', component: LoginView, meta: { public: true } },

  // Admin routes
  { path: '/dashboard', name: 'Dashboard', component: DashboardView, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/patients', name: 'Patients', component: PatientsView, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/rendez-vous', name: 'RendezVous', component: RendezVousView, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/consultations', name: 'Consultations', component: ConsultationsView, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/medecins', name: 'Medecins', component: MedecinsView, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/factures', name: 'Factures', component: FacturesView, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/dossiers', name: 'MedicalRecords', component: MedicalRecordsView, meta: { requiresAuth: true, role: 'admin' } },
  { path: '/settings', name: 'Settings', component: SettingsView, meta: { requiresAuth: true, role: 'admin' } },

  // Secretaire routes (NO layout - App.vue handles it)
  { path: '/secretaire/dashboard', name: 'SecretaireDashboard', component: SecretaireDashboard, meta: { requiresAuth: true, role: 'secretaire' } },
  { path: '/secretaire/patients', name: 'SecretairePatients', component: SecretairePatients, meta: { requiresAuth: true, role: 'secretaire' } },
  { path: '/secretaire/appointments', name: 'SecretaireAppointments', component: SecretaireAppointments, meta: { requiresAuth: true, role: 'secretaire' } },
  { path: '/secretaire/invoices', name: 'SecretaireInvoices', component: SecretaireInvoices, meta: { requiresAuth: true, role: 'secretaire' } },
  { path: '/secretaire/consultations', name: 'SecretaireConsultations', component: () => import('@/views/secretaire/ConsultationsView.vue'), meta: { requiresAuth: true, role: 'secretaire' } },
  { path: '/secretaire/doctors', name: 'SecretaireDoctors', component: SecretaireDoctors, meta: { requiresAuth: true, role: 'secretaire' } }

];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const isPublic = to.meta.public;

  if (isPublic) {
    next();
    return;
  }

  if (to.meta.requiresAuth) {
    const requiredRole = to.meta.role;
    const token = localStorage.getItem(`${requiredRole}_token`);

    if (!token) {
      next('/login');
      return;
    }

    const userRole = localStorage.getItem('user_role');
    if (userRole !== requiredRole) {
      if (userRole === 'admin') {
        next('/dashboard');
      } else if (userRole === 'secretaire') {
        next('/secretaire/dashboard');
      } else {
        next('/login');
      }
      return;
    }
  }

  next();
});

export default router;