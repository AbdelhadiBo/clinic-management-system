<template>
  <div class="app">
    <!-- Sidebar -->
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
        <router-link
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="nav-item"
          :class="{ active: $route.path === item.path || $route.path.startsWith(item.path + '/') }"
        >
          <i :class="item.icon"></i>
          <span>{{ item.label }}</span>
        </router-link>
      </nav>

      <!-- Profile -->
      <div class="sidebar-profile" @click="toggleProfileMenu" ref="profileRef">
        <div class="profile-avatar">{{ userInitials }}</div>

        <div class="profile-info">
          <p class="profile-name">{{ userFullName }}</p>
          <p class="profile-role">{{ userRoleLabel }}</p>
        </div>

        <i class="fas fa-chevron-up profile-arrow" :class="{ open: showProfileMenu }"></i>

        <!-- Dropdown -->
        <div v-if="showProfileMenu" class="profile-dropdown">
          <div class="dropdown-header">
            <p class="dropdown-title">My Account</p>
          </div>

          <div class="dropdown-divider"></div>

          <router-link
            to="/settings"
            class="dropdown-item"
            @click="showProfileMenu = false"
            v-if="isAdmin"
          >
            <i class="fas fa-cog"></i>
            <span>Settings</span>
          </router-link>

          <div class="dropdown-divider" v-if="isAdmin"></div>

          <button @click="handleLogout" class="dropdown-item logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </button>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <main :class="['main-content', !isLoggedIn ? 'full' : '']">
      <!-- Header -->
      <header v-if="isLoggedIn" class="top-header">
        <div class="search-bar">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Search patients, appointments..." />
        </div>

        <div class="header-actions">
          <!-- Notification -->
          <!--
          <div class="notification-bell">
            <i class="fas fa-bell"></i>
            <span class="badge">3</span>
          </div>
          -->

          <div class="header-profile">
            <span class="header-name">{{ userFullName }}</span>
            <div class="header-avatar">{{ userInitials }}</div>
          </div>
        </div>
      </header>

      <!-- Content -->
      <div class="content-area">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { logout } from '@/services/api.js';

const router = useRouter();
const route = useRoute();

const showProfileMenu = ref(false);
const profileRef = ref(null);

// Auth
const isLoggedIn = computed(() => {
  return (
    !!localStorage.getItem('admin_token') ||
    !!localStorage.getItem('secretaire_token') ||
    !!localStorage.getItem('token')
  );
});

const userRole = computed(() => {
  return localStorage.getItem('user_role') || 'admin';
});

const isAdmin = computed(() => userRole.value === 'admin');

const userRoleLabel = computed(() => {
  return userRole.value === 'admin' ? 'Admin' : 'Secrétaire';
});

// User data
const userData = computed(() => {
  const key =
    userRole.value === 'admin'
      ? 'admin_user'
      : 'secretaire_user';

  const user =
    localStorage.getItem(key) ||
    localStorage.getItem('user');

  return user
    ? JSON.parse(user)
    : { nom: 'User', prenom: 'Admin' };
});

const userFullName = computed(() => {
  return `${userData.value.prenom} ${userData.value.nom}`.trim();
});

const userInitials = computed(() => {
  const prenom = userData.value.prenom?.charAt(0) || 'A';
  const nom = userData.value.nom?.charAt(0) || 'D';

  return `${prenom}${nom}`.toUpperCase();
});

// Menu
const menuItems = computed(() => {
  if (userRole.value === 'secretaire') {
    return [
      {
        path: '/secretaire/dashboard',
        label: 'Dashboard',
        icon: 'fas fa-th-large',
      },
      {
        path: '/secretaire/patients',
        label: 'Patients',
        icon: 'fas fa-users',
      },
      {
        path: '/secretaire/appointments',
        label: 'Appointments',
        icon: 'fas fa-calendar-check',
      },
      {
        path: '/secretaire/consultations',
        label: 'Consultations',
        icon: 'fas fa-stethoscope',
      },
      {
        path: '/secretaire/invoices',
        label: 'Invoices',
        icon: 'fas fa-file-invoice-dollar',
      },
      {
        path: '/secretaire/doctors',
        label: 'Doctors',
        icon: 'fas fa-user-md',
      },
    ];
  }

  return [
    {
      path: '/dashboard',
      label: 'Dashboard',
      icon: 'fas fa-th-large',
    },
    {
      path: '/patients',
      label: 'Patients',
      icon: 'fas fa-user-injured',
    },
    {
      path: '/rendez-vous',
      label: 'Appointments',
      icon: 'fas fa-calendar-check',
    },
    {
      path: '/consultations',
      label: 'Consultations',
      icon: 'fas fa-stethoscope',
    },
    {
      path: '/medecins',
      label: 'Doctors',
      icon: 'fas fa-user-md',
    },
    {
      path: '/factures',
      label: 'Invoices',
      icon: 'fas fa-file-invoice-dollar',
    },
    {
      path: '/dossiers',
      label: 'Medical Records',
      icon: 'fas fa-folder-open',
    },
    {
      path: '/settings',
      label: 'Settings',
      icon: 'fas fa-cog',
    },
  ];
});

// Dropdown
const toggleProfileMenu = () => {
  showProfileMenu.value = !showProfileMenu.value;
};

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

// Logout
const handleLogout = async () => {
  showProfileMenu.value = false;

  try {
    await logout();
  } catch (e) {
    console.log('Logout API error');
  }

  localStorage.removeItem('admin_token');
  localStorage.removeItem('admin_user');
  localStorage.removeItem('secretaire_token');
  localStorage.removeItem('secretaire_user');
  localStorage.removeItem('user_role');
  localStorage.removeItem('token');
  localStorage.removeItem('user');

  router.push('/login');
};
</script>
