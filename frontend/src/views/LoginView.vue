<<template>
  <div class="login-page">
    <!-- Logo -->
    <div class="logo-header">
      <div class="logo-icon">
        <i class="fas fa-stethoscope"></i>
      </div>
      <h1 class="logo-text">MediCare</h1>
    </div>
    <p class="tagline">Clinic Management System</p>

    <!-- Carte Login -->
    <div class="login-card">
      <h2 class="welcome">Welcome Back</h2>
      <p class="subtitle">Sign in to access your dashboard</p>

      <!-- Role Selector (NEW - styled to match your design) -->
      <div class="role-selector">
        <button 
          :class="['role-btn', { active: role === 'admin' }]" 
          @click="role = 'admin'"
          type="button"
        >
          <i class="fas fa-user-shield"></i>
          Admin
        </button>
        <button 
          :class="['role-btn', { active: role === 'secretaire' }]" 
          @click="role = 'secretaire'"
          type="button"
        >
          <i class="fas fa-user-nurse"></i>
          Secrétaire
        </button>
      </div>

      <form @submit.prevent="handleLogin">
        <!-- Email -->
        <div class="form-group">
          <label>Email</label>
          <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input 
              v-model="form.email" 
              type="email" 
              :placeholder="role === 'admin' ? 'admin@clinique.com' : 'secretaire@clinique.com'"
              required 
            />
          </div>
        </div>

        <!-- Password -->
        <div class="form-group">
          <label>Password</label>
          <div class="input-wrapper">
            <i class="fas fa-lock input-icon"></i>
            <input 
              v-model="form.password" 
              type="password" 
              placeholder="••••••••"
              required 
            />
          </div>
        </div>

        <!-- Options -->
        <div class="options">
          <label class="remember">
            <input type="checkbox" v-model="rememberMe" />
            <span>Remember me</span>
          </label>
          <a href="#" class="forgot">Forgot password?</a>
        </div>

        <!-- Bouton -->
        <button type="submit" class="btn-signin" :disabled="loading">
          {{ loading ? 'Signing in...' : 'Sign In as ' + (role === 'admin' ? 'Admin' : 'Secrétaire') }}
        </button>

        <!-- Erreur -->
        <p v-if="error" class="error">{{ error }}</p>
      </form>
    </div>

    <!-- Footer -->
    <p class="footer">© 2026 MediCare. All rights reserved.</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api.js';

const router = useRouter();
const loading = ref(false);
const error = ref('');
const rememberMe = ref(false);
const role = ref('admin'); // 'admin' or 'secretaire'

const form = ref({
  email: '',
  password: ''
});

const handleLogin = async () => {
  loading.value = true;
  error.value = '';
  
  try {
    console.log('🔑 Sending login request...', { ...form.value, role: role.value });
    
    // Choose endpoint based on role
    const endpoint = role.value === 'admin' ? '/admin/login' : '/secretaire/login';
    const response = await api.post(endpoint, {
      email: form.value.email,
      password: form.value.password
    });
    
    console.log('✅ Login response:', response.data);
    
    if (response.data.success && response.data.token) {
      // Save token with role prefix
      const tokenKey = role.value === 'admin' ? 'admin_token' : 'secretaire_token';
      const userKey = role.value === 'admin' ? 'admin_user' : 'secretaire_user';
      
      localStorage.setItem(tokenKey, response.data.token);
      localStorage.setItem(userKey, JSON.stringify(response.data.user));
      localStorage.setItem('user_role', role.value);
      
      // Set auth header
      api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
      
      if (rememberMe.value) {
        localStorage.setItem('remember', 'true');
      }
      
      // Redirect based on role
      if (role.value === 'admin') {
        router.push('/dashboard');
      } else {
        router.push('/secretaire/dashboard');
      }
    } else {
      error.value = 'No token received from server';
      console.error('❌ No token in response:', response.data);
    }
  } catch (err) {
    console.error('❌ Login error:', err);
    console.error('Error response:', err.response?.data);
    
    const status = err.response?.status;
    const serverMessage = err.response?.data?.message;
    
    if (status === 401 || serverMessage === 'Invalid credentials') {
      error.value = 'The email address or password you entered is incorrect. Please try again.';
    } else if (status === 500) {
      error.value = 'An unexpected error occurred. Please try again later or contact support.';
    } else if (status === 422) {
      error.value = 'Please check your information and ensure all fields are filled correctly.';
    } else {
      error.value = 'Unable to sign in. Please verify your credentials and try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* ==================== YOUR ORIGINAL STYLES (UNCHANGED) ==================== */

.login-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: linear-gradient(180deg, #f0f7ff 0%, #ffffff 100%);
  padding: 20px;
}

/* Logo */
.logo-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}

.logo-icon {
  width: 44px;
  height: 44px;
  background: #2563eb;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-icon i {
  color: white;
  font-size: 22px;
}

.logo-text {
  font-size: 28px;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
  letter-spacing: -0.5px;
}

.tagline {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 32px;
}

/* Carte */
.login-card {
  background: white;
  padding: 40px;
  border-radius: 16px;
  width: 100%;
  max-width: 420px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 
              0 2px 4px -1px rgba(0, 0, 0, 0.06),
              0 20px 25px -5px rgba(0, 0, 0, 0.05);
}

.welcome {
  font-size: 24px;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 4px;
}

.subtitle {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 28px;
}

/* Form */
.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  margin-bottom: 6px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 12px;
  color: #9ca3af;
  font-size: 14px;
}

.input-wrapper input {
  width: 100%;
  padding: 12px 12px 12px 40px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  background: #f9fafb;
  transition: all 0.2s;
}

.input-wrapper input:focus {
  outline: none;
  border-color: #2563eb;
  background: white;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

/* Options */
.options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.remember {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: #374151;
  cursor: pointer;
}

.remember input {
  width: 16px;
  height: 16px;
  accent-color: #2563eb;
}

.forgot {
  font-size: 14px;
  color: #2563eb;
  text-decoration: none;
}

.forgot:hover {
  text-decoration: underline;
}

/* Bouton */
.btn-signin {
  width: 100%;
  padding: 14px;
  background: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-signin:hover:not(:disabled) {
  background: #1d4ed8;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-signin:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Erreur */
.error {
  color: #dc2626;
  font-size: 14px;
  text-align: center;
  margin-top: 12px;
}

/* Footer */
.footer {
  margin-top: 24px;
  font-size: 13px;
  color: #94a3b8;
}

/* ==================== NEW ROLE SELECTOR STYLES ==================== */

.role-selector {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
}

.role-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  background: white;
  color: #64748b;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.role-btn:hover {
  border-color: #2563eb;
  color: #2563eb;
}

.role-btn.active {
  border-color: #2563eb;
  background: #eff6ff;
  color: #2563eb;
}

.role-btn i {
  font-size: 16px;
}
</style>