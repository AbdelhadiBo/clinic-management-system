<template>
  <div class="dashboard">
    <!-- Header avec bouton logout -->
    <div class="dashboard-header">
      <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="welcome-text">Welcome back, Dr. {{ userName }}</p>
      </div>
      <button @click="handleLogout" class="btn-logout-temp">
        <i class="fas fa-sign-out-alt"></i>
        Logout
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Total Patients</span>
          <div class="stat-icon blue">
            <i class="fas fa-user-injured"></i>
          </div>
        </div>
        <p class="stat-value">{{ stats.total_patients }}</p>
        <p class="stat-change positive">↑ 12% from last month</p>
      </div>

      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Today's Appointments</span>
          <div class="stat-icon green">
            <i class="fas fa-calendar-check"></i>
          </div>
        </div>
        <p class="stat-value">{{ stats.rdv_aujourdhui }}</p>
        <p class="stat-change positive">↑ 8% from last month</p>
      </div>

      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Monthly Revenue</span>
          <div class="stat-icon teal">
            <i class="fas fa-dollar-sign"></i>
          </div>
        </div>
        <p class="stat-value">{{ montantTotal }} DA</p>
        <p class="stat-change positive">↑ 15% from last month</p>
      </div>

      <div class="stat-card">
        <div class="stat-header">
          <span class="stat-label">Active Doctors</span>
          <div class="stat-icon purple">
            <i class="fas fa-user-md"></i>
          </div>
        </div>
        <p class="stat-value">{{ stats.total_medecins }}</p>
        <p class="stat-change positive">↑ 5% from last month</p>
      </div>
    </div>

    <!-- Graphiques -->
    <div class="charts-grid">
      <div class="chart-card">
        <h3 class="chart-title">Appointments This Week</h3>
        <div class="chart-placeholder bar-chart">
          <div class="bar" style="height: 60%"></div>
          <div class="bar" style="height: 85%"></div>
          <div class="bar" style="height: 70%"></div>
          <div class="bar" style="height: 100%"></div>
          <div class="bar" style="height: 80%"></div>
          <div class="bar" style="height: 50%"></div>
          <div class="bar" style="height: 35%"></div>
        </div>
        <div class="chart-labels">
          <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
        </div>
      </div>

      <div class="chart-card">
        <h3 class="chart-title">Revenue Trend</h3>
        <div class="chart-placeholder line-chart">
          <svg viewBox="0 0 300 150" class="line-svg">
            <polyline 
              points="0,100 100,90 200,80 300,60" 
              fill="none" 
              stroke="#10b981" 
              stroke-width="3"
            />
            <circle cx="0" cy="100" r="4" fill="#10b981"/>
            <circle cx="100" cy="90" r="4" fill="#10b981"/>
            <circle cx="200" cy="80" r="4" fill="#10b981"/>
            <circle cx="300" cy="60" r="4" fill="#10b981"/>
          </svg>
        </div>
        <div class="chart-labels">
          <span>Jan</span><span>Feb</span><span>Mar</span>
        </div>
      </div>
    </div>

    <!-- Today's Appointments -->
    <div class="appointments-card">
      <div class="appointments-header">
        <h3 class="chart-title">Today's Appointments</h3>
        <button class="btn-view-all">View All</button>
      </div>
      
      <div v-if="loading" class="loading">Chargement...</div>
      
      <div v-else class="appointments-list">
        <div v-for="rdv in todayAppointments" :key="rdv.id_rdv" class="appointment-item">
          <div class="appointment-avatar">
            <i class="fas fa-clock"></i>
          </div>
          <div class="appointment-info">
            <p class="appointment-patient">{{ rdv.patient?.nom }} {{ rdv.patient?.prenom }}</p>
            <p class="appointment-details">{{ rdv.heure }} • Dr. {{ rdv.medecin?.nom }}</p>
            <p class="appointment-type">{{ rdv.motif || 'General Checkup' }}</p>
          </div>
          <span :class="['status-badge', rdv.statut === 'confirmé' ? 'confirmed' : 'pending']">
            {{ rdv.statut }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { getDashboardStats, getRendezVous, logout } from '@/services/api.js';

const router = useRouter();

const stats = ref({
  total_patients: 0,
  total_medecins: 0,
  total_secretaires: 0,
  total_infirmiers: 0,
  rdv_aujourdhui: 0,
  rdv_en_attente: 0,
  total_factures: 0,
  montant_total_factures: 0
});

const rendezVous = ref([]);
const loading = ref(false);

const userName = computed(() => {
  const user = localStorage.getItem('user');
  return user ? JSON.parse(user).prenom : 'Admin';
});

const montantTotal = computed(() => {
  return stats.value.montant_total_factures?.toLocaleString() || '0';
});

const todayAppointments = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return rendezVous.value.filter(rdv => rdv.date_rdv === today);
});

onMounted(() => {
  loadStats();
  loadRendezVous();
});

const loadStats = async () => {
  try {
    const response = await getDashboardStats();
    stats.value = response.data.data;
  } catch (error) {
    console.error('Erreur stats:', error);
  }
};

const loadRendezVous = async () => {
  loading.value = true;
  try {
    const response = await getRendezVous();
    rendezVous.value = response.data.data;
  } catch (error) {
    console.error('Erreur RDV:', error);
  } finally {
    loading.value = false;
  }
};

// Logout temporaire
const handleLogout = async () => {
  if (!confirm('Se déconnecter ?')) return;
  
  try {
    await logout();
  } catch (e) {
    console.log('Erreur logout API');
  }
  
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  router.push('/login');
};
</script>

<style scoped>
.dashboard {
  max-width: 1400px;
}

/* Header */
.dashboard-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.welcome-text {
  font-size: 14px;
  color: #64748b;
}

/* Bouton Logout Temporaire */
.btn-logout-temp {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #fee2e2;
  color: #dc2626;
  border: 1px solid #fecaca;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-logout-temp:hover {
  background: #fecaca;
  transform: translateY(-1px);
}

.btn-logout-temp i {
  font-size: 14px;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.stat-label {
  font-size: 13px;
  font-weight: 500;
  color: #64748b;
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon.blue { background: #eff6ff; color: #2563eb; }
.stat-icon.green { background: #ecfdf5; color: #10b981; }
.stat-icon.teal { background: #f0fdfa; color: #14b8a6; }
.stat-icon.purple { background: #f5f3ff; color: #8b5cf6; }

.stat-icon i {
  font-size: 18px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 8px;
}

.stat-change {
  font-size: 12px;
  font-weight: 500;
}

.stat-change.positive {
  color: #10b981;
}

/* Charts Grid */
.charts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 24px;
}

.chart-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.chart-title {
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 20px;
}

/* Bar Chart */
.bar-chart {
  display: flex;
  align-items: flex-end;
  justify-content: space-around;
  height: 200px;
  padding: 0 10px;
  gap: 12px;
}

.bar {
  flex: 1;
  background: #3b82f6;
  border-radius: 6px 6px 0 0;
  min-width: 30px;
  transition: opacity 0.2s;
}

.bar:hover {
  opacity: 0.8;
}

/* Line Chart */
.line-chart {
  height: 200px;
  display: flex;
  align-items: center;
}

.line-svg {
  width: 100%;
  height: 100%;
}

.chart-labels {
  display: flex;
  justify-content: space-between;
  margin-top: 12px;
  padding: 0 10px;
}

.chart-labels span {
  font-size: 12px;
  color: #94a3b8;
}

/* Appointments */
.appointments-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.appointments-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.btn-view-all {
  padding: 8px 16px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-view-all:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.appointments-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.appointment-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  border-radius: 10px;
  background: #f8fafc;
  transition: background 0.2s;
}

.appointment-item:hover {
  background: #f1f5f9;
}

.appointment-avatar {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #dbeafe;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2563eb;
  font-size: 16px;
}

.appointment-info {
  flex: 1;
}

.appointment-patient {
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 4px;
}

.appointment-details {
  font-size: 12px;
  color: #64748b;
  margin-bottom: 2px;
}

.appointment-type {
  font-size: 12px;
  color: #94a3b8;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  text-transform: lowercase;
}

.status-badge.confirmed {
  background: #ecfdf5;
  color: #10b981;
}

.status-badge.pending {
  background: #fef3c7;
  color: #d97706;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

/* Responsive */
@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .charts-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .dashboard-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }
}
</style>