<<template>
  <div class="page">
    <!-- Page Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Consultations</h1>
        <p class="page-subtitle">Manage patient consultations and diagnoses</p>
      </div>
      <button @click="openModal" class="btn-primary">
        <i class="fas fa-plus"></i>
        New Consultation
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-info">
          <span class="stat-label">Total Consultations</span>
          <span class="stat-value blue">{{ totalConsultations }}</span>
        </div>
        <div class="stat-icon blue">
          <i class="fas fa-stethoscope"></i>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-info">
          <span class="stat-label">Today</span>
          <span class="stat-value green">{{ todayConsultations }}</span>
        </div>
        <div class="stat-icon green">
          <i class="fas fa-calendar-day"></i>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-info">
          <span class="stat-label">Pending</span>
          <span class="stat-value orange">{{ pendingConsultations }}</span>
        </div>
        <div class="stat-icon orange">
          <i class="fas fa-clock"></i>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-info">
          <span class="stat-label">Completed</span>
          <span class="stat-value purple">{{ completedConsultations }}</span>
        </div>
        <div class="stat-icon purple">
          <i class="fas fa-check-circle"></i>
        </div>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="filter-card">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Search consultations..."
        />
      </div>
      <select v-model="statusFilter" class="filter-select">
        <option value="">All Status</option>
        <option value="completed">Completed</option>
        <option value="pending">Pending</option>
      </select>
    </div>

    <!-- Consultations Table -->
    <div class="table-card">
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <span>Loading consultations...</span>
      </div>

      <table v-else class="data-table">
        <thead>
          <tr>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Diagnosis</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="consultation in filteredConsultations" :key="consultation.id_consultation">
            <td>
              <div class="patient-cell">
                <div class="patient-avatar">{{ getInitials(consultation.rendez_vous?.patient) }}</div>
                <div class="patient-info">
                  <p class="patient-name">{{ consultation.rendez_vous?.patient?.prenom }} {{ consultation.rendez_vous?.patient?.nom }}</p>
                  <p class="patient-id">ID: {{ consultation.rendez_vous?.patient?.id_patient }}</p>
                </div>
              </div>
            </td>
            <td>
              <div class="doctor-cell">
                <p class="doctor-name">Dr. {{ consultation.rendez_vous?.medecin?.prenom }} {{ consultation.rendez_vous?.medecin?.nom }}</p>
                <p class="doctor-specialty">{{ consultation.rendez_vous?.medecin?.specialite }}</p>
              </div>
            </td>
            <td>
              <div class="date-cell">
                <p class="date">{{ formatDate(consultation.date) }}</p>
                <p class="time">{{ consultation.rendez_vous?.heure }}</p>
              </div>
            </td>
            <td>
              <p class="diagnosis">{{ consultation.diagnostic || 'No diagnosis' }}</p>
            </td>
            <td>
              <span :class="['status-badge', consultation.statut]">
                {{ consultation.statut }}
              </span>
            </td>
            <td>
              <div class="actions">
                <button @click="viewConsultation(consultation)" class="btn-icon blue" title="View">
                  <i class="fas fa-eye"></i>
                </button>
                <button @click="editConsultation(consultation)" class="btn-icon green" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="deleteConsultation(consultation.id_consultation)" class="btn-icon red" title="Delete">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="!loading && filteredConsultations.length === 0" class="empty-state">
        <i class="fas fa-folder-open empty-icon"></i>
        <p>No consultations found</p>
      </div>
    </div>

    <!-- ADD/EDIT Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>{{ editing ? 'Edit Consultation' : 'New Consultation' }}</h3>
          <button @click="closeModal" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="saveConsultation">
            <div class="form-group">
              <label>Patient</label>
              <select v-model="form.id_patient" required class="form-select">
                <option value="">Select Patient</option>
                <option v-for="patient in patients" :key="patient.id_patient" :value="patient.id_patient">
                  {{ patient.prenom }} {{ patient.nom }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Doctor</label>
              <select v-model="form.id_medecin" required class="form-select">
                <option value="">Select Doctor</option>
                <option v-for="medecin in medecins" :key="medecin.id_medecin" :value="medecin.id_medecin">
                  Dr. {{ medecin.prenom }} {{ medecin.nom }} - {{ medecin.specialite }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label>Date</label>
              <input v-model="form.date" type="date" required class="form-input" />
            </div>
            <div class="form-group">
              <label>Diagnosis</label>
              <textarea v-model="form.diagnostic" rows="3" class="form-textarea" placeholder="Enter diagnosis..."></textarea>
            </div>
            <div class="form-group">
              <label>Treatment</label>
              <textarea v-model="form.traitement" rows="3" class="form-textarea" placeholder="Enter treatment plan..."></textarea>
            </div>
            <div class="form-group">
              <label>Status</label>
              <select v-model="form.statut" class="form-select">
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
              </select>
            </div>
            <div class="modal-footer">
              <button type="button" @click="closeModal" class="btn-secondary">Close</button>
              <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i>
                {{ editing ? 'Update' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- VIEW Modal (Card style) -->
    <div v-if="showViewModal" class="modal-overlay" @click="closeViewModal">
      <div class="view-modal" @click.stop>
        <div class="view-header">
          <div class="view-patient">
            <div class="view-avatar">{{ getInitials(selectedConsultation?.rendez_vous?.patient) }}</div>
            <div>
              <h3>{{ selectedConsultation?.rendez_vous?.patient?.prenom }} {{ selectedConsultation?.rendez_vous?.patient?.nom }}</h3>
              <p class="view-id">Patient ID: {{ selectedConsultation?.rendez_vous?.patient?.id_patient }}</p>
            </div>
          </div>
          <button @click="closeViewModal" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="view-body">
          <div class="view-section">
            <h4><i class="fas fa-user-md"></i> Doctor Information</h4>
            <div class="view-row">
              <span class="view-label">Doctor:</span>
              <span class="view-value">Dr. {{ selectedConsultation?.rendez_vous?.medecin?.prenom }} {{ selectedConsultation?.rendez_vous?.medecin?.nom }}</span>
            </div>
            <div class="view-row">
              <span class="view-label">Specialty:</span>
              <span class="view-value">{{ selectedConsultation?.rendez_vous?.medecin?.specialite || 'General' }}</span>
            </div>
          </div>

          <div class="view-section">
            <h4><i class="fas fa-calendar-alt"></i> Appointment Details</h4>
            <div class="view-row">
              <span class="view-label">Date:</span>
              <span class="view-value">{{ formatDate(selectedConsultation?.date) }}</span>
            </div>
            <div class="view-row">
              <span class="view-label">Time:</span>
              <span class="view-value">{{ selectedConsultation?.rendez_vous?.heure || '-' }}</span>
            </div>
            <div class="view-row">
              <span class="view-label">Status:</span>
              <span :class="['status-badge', selectedConsultation?.statut]">{{ selectedConsultation?.statut }}</span>
            </div>
          </div>

          <div class="view-section">
            <h4><i class="fas fa-stethoscope"></i> Medical Information</h4>
            <div class="view-row block">
              <span class="view-label">Diagnosis:</span>
              <p class="view-text">{{ selectedConsultation?.diagnostic || 'No diagnosis recorded' }}</p>
            </div>
            <div class="view-row block">
              <span class="view-label">Treatment:</span>
              <p class="view-text">{{ selectedConsultation?.traitement || 'No treatment recorded' }}</p>
            </div>
          </div>
        </div>

        <div class="view-footer">
          <button @click="closeViewModal" class="btn-secondary">Close</button>
         
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { getConsultations, getPatients, getMedecins, createConsultation, updateConsultation, deleteConsultation as deleteConsultationApi } from '@/services/api.js';

const consultations = ref([]);
const patients = ref([]);
const medecins = ref([]);
const loading = ref(false);
const searchQuery = ref('');
const statusFilter = ref('');
const showModal = ref(false);
const showViewModal = ref(false);
const editing = ref(false);
const selectedConsultation = ref(null);

const form = ref({
  id_consultation: null,
  id_patient: '',
  id_medecin: '',
  date: '',
  diagnostic: '',
  traitement: '',
  statut: 'pending'
});

onMounted(() => {
  loadData();
});

const loadData = async () => {
  loading.value = true;
  try {
    const [consultationsRes, patientsRes, medecinsRes] = await Promise.all([
      getConsultations(),
      getPatients(),
      getMedecins()
    ]);
    
    consultations.value = consultationsRes.data.data || [];
    patients.value = patientsRes.data.data || [];
    medecins.value = medecinsRes.data.data || [];
  } catch (error) {
    console.error('Error loading data:', error);
    alert('Unable to load consultations');
  } finally {
    loading.value = false;
  }
};

const filteredConsultations = computed(() => {
  let result = [...consultations.value];
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(c => 
      c.rendez_vous?.patient?.nom?.toLowerCase().includes(query) ||
      c.rendez_vous?.patient?.prenom?.toLowerCase().includes(query) ||
      c.diagnostic?.toLowerCase().includes(query) ||
      c.rendez_vous?.medecin?.nom?.toLowerCase().includes(query)
    );
  }
  
  if (statusFilter.value) {
    result = result.filter(c => c.statut === statusFilter.value);
  }
  
  return result.sort((a, b) => new Date(b.date) - new Date(a.date));
});

const totalConsultations = computed(() => consultations.value.length);
const todayConsultations = computed(() => {
  const today = new Date().toISOString().split('T')[0];
  return consultations.value.filter(c => c.date === today).length;
});
const pendingConsultations = computed(() => consultations.value.filter(c => c.statut === 'pending').length);
const completedConsultations = computed(() => consultations.value.filter(c => c.statut === 'completed').length);

const getInitials = (patient) => {
  if (!patient) return '?';
  return `${patient.prenom?.charAt(0) || ''}${patient.nom?.charAt(0) || ''}`.toUpperCase();
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  return date.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const openModal = () => {
  editing.value = false;
  form.value = {
    id_consultation: null,
    id_patient: '',
    id_medecin: '',
    date: '',
    diagnostic: '',
    traitement: '',
    statut: 'pending'
  };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const viewConsultation = (consultation) => {
  selectedConsultation.value = consultation;
  showViewModal.value = true;
};

const closeViewModal = () => {
  showViewModal.value = false;
  selectedConsultation.value = null;
};

const editFromView = () => {
  closeViewModal();
  editConsultation(selectedConsultation.value);
};

const editConsultation = (consultation) => {
  editing.value = true;
  form.value = {
    id_consultation: consultation.id_consultation,
    id_patient: consultation.rendez_vous?.patient?.id_patient,
    id_medecin: consultation.rendez_vous?.medecin?.id_medecin,
    date: consultation.date,
    diagnostic: consultation.diagnostic || '',
    traitement: consultation.traitement || '',
    statut: consultation.statut
  };
  showModal.value = true;
};

const saveConsultation = async () => {
  try {
    if (editing.value) {
      await updateConsultation(form.value.id_consultation, form.value);
    } else {
      await createConsultation(form.value);
    }
    closeModal();
    loadData();
  } catch (error) {
    console.error('Error saving consultation:', error);
    alert('Failed to save consultation');
  }
};

const deleteConsultation = async (id) => {
  if (!confirm('Are you sure you want to delete this consultation?')) return;
  
  try {
    await deleteConsultationApi(id);
    loadData();
  } catch (error) {
    console.error('Error deleting consultation:', error);
    alert('Failed to delete consultation');
  }
};
</script>

<style scoped>
.page {
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.page-subtitle {
  font-size: 14px;
  color: #64748b;
  margin: 0;
}

.btn-primary {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-primary:hover {
  background: #1d4ed8;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 24px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  border: 1px solid #f1f5f9;
}

.stat-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.stat-label {
  font-size: 13px;
  color: #64748b;
  font-weight: 500;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #1e293b;
}

.stat-value.blue { color: #3b82f6; }
.stat-value.green { color: #10b981; }
.stat-value.orange { color: #f59e0b; }
.stat-value.purple { color: #8b5cf6; }

.stat-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.stat-icon.blue { background: #eff6ff; color: #3b82f6; }
.stat-icon.green { background: #ecfdf5; color: #10b981; }
.stat-icon.orange { background: #fffbeb; color: #f59e0b; }
.stat-icon.purple { background: #f5f3ff; color: #8b5cf6; }

/* Filter Card */
.filter-card {
  display: flex;
  gap: 12px;
  margin-bottom: 20px;
  background: white;
  padding: 16px 20px;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  align-items: center;
}

.search-box {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f8fafc;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.search-box i {
  color: #94a3b8;
  font-size: 14px;
}

.search-box input {
  flex: 1;
  border: none;
  background: none;
  outline: none;
  font-size: 14px;
  color: #1e293b;
}

.search-box input::placeholder {
  color: #94a3b8;
}

.filter-select {
  padding: 10px 14px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  font-size: 14px;
  color: #64748b;
  cursor: pointer;
  min-width: 140px;
  outline: none;
}

.filter-select:focus {
  border-color: #3b82f6;
}

/* Table */
.table-card {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th {
  text-align: left;
  padding: 14px 20px;
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.data-table td {
  padding: 16px 20px;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
  color: #374151;
}

.patient-cell {
  display: flex;
  align-items: center;
  gap: 12px;
}

.patient-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 600;
}

.patient-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.patient-name {
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.patient-id {
  font-size: 12px;
  color: #94a3b8;
  margin: 0;
}

.doctor-cell {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.doctor-name {
  font-weight: 500;
  color: #374151;
  margin: 0;
}

.doctor-specialty {
  font-size: 12px;
  color: #94a3b8;
  margin: 0;
}

.date-cell {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.date {
  font-weight: 500;
  color: #374151;
  margin: 0;
}

.time {
  font-size: 12px;
  color: #94a3b8;
  margin: 0;
}

.diagnosis {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  margin: 0;
}

.status-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.status-badge.completed {
  background: #d1fae5;
  color: #065f46;
}

.status-badge.pending {
  background: #fef3c7;
  color: #92400e;
}

.actions {
  display: flex;
  gap: 8px;
}

.btn-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s;
}

.btn-icon.blue {
  background: #eff6ff;
  color: #3b82f6;
}

.btn-icon.blue:hover {
  background: #dbeafe;
}

.btn-icon.green {
  background: #ecfdf5;
  color: #10b981;
}

.btn-icon.green:hover {
  background: #d1fae5;
}

.btn-icon.red {
  background: #fef2f2;
  color: #ef4444;
}

.btn-icon.red:hover {
  background: #fee2e2;
}

/* Modal */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
}

.btn-close {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: none;
  background: #f1f5f9;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 14px;
}

.modal-body {
  padding: 24px;
}

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

.form-input,
.form-select,
.form-textarea {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  color: #1e293b;
  background: white;
  outline: none;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
  border-color: #3b82f6;
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}

.btn-secondary {
  padding: 10px 20px;
  background: #f1f5f9;
  color: #64748b;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-secondary:hover {
  background: #e2e8f0;
}

/* ========== VIEW MODAL (Card Style) ========== */
.view-modal {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.view-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 16px 16px 0 0;
}

.view-patient {
  display: flex;
  align-items: center;
  gap: 16px;
}

.view-avatar {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  font-weight: 700;
  border: 2px solid rgba(255, 255, 255, 0.3);
}

.view-header h3 {
  font-size: 20px;
  font-weight: 600;
  margin: 0;
  color: white;
}

.view-id {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.8);
  margin: 4px 0 0 0;
}

.view-body {
  padding: 24px;
}

.view-section {
  margin-bottom: 24px;
  padding-bottom: 20px;
  border-bottom: 1px solid #f1f5f9;
}

.view-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.view-section h4 {
  font-size: 14px;
  font-weight: 600;
  color: #64748b;
  margin: 0 0 16px 0;
  display: flex;
  align-items: center;
  gap: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.view-section h4 i {
  color: #3b82f6;
}

.view-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
}

.view-row.block {
  flex-direction: column;
  align-items: flex-start;
  gap: 8px;
}

.view-label {
  font-size: 13px;
  color: #94a3b8;
  font-weight: 500;
}

.view-value {
  font-size: 14px;
  color: #1e293b;
  font-weight: 500;
}

.view-text {
  font-size: 14px;
  color: #374151;
  line-height: 1.6;
  margin: 0;
  background: #f8fafc;
  padding: 12px 16px;
  border-radius: 8px;
  width: 100%;
}

.view-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 24px;
  border-top: 1px solid #e2e8f0;
}

/* Loading */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px;
  gap: 16px;
  color: #94a3b8;
}

.spinner {
  width: 32px;
  height: 32px;
  border: 3px solid #f1f5f9;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Empty */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px;
  gap: 12px;
  color: #94a3b8;
}

.empty-icon {
  font-size: 48px;
  color: #d1d5db;
}

@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .page-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }
  
  .filter-card {
    flex-direction: column;
    align-items: stretch;
  }
  
  .data-table {
    display: block;
    overflow-x: auto;
  }
  
  .view-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }
}
</style>