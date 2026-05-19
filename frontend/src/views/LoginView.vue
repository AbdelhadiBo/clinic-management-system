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

      <form @submit.prevent="handleLogin">
        <!-- Email -->
        <div class="form-group">
          <label>Email</label>
          <div class="input-wrapper">
            <i class="fas fa-envelope input-icon"></i>
            <input 
              v-model="form.email" 
              type="email" 
              placeholder="your.email@clinic.com"
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
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>

        <!-- Erreur -->
        <p v-if="error" class="error">{{ error }}</p>
      </form>

      <!-- Debug Info -->
      <div v-if="debugInfo" class="debug-box">
        <p><strong>Debug:</strong></p>
        <pre>{{ debugInfo }}</pre>
      </div>

      <!-- Demo Credentials -->
      
    </div>

    <!-- Footer -->
    <p class="footer">© 2026 MediCare. All rights reserved.</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { login } from '@/services/api.js';

const router = useRouter();
const loading = ref(false);
const error = ref('');
const debugInfo = ref('');
const rememberMe = ref(false);
const form = ref({
  email: '',
  password: ''
});

const handleLogin = async () => {
  loading.value = true;
  error.value = '';
  debugInfo.value = '';
  
  try {
    console.log('🔑 Sending login request...', form.value);
    const response = await login(form.value);
    
    console.log('✅ Login response:', response.data);
    debugInfo.value = JSON.stringify(response.data, null, 2);
    
    if (response.data.success && response.data.token) {
      // Sauvegarde le token
      localStorage.setItem('token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));
      
      console.log('💾 Token saved:', response.data.token.substring(0, 20) + '...');
      
      if (rememberMe.value) {
        localStorage.setItem('remember', 'true');
      }
      
      // Redirige
      router.push('/dashboard');
    } else {
      error.value = 'No token received from server';
      console.error('❌ No token in response:', response.data);
    }
  } catch (err) {
    console.error('❌ Login error:', err);
    console.error('Error response:', err.response?.data);
    
    error.value = err.response?.data?.message || 'Invalid credentials';
    debugInfo.value = JSON.stringify(err.response?.data || err.message, null, 2);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
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

/* Debug */
.debug-box {
  margin-top: 16px;
  padding: 12px;
  background: #fef3c7;
  border-radius: 8px;
  border: 1px solid #fcd34d;
}

.debug-box pre {
  font-size: 11px;
  color: #92400e;
  white-space: pre-wrap;
  word-break: break-all;
}

/* Demo */
.demo-box {
  margin-top: 24px;
  padding: 16px;
  background: #eff6ff;
  border-radius: 8px;
  border: 1px solid #dbeafe;
}

.demo-title {
  font-weight: 600;
  color: #1e40af;
  font-size: 13px;
  margin-bottom: 8px;
}

.demo-box p {
  font-size: 13px;
  color: #3b82f6;
  margin: 4px 0;
}

.demo-box strong {
  color: #1e40af;
}

/* Footer */
.footer {
  margin-top: 24px;
  font-size: 13px;
  color: #94a3b8;
}
</style>