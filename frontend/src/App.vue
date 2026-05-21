<<template>
  <div class="app">
    <!-- Sidebar (visible only when logged in) -->
    <aside v-if="isLoggedIn" class="sidebar">
      <!-- Logo -->
      <div class="sidebar-logo">
        <div class="logo-icon-small">
          <i class="fas fa-stethoscope"></i>
        </div>
        <span class="logo-text-small">MediCare</span>
      </div>

      <!-- Navigation - Dynamic based on role -->
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

      <!-- Profile at bottom -->
      <div class="sidebar-profile" @click="toggleProfileMenu" ref="profileRef">
        <div class="profile-avatar">{{ userInitials }}</div>
        <div class="profile-info">
          <p class="profile-name">{{ userFullName }}</p>
          <p class="profile-role">{{ userRoleLabel }}</p>
        </div>
        <i class="fas fa-chevron-up profile-arrow" :class="{ open: showProfileMenu }"></i>

        <!-- Dropdown Menu -->
        <div v-if="showProfileMenu" class="profile-dropdown">
          <div class="dropdown-header">
            <p class="dropdown-title">My Account</p>
          </div>
          <div class="dropdown-divider"></div>
          <router-link to="/settings" class="dropdown-item" @click="showProfileMenu = false" v-if="isAdmin">
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

    <!-- Main Content -->
    <main :class="['main-content', !isLoggedIn ? 'full' : '']">
      <!-- Header (visible only when logged in) -->
      <header v-if="isLoggedIn" class="top-header">
        <div class="search-bar">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Search patients, appointments..." />
        </div>
        <div class="header-actions">
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
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();
const showProfileMenu = ref(false);
const profileRef = ref(null);

// Check if logged in (either admin or secretaire)
const isLoggedIn = computed(() => {
  return !!localStorage.getItem('admin_token') || !!localStorage.getItem('secretaire_token');
});

const userRole = computed(() => {
  return localStorage.getItem('user_role') || 'admin';
});

const isAdmin = computed(() => userRole.value === 'admin');

const userRoleLabel = computed(() => {
  return userRole.value === 'admin' ? 'Admin' : 'Secrétaire';
});

// Get user data based on role
const userData = computed(() => {
  const key = userRole.value === 'admin' ? 'admin_user' : 'secretaire_user';
  const user = localStorage.getItem(key);
  return user ? JSON.parse(user) : { nom: 'User', prenom: 'Admin' };
});

const userFullName = computed(() => {
  return `${userData.value.prenom} ${userData.value.nom}`.trim();
});

const userInitials = computed(() => {
  const prenom = userData.value.prenom?.charAt(0) || 'A';
  const nom = userData.value.nom?.charAt(0) || 'D';
  return `${prenom}${nom}`.toUpperCase();
});

// Dynamic menu based on role
const menuItems = computed(() => {
  if (userRole.value === 'secretaire') {
    return [
      { path: '/secretaire/dashboard', label: 'Dashboard', icon: 'fas fa-th-large' },
      { path: '/secretaire/patients', label: 'Patients', icon: 'fas fa-users' },
      { path: '/secretaire/appointments', label: 'Appointments', icon: 'fas fa-calendar-check' },
      { path: '/secretaire/consultations', label: 'Consultations', icon: 'fas fa-stethoscope' },
      { path: '/secretaire/invoices', label: 'Invoices', icon: 'fas fa-file-invoice-dollar' },
      { path: '/secretaire/doctors', label: 'Doctors', icon: 'fas fa-user-md' },

    
    ];
  }
  // Admin menu
  return [
    //{ path: '/secretaire/consultations', label: 'Consultations', icon: 'fas fa-stethoscope' },
    { path: '/dashboard', label: 'Dashboard', icon: 'fas fa-th-large' },
    { path: '/patients', label: 'Patients', icon: 'fas fa-user-injured' },
    { path: '/rendez-vous', label: 'Appointments', icon: 'fas fa-calendar-check' },
    { path: '/consultations', label: 'Consultations', icon: 'fas fa-stethoscope' },
    { path: '/medecins', label: 'Doctors', icon: 'fas fa-user-md' },
    { path: '/factures', label: 'Invoices', icon: 'fas fa-file-invoice-dollar' },
    { path: '/dossiers', label: 'Medical Records', icon: 'fas fa-folder-open' },
    { path: '/settings', label: 'Settings', icon: 'fas fa-cog' },
  ];
});

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

const handleLogout = async () => {
  showProfileMenu.value = false;
  
  // Clear all auth data
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

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

html, body, #app {
  height: 100vh;
  height: 100dvh;
  width: 100%;
  overflow: hidden;
  margin: 0;
  padding: 0;
}

body { 
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
  background: #f8fafc; 
  color: #1e293b;
}

.app { 
  display: flex; 
  height: 100vh;
  width: 100%;
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
  top: 0;
  left: 0;
  bottom: 0;
  z-index: 100;
}

.sidebar-logo {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
  flex-shrink: 0;
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
  overflow-y: auto;
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
  flex-shrink: 0;
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
  cursor: pointer;
  position: relative;
  transition: background 0.2s;
  flex-shrink: 0;
  background: white;
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
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
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
  height: 100vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
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
  flex-shrink: 0;
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

/* ========== CONTENT AREA ========== */
.content-area {
  flex: 1;
  padding: 24px 32px;
  overflow-y: auto;
  background: #f8fafc;
}

/* Scrollbar styling */
.content-area::-webkit-scrollbar {
  width: 6px;
}

.content-area::-webkit-scrollbar-track {
  background: transparent;
}

.content-area::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    width: 70px;
  }
  
  .logo-text-small, .profile-info, .nav-item span {
    display: none;
  }
  
  .main-content {
    margin-left: 70px;
  }
  
  .search-bar {
    width: 200px;
  }
}
</style>