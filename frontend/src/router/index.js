import { createRouter, createWebHistory } from 'vue-router';
import DashboardView from '@/views/admin/DashboardView.vue';      // ← DECOMMENTE !
import PatientsView from '@/views/admin/PatientsView.vue';        // ← DECOMMENTE !
import MedecinsView from '@/views/admin/MedecinsView.vue';
import SecretairesView from '@/views/admin/SecretairesView.vue';
import InfirmiersView from '@/views/admin/InfirmiersView.vue';

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/dashboard', name: 'Dashboard', component: DashboardView },
  { path: '/patients', name: 'Patients', component: PatientsView },
  { path: '/medecins', name: 'Medecins', component: MedecinsView },
  { path: '/secretaires', name: 'Secretaires', component: SecretairesView },
  { path: '/infirmiers', name: 'Infirmiers', component: InfirmiersView },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;