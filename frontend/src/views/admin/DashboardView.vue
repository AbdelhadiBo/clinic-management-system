<template>
<<<<<<< HEAD
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
        <p class="stat-value">{{ montantTotal }} MAD</p>
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
=======
  <div class="p-8">
    <h1 class="text-2xl font-bold text-slate-800">📁 Dossiers Médicaux (Admin)</h1>
    <p class="text-slate-500 mt-2">En cours de développement...</p>
>>>>>>> a1d070d0ccd25a854b121fa52d4bf12376cfe215
  </div>
</template>