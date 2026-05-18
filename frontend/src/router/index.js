import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '@/views/LoginView.vue';
import AccueilView from '@/views/AccueilView.vue';
import DashboardView from '@/views/admin/DashboardView.vue';
import PatientsView from '@/views/admin/PatientsView.vue';
import MedecinsView from '@/views/admin/MedecinsView.vue';
import SecretairesView from '@/views/admin/SecretairesView.vue';
import InfirmiersView from '@/views/admin/InfirmiersView.vue';
import RendezVousView from '@/views/admin/RendezVousView.vue';
import ConsultationsView from '@/views/admin/ConsultationsView.vue';
import FacturesView from '@/views/admin/FacturesView.vue';
import MedicalRecordsView from '@/views/admin/MedicalRecordsView.vue';

const routes = [
  // Page d'accueil publique (première page)
  { path: '/', name: 'Accueil', component: AccueilView, meta: { public: true } },
  
  // Login publique
  { path: '/login', name: 'Login', component: LoginView, meta: { public: true } },
  
  // Pages protégées (nécessitent login)
  { path: '/dashboard', name: 'Dashboard', component: DashboardView },
  { path: '/patients', name: 'Patients', component: PatientsView },
  { path: '/medecins', name: 'Medecins', component: MedecinsView },
  { path: '/secretaires', name: 'Secretaires', component: SecretairesView },
  { path: '/infirmiers', name: 'Infirmiers', component: InfirmiersView },
  { path: '/rendez-vous', name: 'RendezVous', component: RendezVousView },
  { path: '/consultations', name: 'Consultations', component: ConsultationsView },
  { path: '/factures', name: 'Factures', component: FacturesView },
  { path: '/dossiers', name: 'MedicalRecords', component: MedicalRecordsView },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Guard - Protection des routes
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const isPublic = to.meta.public;
  
  // Si pas de token et page non publique → rediriger vers login
  if (!isPublic && !token) {
    next('/login');
  } 
  // Si token présent et page login → rediriger vers dashboard
  else if (to.path === '/login' && token) {
    next('/dashboard');
  } 
  else {
    next();
  }
});

export default router;