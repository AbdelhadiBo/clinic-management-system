<template>
  <div class="dashboard">
    <!-- Header -->
    <div class="dashboard-header">
      <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="welcome-text">Welcome back, {{ userFullName }}</p>
      </div>
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
        <p class="stat-value">{{ montantTotal }} </p>
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

    <!-- Graphiques Dynamiques -->
    <div class="charts-grid">
      <div class="chart-card">
        <h3 class="chart-title">Appointments This Week</h3>
        <div v-if="loadingCharts" class="loading">Loading...</div>
        <div v-else class="chart-placeholder bar-chart">
          <div 
            v-for="(day, index) in weeklyAppointments" 
            :key="index"
            class="bar" 
            :style="{ height: day.percentage + '%' }"
            :title="day.label + ': ' + day.count + ' appointments'"
          ></div>
        </div>
        <div v-if="!loadingCharts" class="chart-labels">
          <span v-for="(day, index) in weeklyAppointments" :key="index">{{ day.label }}</span>
        </div>
      </div>

      <div class="chart-card">
        <h3 class="chart-title">Revenue Trend</h3>
        <div v-if="loadingCharts" class="loading">Loading...</div>
        <div v-else class="chart-placeholder line-chart">
          <svg viewBox="0 0 300 150" class="line-svg">
            <polyline 
              :points="revenuePoints" 
              fill="none" 
              stroke="#10b981" 
              stroke-width="3"
            />
            <circle 
              v-for="(point, index) in revenuePointsArray" 
              :key="index"
              :cx="point.x" 
              :cy="point.y" 
              r="4" 
              fill="#10b981"
            />
          </svg>
        </div>
        <div v-if="!loadingCharts" class="chart-labels">
          <span v-for="(month, index) in revenueMonths" :key="index">{{ month }}</span>
        </div>
      </div>
    </div>

    <!-- Today's Appointments -->
    <div class="appointments-card">
      <div class="appointments-header">
        <h3 class="chart-title">Today's Appointments</h3>
        <router-link to="/rendez-vous" class="btn-view-all">View All</router-link>
      </div>
      
      <div v-if="loading" class="loading">Loading appointments...</div>
      
      <div v-else-if="todayAppointments.length > 0" class="appointments-list">
        <div v-for="rdv in todayAppointments" :key="rdv.id_rdv" class="appointment-item">
          <div class="appointment-avatar">
            <i class="fas fa-clock"></i>
          </div>
          <div class="appointment-info">
            <p class="appointment-patient">{{ rdv.patient?.prenom }} {{ rdv.patient?.nom }}</p>
            <p class="appointment-details">{{ rdv.heure }} • Dr. {{ rdv.medecin?.prenom }} {{ rdv.medecin?.nom }}</p>
            <p class="appointment-type">{{ rdv.motif || 'General Checkup' }}</p>
          </div>
          <span :class="['status-badge', rdv.statut === 'confirmé' ? 'confirmed' : 'pending']">
            {{ rdv.statut }}
          </span>
        </div>
      </div>
      
      <div v-else class="empty-state">
        <i class="fas fa-calendar-day"></i>
        <p>No appointments scheduled for today</p>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <h3 class="chart-title">Quick Actions</h3>
      <div class="actions-grid">
        <router-link to="/patients" class="action-card">
          <div class="action-icon blue">
            <i class="fas fa-user-plus"></i>
          </div>
          <span>Add Patient</span>
        </router-link>
        <router-link to="/rendez-vous" class="action-card">
          <div class="action-icon green">
            <i class="fas fa-calendar-plus"></i>
          </div>
          <span>New Appointment</span>
        </router-link>
        <router-link to="/consultations" class="action-card">
          <div class="action-icon purple">
            <i class="fas fa-file-medical"></i>
          </div>
          <span>New Consultation</span>
        </router-link>
        <router-link to="/factures" class="action-card">
          <div class="action-icon teal">
            <i class="fas fa-file-invoice"></i>
          </div>
          <span>Create Invoice</span>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { getDashboardStats, getRendezVous, getPatients, getMedecins, getFactures } from '@/services/api.js';

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
const patients = ref([]);
const medecins = ref([]);
const factures = ref([]);
const loading = ref(false);
const loadingCharts = ref(false);

// 🔥 Nom complet de l'utilisateur connecté
const userFullName = computed(() => {
  const user = localStorage.getItem('user');
  if (user) {
    const parsed = JSON.parse(user);
    return `${parsed.prenom} ${parsed.nom}`;
  }
  return 'Admin';
});

const montantTotal = computed(() => {
  return stats.value.montant_total_factures?.toLocaleString() || '0';
});

// 🔥 Rendez-vous d'aujourd'hui
const todayAppointments = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return rendezVous.value.filter(rdv => rdv.date_rdv === today);
});

// 🔥 Rendez-vous de la semaine (pour le graphique)
const weeklyAppointments = computed(() => {
  const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
  const result = [];
  
  for (let i = 0; i < 7; i++) {
    const date = new Date();
    date.setDate(date.getDate() - date.getDay() + i);
    const dateStr = date.toISOString().split('T')[0];
    const count = rendezVous.value.filter(rdv => rdv.date_rdv === dateStr).length;
    result.push({
      label: days[i],
      count: count,
      percentage: Math.min(count * 20, 100) // Max 100%
    });
  }
  
  return result;
});

// 🔥 Points pour le graphique de revenus
const revenuePointsArray = computed(() => {
  const monthlyRevenue = [12000, 15000, 18000, 22000, 25000, stats.value.montant_total_factures || 28000];
  const max = Math.max(...monthlyRevenue);
  const points = [];
  
  monthlyRevenue.forEach((value, index) => {
    const x = (index / (monthlyRevenue.length - 1)) * 300;
    const y = 150 - (value / max) * 130;
    points.push({ x, y });
  });
  
  return points;
});

const revenuePoints = computed(() => {
  return revenuePointsArray.value.map(p => `${p.x},${p.y}`).join(' ');
});

const revenueMonths = computed(() => {
  const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
  return months;
});

onMounted(() => {
  loadAllData();
});

// 🔥 Charger toutes les données
const loadAllData = async () => {
  loading.value = true;
  loadingCharts.value = true;
  
  try {
    await Promise.all([
      loadStats(),
      loadRendezVous(),
      loadPatients(),
      loadMedecins(),
      loadFactures()
    ]);
  } catch (error) {
    console.error('❌ Error loading dashboard data:', error);
  } finally {
    loading.value = false;
    loadingCharts.value = false;
  }
};

const loadStats = async () => {
  try {
    const response = await getDashboardStats();
    if (response.data && response.data.data) {
      stats.value = response.data.data;
      console.log('✅ Stats loaded:', stats.value);
    }
  } catch (error) {
    console.error('❌ Error loading stats:', error);
  }
};

const loadRendezVous = async () => {
  try {
    const response = await getRendezVous();
    if (response.data && response.data.data) {
      rendezVous.value = response.data.data;
      console.log('✅ Rendez-vous loaded:', rendezVous.value.length);
    }
  } catch (error) {
    console.error('❌ Error loading rendez-vous:', error);
  }
};

const loadPatients = async () => {
  try {
    const response = await getPatients();
    if (response.data && response.data.data) {
      patients.value = response.data.data;
      stats.value.total_patients = patients.value.length;
    }
  } catch (error) {
    console.error('❌ Error loading patients:', error);
  }
};

const loadMedecins = async () => {
  try {
    const response = await getMedecins();
    if (response.data && response.data.data) {
      medecins.value = response.data.data;
      stats.value.total_medecins = medecins.value.length;
    }
  } catch (error) {
    console.error('❌ Error loading medecins:', error);
  }
};

const loadFactures = async () => {
  try {
    const response = await getFactures();
    if (response.data && response.data.data) {
      factures.value = response.data.data;
      const total = factures.value.reduce((sum, f) => sum + (parseFloat(f.montant_total) || 0), 0);
      stats.value.montant_total_factures = total;
      stats.value.total_factures = factures.value.length;
    }
  } catch (error) {
    console.error('❌ Error loading factures:', error);
  }
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
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
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
  transition: all 0.3s;
  cursor: pointer;
}

.bar:hover {
  opacity: 0.8;
  transform: scaleY(1.02);
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
  margin-bottom: 24px;
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
  text-decoration: none;
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

.empty-state {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
}

.empty-state i {
  font-size: 48px;
  margin-bottom: 12px;
  display: block;
}

/* Quick Actions */
.quick-actions {
  background: white;
  padding: 24px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.action-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 24px;
  border-radius: 10px;
  background: #f8fafc;
  text-decoration: none;
  color: #374151;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
}

.action-card:hover {
  background: #f1f5f9;
  transform: translateY(-2px);
}

.action-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.action-icon.blue { background: #eff6ff; color: #2563eb; }
.action-icon.green { background: #ecfdf5; color: #10b981; }
.action-icon.purple { background: #f5f3ff; color: #8b5cf6; }
.action-icon.teal { background: #f0fdfa; color: #14b8a6; }

/* Loading */
.loading {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
  font-size: 14px;
}

/* Responsive */
@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .charts-grid {
    grid-template-columns: 1fr;
  }
  .actions-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  .actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>