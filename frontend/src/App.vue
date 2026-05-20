<template>
  <div class="app">
    <!-- Sidebar (visible seulement si connecté) -->
    <aside v-if="isLoggedIn" class="sidebar">
      <!-- Logo -->
      <div class="sidebar-logo">
        <div class="logo-icon-small">
          <i class="fas fa-stethoscope"></i>
        </div>
        <span class="logo-text-small">MediCare</span>
      </div>

      <!-- Navigation -->
      <nav class="sidebar-nav">
        <router-link to="/dashboard" class="nav-item" :class="{ active: $route.path === '/dashboard' }">
          <i class="fas fa-th-large"></i>
          <span>Dashboard</span>
        </router-link>
        <router-link to="/patients" class="nav-item" :class="{ active: $route.path === '/patients' }">
          <i class="fas fa-user-injured"></i>
          <span>Patients</span>
        </router-link>
        <router-link to="/rendez-vous" class="nav-item" :class="{ active: $route.path === '/rendez-vous' }">
          <i class="fas fa-calendar-check"></i>
          <span>Appointments</span>
        </router-link>
        <router-link to="/consultations" class="nav-item" :class="{ active: $route.path === '/consultations' }">
          <i class="fas fa-stethoscope"></i>
          <span>Consultations</span>
        </router-link>
        <router-link to="/medecins" class="nav-item" :class="{ active: $route.path === '/medecins' }">
          <i class="fas fa-user-md"></i>
          <span>Doctors</span>
        </router-link>
        <router-link to="/factures" class="nav-item" :class="{ active: $route.path === '/factures' }">
          <i class="fas fa-file-invoice-dollar"></i>
          <span>Invoices</span>
        </router-link>
        <router-link to="/dossiers" class="nav-item" :class="{ active: $route.path === '/dossiers' }">
          <i class="fas fa-folder-open"></i>
          <span>Medical Records</span>
        </router-link>
        <router-link to="/settings" class="nav-item" :class="{ active: $route.path === '/settings' }">
          <i class="fas fa-cog"></i>
          <span>Settings</span>
        </router-link>
      </nav>

      <!-- Profil en bas avec dropdown -->
      <div class="sidebar-profile" @click="toggleProfileMenu" ref="profileRef">
        <div class="profile-avatar">{{ userInitials }}</div>
        <div class="profile-info">
          <p class="profile-name">{{ userFullName }}</p>
          <p class="profile-role">Admin</p>
        </div>
        <i class="fas fa-chevron-up profile-arrow" :class="{ open: showProfileMenu }"></i>

        <!-- Dropdown Menu -->
        <div v-if="showProfileMenu" class="profile-dropdown">
          <div class="dropdown-header">
            <p class="dropdown-title">My Account</p>
          </div>
          <div class="dropdown-divider"></div>
          <router-link to="/settings" class="dropdown-item" @click="showProfileMenu = false">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
          </router-link>
          <div class="dropdown-divider"></div>
          <button @click="handleLogout" class="dropdown-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main :class="['main-content', !isLoggedIn ? 'full' : '']">
      <!-- Header (visible seulement si connecté) -->
      <header v-if="isLoggedIn" class="top-header">
        <div class="search-bar">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Search patients, appointments..." />
        </div>
        <div class="header-actions">
          <!-- <div class="notification-bell">
            <i class="fas fa-bell"></i>
            <span class="badge">3</span>
          </div> -->
          <div class="header-profile">
            <span class="header-name">{{ userFullName }}</span>
            <div class="header-avatar">{{ userInitials }}</div>
          </div>
        </div>
      </header>

      <!-- Router View -->
      <div class="content-area">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { logout } from '@/services/api.js';

const router = useRouter();
const showProfileMenu = ref(false);
const profileRef = ref(null);

const isLoggedIn = computed(() => !!localStorage.getItem('token'));

// 🔥 Récupérer les vraies infos de l'utilisateur
const userData = computed(() => {
  const user = localStorage.getItem('user');
  return user ? JSON.parse(user) : { nom: 'Admin', prenom: 'User' };
});

const userFullName = computed(() => {
  return `${userData.value.prenom} ${userData.value.nom}`;
});

const userInitials = computed(() => {
  const prenom = userData.value.prenom?.charAt(0) || 'A';
  const nom = userData.value.nom?.charAt(0) || 'D';
  return `${prenom}${nom}`;
});

// Toggle le menu dropdown
const toggleProfileMenu = () => {
  showProfileMenu.value = !showProfileMenu.value;
};

// Fermer le menu quand on clique ailleurs
const handleClickOutside = (event) => {
  if (profileRef.value && !profileRef.value.contains(event.target)) {
    showProfileMenu.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const handleLogout = async () => {
  showProfileMenu.value = false;
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

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { 
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
  background: #f8fafc; 
  color: #1e293b;
}

.app { 
  display: flex; 
  min-height: 100vh; 
}

/* ========== SIDEBAR ========== */
.sidebar {
  width: 260px;
  background: white;
  border-right: 1px solid #e2e8f0;
  display: flex;
  flex-direction: column;
  position: fixed;
  height: 100vh;
  z-index: 100;
}

.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
}

.logo-icon-small {
  width: 32px;
  height: 32px;
  background: #2563eb;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-icon-small i {
  color: white;
  font-size: 16px;
}

.logo-text-small {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
}

.sidebar-nav {
  flex: 1;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 8px;
  color: #64748b;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
}

.nav-item i {
  font-size: 18px;
  width: 20px;
  text-align: center;
}

.nav-item:hover {
  background: #f1f5f9;
  color: #1e293b;
}

.nav-item.active {
  background: #eff6ff;
  color: #2563eb;
}

/* ========== SIDEBAR PROFILE ========== */
.sidebar-profile {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px 24px;
  border-top: 1px solid #f1f5f9;
  margin-top: auto;
  cursor: pointer;
  position: relative;
  transition: background 0.2s;
}

.sidebar-profile:hover {
  background: #f8fafc;
}

.profile-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #2563eb;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 600;
  flex-shrink: 0;
}

.profile-info {
  flex: 1;
  min-width: 0;
}

.profile-name {
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.profile-role {
  font-size: 12px;
  color: #64748b;
}

.profile-arrow {
  font-size: 12px;
  color: #94a3b8;
  transition: transform 0.2s;
  margin-left: auto;
}

.profile-arrow.open {
  transform: rotate(180deg);
}

/* ========== DROPDOWN MENU ========== */
.profile-dropdown {
  position: absolute;
  bottom: calc(100% + 8px);
  left: 16px;
  right: 16px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15), 0 2px 8px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
  z-index: 200;
  overflow: hidden;
  animation: dropdownSlide 0.2s ease-out;
}

@keyframes dropdownSlide {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-header {
  padding: 12px 16px;
}

.dropdown-title {
  font-size: 13px;
  font-weight: 600;
  color: #1e293b;
}

.dropdown-divider {
  height: 1px;
  background: #f1f5f9;
  margin: 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  color: #374151;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
  background: none;
  border: none;
  width: 100%;
  cursor: pointer;
  text-align: left;
}

.dropdown-item:hover {
  background: #f8fafc;
}

.dropdown-item i {
  font-size: 16px;
  color: #64748b;
  width: 20px;
  text-align: center;
}

.dropdown-item.logout {
  color: #dc2626;
}

.dropdown-item.logout i {
  color: #dc2626;
}

.dropdown-item.logout:hover {
  background: #fef2f2;
}

/* ========== MAIN CONTENT ========== */
.main-content {
  margin-left: 260px;
  flex: 1;
  min-height: 100vh;
}

.main-content.full {
  margin-left: 0;
}

/* ========== TOP HEADER ========== */
.top-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 32px;
  background: white;
  border-bottom: 1px solid #e2e8f0;
}

.search-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f1f5f9;
  padding: 10px 16px;
  border-radius: 10px;
  width: 320px;
}

.search-bar i {
  color: #94a3b8;
  font-size: 14px;
}

.search-bar input {
  border: none;
  background: none;
  outline: none;
  font-size: 14px;
  color: #1e293b;
  width: 100%;
}

.search-bar input::placeholder {
  color: #94a3b8;
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 20px;
}

.notification-bell {
  position: relative;
  cursor: pointer;
  padding: 8px;
  border-radius: 8px;
  transition: background 0.2s;
}

.notification-bell:hover {
  background: #f1f5f9;
}

.notification-bell i {
  font-size: 18px;
  color: #64748b;
}

.notification-bell .badge {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 18px;
  height: 18px;
  background: #ef4444;
  color: white;
  font-size: 11px;
  font-weight: 600;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.header-profile {
  display: flex;
  align-items: center;
  gap: 10px;
}

.header-name {
  font-size: 14px;
  font-weight: 500;
  color: #1e293b;
}

.header-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #dbeafe;
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 600;
}

.content-area {
  padding: 24px 32px;
}
</style>