<<template>
  <div class="dashboard">
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1>Dashboard</h1>
        <p class="welcome-text">Welcome back, {{ userName }}</p>
      </div>
      <div class="header-actions">
        <div class="date-badge">
          <i class="fas fa-calendar"></i>
          {{ todayDate }}
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-content">
          <div class="stat-icon-box blue">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-info">
            <span class="stat-value">{{ stats.total_patients || 0 }}</span>
            <span class="stat-label">Total Patients</span>
          </div>
        </div>
        <div class="stat-trend up">
          <i class="fas fa-arrow-up"></i>
          <span>12% this month</span>
        </div>
      </div>

      <div class="stat-card primary">
        <div class="stat-content">
          <div class="stat-icon-box green">
            <i class="fas fa-calendar-check"></i>
          </div>
          <div class="stat-info">
            <span class="stat-value">{{ stats.rdv_aujourdhui || 0 }}</span>
            <span class="stat-label">Today's Appointments</span>
          </div>
        </div>
        <div class="stat-trend up">
          <i class="fas fa-arrow-up"></i>
          <span>8% vs yesterday</span>
        </div>
      </div>

      <div class="stat-card primary">
        <div class="stat-content">
          <div class="stat-icon-box teal">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
          <div class="stat-info">
            <span class="stat-value">{{ formatMoney(stats.montant_total_factures) }}</span>
            <span class="stat-label">Monthly Revenue</span>
          </div>
        </div>
        <div class="stat-trend up">
          <i class="fas fa-arrow-up"></i>
          <span>15% this month</span>
        </div>
      </div>

      <div class="stat-card primary">
        <div class="stat-content">
          <div class="stat-icon-box purple">
            <i class="fas fa-user-md"></i>
          </div>
          <div class="stat-info">
            <span class="stat-value">{{ stats.total_medecins || 0 }}</span>
            <span class="stat-label">Active Doctors</span>
          </div>
        </div>
        <div class="stat-trend neutral">
          <i class="fas fa-minus"></i>
          <span>No change</span>
        </div>
      </div>
    </div>

    <!-- Main Content Grid -->
    <div class="main-grid">
      <!-- Today's Appointments -->
      <div class="card appointments-card">
        <div class="card-header">
          <div class="header-title">
            <div class="header-icon blue">
              <i class="fas fa-calendar-day"></i>
            </div>
            <div>
              <h3>Today's Appointments</h3>
              <p class="header-subtitle">{{ todayAppointments.length }} scheduled</p>
            </div>
          </div>
          <router-link to="/secretaire/appointments" class="btn-view">
            View All <i class="fas fa-arrow-right"></i>
          </router-link>
        </div>

        <div v-if="todayAppointments.length" class="appointments-list">
          <div 
            v-for="rdv in todayAppointments.slice(0, 5)" 
            :key="rdv.id_rdv"
            class="appointment-row"
          >
            <div class="appointment-time">
              <span class="time">{{ formatTime(rdv.heure) }}</span>
              <span class="duration">30 min</span>
            </div>
            <div class="appointment-divider"></div>
            <div class="appointment-info">
              <div class="patient-avatar-small">
                {{ getInitials(rdv.patient) }}
              </div>
              <div class="patient-details">
                <p class="name">{{ rdv.patient?.prenom }} {{ rdv.patient?.nom }}</p>
                <p class="detail">
                  <i class="fas fa-user-md"></i>
                  Dr. {{ rdv.medecin?.nom }} • {{ rdv.motif || 'General Checkup' }}
                </p>
              </div>
            </div>
            <span :class="['status-pill', getStatusClass(rdv.statut)]">
              {{ rdv.statut }}
            </span>
          </div>
        </div>
        <div v-else class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-calendar-check"></i>
          </div>
          <p>No appointments scheduled for today</p>
          <router-link to="/secretaire/appointments" class="btn-link">
            Schedule one now
          </router-link>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card actions-card">
        <div class="card-header">
          <div class="header-title">
            <div class="header-icon purple">
              <i class="fas fa-bolt"></i>
            </div>
            <div>
              <h3>Quick Actions</h3>
              <p class="header-subtitle">Frequently used tasks</p>
            </div>
          </div>
        </div>

        <div class="actions-grid">
          <router-link to="/secretaire/patients" class="action-item">
            <div class="action-icon blue">
              <i class="fas fa-user-plus"></i>
            </div>
            <span class="action-label">Add Patient</span>
            <i class="fas fa-chevron-right action-arrow"></i>
          </router-link>

          <router-link to="/secretaire/appointments" class="action-item">
            <div class="action-icon green">
              <i class="fas fa-calendar-plus"></i>
            </div>
            <span class="action-label">New Appointment</span>
            <i class="fas fa-chevron-right action-arrow"></i>
          </router-link>

          <router-link to="/secretaire/consultations" class="action-item">
            <div class="action-icon teal">
              <i class="fas fa-stethoscope"></i>
            </div>
            <span class="action-label">View Consultation</span>
            <i class="fas fa-chevron-right action-arrow"></i>
          </router-link>

          <router-link to="/secretaire/invoices" class="action-item">
            <div class="action-icon orange">
              <i class="fas fa-file-invoice"></i>
            </div>
            <span class="action-label">View Invoice</span>
            <i class="fas fa-chevron-right action-arrow"></i>
          </router-link>
        </div>
      </div>
    </div>

    <!-- Bottom Grid -->
    <div class="bottom-grid">
      <!-- Recent Patients -->
      <div class="card">
        <div class="card-header">
          <div class="header-title">
            <div class="header-icon green">
              <i class="fas fa-user-clock"></i>
            </div>
            <div>
              <h3>Recent Patients</h3>
              <p class="header-subtitle">Last 5 registrations</p>
            </div>
          </div>
          <router-link to="/secretaire/patients" class="btn-view">
            View All <i class="fas fa-arrow-right"></i>
          </router-link>
        </div>

        <div v-if="recentPatients.length" class="patients-list">
          <div v-for="patient in recentPatients" :key="patient.id_patient" class="patient-row">
            <div class="patient-avatar-small" :style="{ background: getAvatarColor(patient.id_patient) }">
              {{ getInitials(patient) }}
            </div>
            <div class="patient-info-compact">
              <p class="name">{{ patient.prenom }} {{ patient.nom }}</p>
              <p class="meta">
                <i class="fas fa-phone"></i> {{ patient.telephone || 'No phone' }}
              </p>
            </div>
            <span class="patient-date">{{ formatDate(patient.created_at) }}</span>
          </div>
        </div>
        <div v-else class="empty-state compact">
          <p>No recent patients</p>
        </div>
      </div>

      <!-- Weekly Overview -->
      <div class="card">
        <div class="card-header">
          <div class="header-title">
            <div class="header-icon orange">
              <i class="fas fa-chart-bar"></i>
            </div>
            <div>
              <h3>Weekly Overview</h3>
              <p class="header-subtitle">Appointments this week</p>
            </div>
          </div>
        </div>

        <div class="week-chart">
          <div v-for="(day, index) in weekData" :key="index" class="day-bar">
            <div class="bar-container">
              <div 
                class="bar" 
                :style="{ height: day.percentage + '%' }"
                :class="{ 'bar-today': day.isToday }"
              ></div>
            </div>
            <span class="day-label">{{ day.label }}</span>
            <span class="day-value">{{ day.count }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onActivated, onUnmounted } from 'vue'
import { useRoute } from 'vue-router'
import { getDashboardStats, getRendezVous, getPatients } from '@/services/api.js'

const route = useRoute()

const user = ref(JSON.parse(localStorage.getItem('secretaire_user') || '{}'))

const userName = computed(() => {
  return `${user.value.prenom || ''} ${user.value.nom || ''}`.trim() || 'Secrétaire'
})

const todayDate = computed(() => {
  return new Date().toLocaleDateString('en-GB', { 
    weekday: 'long', 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  })
})

const stats = ref({})
const todayAppointments = ref([])
const recentPatients = ref([])
const weekData = ref([])

const loadDashboard = async () => {
  try {
    const [statsRes, rdvsRes, patientsRes] = await Promise.all([
      getDashboardStats(),
      getRendezVous(),
      getPatients()
    ])
    
    stats.value = statsRes.data.data || {}
    
    const allRdvs = rdvsRes.data.data || []
    const today = new Date().toISOString().split('T')[0]
    todayAppointments.value = allRdvs.filter(rdv => rdv.date_rdv?.startsWith(today))
    
    // Recent patients (last 5)
    const allPatients = patientsRes.data.data || []
    recentPatients.value = allPatients
      .sort((a, b) => new Date(b.created_at || 0) - new Date(a.created_at || 0))
      .slice(0, 5)
    
    // Weekly data
    generateWeekData(allRdvs)
  } catch (err) {
    console.error('Error loading dashboard:', err)
  }
}

const generateWeekData = (rdvs) => {
  const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
  const today = new Date()
  const week = []
  
  for (let i = 0; i < 7; i++) {
    const date = new Date(today)
    date.setDate(today.getDate() - today.getDay() + i)
    const dateStr = date.toISOString().split('T')[0]
    
    const count = rdvs.filter(rdv => rdv.date_rdv?.startsWith(dateStr)).length
    week.push({
      label: days[i],
      count: count,
      percentage: Math.min((count / 10) * 100, 100),
      isToday: i === today.getDay()
    })
  }
  
  weekData.value = week
}

const formatMoney = (amount) => {
  if (!amount) return '0 MAD'
  return new Intl.NumberFormat('en-GB', {
    style: 'currency',
    currency: 'MAD',
    minimumFractionDigits: 0
  }).format(amount)
}

const formatTime = (time) => {
  if (!time) return '--:--'
  return time.substring(0, 5)
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const date = new Date(dateStr)
  return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })
}

const getInitials = (person) => {
  if (!person) return '?'
  return `${person.prenom?.charAt(0) || ''}${person.nom?.charAt(0) || ''}`.toUpperCase()
}

const getAvatarColor = (id) => {
  const colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899']
  return colors[(id || 0) % colors.length]
}

const getStatusClass = (status) => {
  const classes = {
    'en attente': 'pending',
    'confirmé': 'confirmed',
    'annulé': 'cancelled',
    'terminé': 'completed'
  }
  return classes[status] || 'pending'
}

// Refresh logic
let refreshInterval = null

onMounted(() => {
  loadDashboard()
  refreshInterval = setInterval(() => {
    if (route.path === '/secretaire/dashboard') {
      loadDashboard()
    }
  }, 30000)
})

onActivated(() => {
  loadDashboard()
})

onUnmounted(() => {
  clearInterval(refreshInterval)
})
</script>

<style scoped>
.dashboard {
  max-width: 1400px;
  margin: 0 auto;
  padding-bottom: 40px;
}

/* Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
}

.page-header h1 {
  font-size: 32px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 6px 0;
  letter-spacing: -0.5px;
}

.welcome-text {
  color: #64748b;
  font-size: 15px;
  margin: 0;
}

.date-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: white;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  color: #64748b;
  font-size: 14px;
  font-weight: 500;
}

.date-badge i {
  color: #3b82f6;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 28px;
}

.stat-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  border: 1px solid #e2e8f0;
  transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
}

.stat-content {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}

.stat-icon-box {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
}

.stat-icon-box.blue { background: #eff6ff; color: #3b82f6; }
.stat-icon-box.green { background: #ecfdf5; color: #10b981; }
.stat-icon-box.teal { background: #f0fdfa; color: #14b8a6; }
.stat-icon-box.purple { background: #f5f3ff; color: #8b5cf6; }

.stat-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: #1e293b;
  line-height: 1;
}

.stat-label {
  font-size: 13px;
  color: #64748b;
  font-weight: 500;
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 600;
  padding-top: 12px;
  border-top: 1px solid #f1f5f9;
}

.stat-trend.up {
  color: #10b981;
}

.stat-trend.neutral {
  color: #94a3b8;
}

/* Cards */
.card {
  background: white;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid #f1f5f9;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 14px;
}

.header-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.header-icon.blue { background: #eff6ff; color: #3b82f6; }
.header-icon.green { background: #ecfdf5; color: #10b981; }
.header-icon.purple { background: #f5f3ff; color: #8b5cf6; }
.header-icon.orange { background: #fff7ed; color: #f97316; }

.card-header h3 {
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 2px 0;
}

.header-subtitle {
  font-size: 13px;
  color: #94a3b8;
  margin: 0;
}

.btn-view {
  display: flex;
  align-items: center;
  gap: 6px;
  color: #3b82f6;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  padding: 8px 14px;
  border-radius: 8px;
  transition: background 0.2s;
}

.btn-view:hover {
  background: #eff6ff;
}

/* Main Grid */
.main-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

/* Appointments */
.appointments-list {
  padding: 16px 24px 24px;
}

.appointment-row {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 0;
  border-bottom: 1px solid #f1f5f9;
}

.appointment-row:last-child {
  border-bottom: none;
}

.appointment-time {
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 60px;
}

.time {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
}

.duration {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 500;
}

.appointment-divider {
  width: 3px;
  height: 40px;
  background: #e2e8f0;
  border-radius: 3px;
}

.appointment-info {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 14px;
}

.patient-avatar-small {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}

.patient-details {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.patient-details .name {
  font-weight: 600;
  color: #1e293b;
  font-size: 14px;
  margin: 0;
}

.patient-details .detail {
  color: #94a3b8;
  font-size: 12px;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

.patient-details .detail i {
  font-size: 10px;
}

.status-pill {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
  white-space: nowrap;
}

.status-pill.pending {
  background: #fef3c7;
  color: #92400e;
}

.status-pill.confirmed {
  background: #d1fae5;
  color: #065f46;
}

.status-pill.cancelled {
  background: #fee2e2;
  color: #991b1b;
}

.status-pill.completed {
  background: #dbeafe;
  color: #1e40af;
}

/* Quick Actions */
.actions-grid {
  padding: 20px 24px 24px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.action-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 16px;
  border-radius: 12px;
  text-decoration: none;
  color: inherit;
  transition: background 0.2s;
}

.action-item:hover {
  background: #f8fafc;
}

.action-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}

.action-icon.blue { background: #eff6ff; color: #3b82f6; }
.action-icon.green { background: #ecfdf5; color: #10b981; }
.action-icon.teal { background: #f0fdfa; color: #14b8a6; }
.action-icon.orange { background: #fff7ed; color: #f97316; }

.action-label {
  flex: 1;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}

.action-arrow {
  color: #cbd5e1;
  font-size: 12px;
}

/* Bottom Grid */
.bottom-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

/* Patients List */
.patients-list {
  padding: 16px 24px 24px;
}

.patient-row {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 0;
  border-bottom: 1px solid #f1f5f9;
}

.patient-row:last-child {
  border-bottom: none;
}

.patient-info-compact {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.patient-info-compact .name {
  font-weight: 600;
  color: #1e293b;
  font-size: 14px;
  margin: 0;
}

.patient-info-compact .meta {
  color: #94a3b8;
  font-size: 12px;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

.patient-date {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 500;
}

/* Week Chart */
.week-chart {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  padding: 24px;
  gap: 12px;
  height: 200px;
}

.day-bar {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.bar-container {
  width: 100%;
  height: 120px;
  display: flex;
  align-items: flex-end;
  background: #f8fafc;
  border-radius: 8px;
  padding: 4px;
}

.bar {
  width: 100%;
  background: #cbd5e1;
  border-radius: 6px;
  transition: all 0.3s;
  min-height: 4px;
}

.bar-today {
  background: #3b82f6;
}

.day-label {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 600;
}

.day-value {
  font-size: 14px;
  font-weight: 700;
  color: #1e293b;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 24px;
  gap: 12px;
  color: #94a3b8;
}

.empty-state.compact {
  padding: 32px 24px;
}

.empty-icon {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: #cbd5e1;
}

.empty-state p {
  font-size: 14px;
  margin: 0;
}

.btn-link {
  color: #3b82f6;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
}

.btn-link:hover {
  text-decoration: underline;
}

/* Responsive */
@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .main-grid {
    grid-template-columns: 1fr;
  }
  
  .bottom-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .page-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }
  
  .appointment-row {
    flex-wrap: wrap;
  }
  
  .appointment-divider {
    display: none;
  }
}
</style>