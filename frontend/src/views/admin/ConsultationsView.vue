<template>
  <div class="consultations-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-title">
        <h1 class="page-title">Consultations</h1>
        <p class="page-subtitle">Record and manage patient consultations</p>
      </div>
      <button @click="openAddModal" class="btn-add">
        <span class="icon">+</span>
        Add Consultation
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-info">
          <span class="stat-label">Total Consultations</span>
          <span class="stat-value">{{ consultations.length }}</span>
        </div>
        <div class="stat-icon blue">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
          </svg>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-info">
          <span class="stat-label">This Month</span>
          <span class="stat-value" style="color: #10b981;">{{ thisMonthCount }}</span>
        </div>
        <div class="stat-icon green">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
            <line x1="16" x2="16" y1="2" y2="6"/>
            <line x1="8" x2="8" y1="2" y2="6"/>
            <line x1="3" x2="21" y1="10" y2="10"/>
          </svg>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-info">
          <span class="stat-label">This Week</span>
          <span class="stat-value" style="color: #a855f7;">{{ thisWeekCount }}</span>
        </div>
        <div class="stat-icon purple">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
            <line x1="16" x2="16" y1="2" y2="6"/>
            <line x1="8" x2="8" y1="2" y2="6"/>
            <line x1="3" x2="21" y1="10" y2="10"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Recent Consultations -->
    <div class="consultations-card">
      <div class="card-header">
        <h3>Recent Consultations</h3>
      </div>

      <div v-if="loading" class="loading">Chargement...</div>

      <div v-else-if="consultations.length > 0" class="consultations-list">
        <div 
          v-for="consultation in consultations" 
          :key="consultation.id_consultation" 
          class="consultation-item"
        >
          <!-- Header with icon and patient info -->
          <div class="consultation-header">
            <div class="patient-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
              </svg>
            </div>
            <div class="patient-info">
              <h4 class="patient-name">
                {{ consultation.rendez_vous?.patient?.prenom || consultation.rendezVous?.patient?.prenom || '-' }} 
                {{ consultation.rendez_vous?.patient?.nom || consultation.rendezVous?.patient?.nom || '-' }}
              </h4>
              <p class="consultation-meta">
                Consultation with Dr. {{ consultation.medecin?.prenom || '' }} {{ consultation.medecin?.nom || '-' }}
              </p>
              <span class="consultation-date">{{ formatDate(consultation.date) }}</span>
            </div>
            <div class="header-actions">
              <span v-if="consultation.facture" class="badge status-paye">
                {{ consultation.facture.montant_total }} DA
              </span>
              <button v-if="!consultation.facture" @click="openFactureModal(consultation)" class="btn-facture-small">
                💰 Create Invoice
              </button>
              <button v-else @click="voirFacture(consultation.facture)" class="btn-view">
                View Details
              </button>
            </div>
          </div>

          <!-- Details -->
          <div class="consultation-details">
            <div class="detail-section">
              <span class="detail-label">Diagnosis:</span>
              <p class="detail-text">{{ consultation.diagnostic || 'No diagnosis recorded' }}</p>
            </div>
            
            <div class="detail-section" v-if="consultation.observations">
              <span class="detail-label">Notes:</span>
              <p class="detail-text">{{ consultation.observations }}</p>
            </div>

            <!-- Prescription -->
            <div class="prescription-box" v-if="consultation.traitement">
              <span class="prescription-label">Prescription:</span>
              <p class="prescription-text">{{ consultation.traitement }}</p>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="empty-state">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="empty-icon">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
        </svg>
        <h3>No consultations recorded</h3>
        <p>Create your first consultation to get started</p>
      </div>
    </div>

    <!-- Modal Ajouter Consultation -->
    <div v-if="showAddModal" class="modal-overlay" @click.self="closeAddModal">
      <div class="modal">
        <div class="modal-header">
          <h2>➕ New Consultation</h2>
          <button @click="closeAddModal" class="btn-close">×</button>
        </div>
        
        <form @submit.prevent="saveConsultation" class="modal-form">
          <div class="form-group">
            <label>Appointment *</label>
            <select v-model="consultForm.id_rdv" required>
              <option value="">-- Select appointment --</option>
              <option v-for="rdv in rendezVousList" :key="rdv.id_rdv" :value="rdv.id_rdv">
                {{ rdv.patient?.nom }} {{ rdv.patient?.prenom }} - Dr. {{ rdv.medecin?.nom }} ({{ rdv.date_rdv }})
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Doctor *</label>
            <select v-model="consultForm.id_medecin" required>
              <option value="">-- Select doctor --</option>
              <option v-for="m in medecins" :key="m.id_medecin" :value="m.id_medecin">
                Dr. {{ m.nom }} - {{ m.specialite }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Nurse</label>
            <select v-model="consultForm.id_infirmier">
              <option value="">-- Select nurse --</option>
              <option v-for="i in infirmiers" :key="i.id_infirmier" :value="i.id_infirmier">
                {{ i.nom }} {{ i.prenom }} - {{ i.service }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Date *</label>
            <input v-model="consultForm.date" type="date" required />
          </div>
          <div class="form-group">
            <label>Diagnosis</label>
            <textarea v-model="consultForm.diagnostic" placeholder="Patient diagnosis..." rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Treatment / Prescription</label>
            <textarea v-model="consultForm.traitement" placeholder="Prescribed treatment..." rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Observations</label>
            <textarea v-model="consultForm.observations" placeholder="Additional observations..." rows="2"></textarea>
          </div>
          
          <div class="modal-footer">
            <button type="submit" class="btn-save">💾 Save</button>
            <button type="button" @click="closeAddModal" class="btn-cancel">❌ Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Créer Facture -->
    <div v-if="showFactureModal" class="modal-overlay" @click.self="closeFactureModal">
      <div class="modal">
        <div class="modal-header">
          <h2>💰 Create Invoice</h2>
          <button @click="closeFactureModal" class="btn-close">×</button>
        </div>
        
        <p class="info">
          Consultation #{{ selectedConsultation?.id_consultation }} - 
          {{ selectedConsultation?.rendez_vous?.patient?.nom || selectedConsultation?.rendezVous?.patient?.nom }} 
          {{ selectedConsultation?.rendez_vous?.patient?.prenom || selectedConsultation?.rendezVous?.patient?.prenom }}
        </p>
        
        <form @submit.prevent="saveFacture" class="modal-form">
          <div class="form-group">
            <label>Total Amount *</label>
            <input v-model="factureForm.montant_total" type="number" step="0.01" placeholder="2500.00" required />
          </div>
          <div class="form-group">
            <label>Date *</label>
            <input v-model="factureForm.date" type="date" required />
          </div>
          <div class="form-group">
            <label>Payment Method</label>
            <select v-model="factureForm.mode_paiement">
              <option value="Cash">Cash</option>
              <option value="Credit Card">Credit Card</option>
              <option value="Check">Check</option>
              <option value="Transfer">Transfer</option>
            </select>
          </div>
          <div class="form-group">
            <label>Status</label>
            <select v-model="factureForm.statut_paiement">
              <option value="payé">Paid</option>
              <option value="non payé">Unpaid</option>
            </select>
          </div>
          
          <div class="modal-footer">
            <button type="submit" class="btn-save">💾 Create Invoice</button>
            <button type="button" @click="closeFactureModal" class="btn-cancel">❌ Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { 
  getConsultations, 
  addConsultation,
  addFacture,
  getRendezVous,
  getMedecins,
  getInfirmiers
} from '@/services/api.js';

const consultations = ref([]);
const rendezVousList = ref([]);
const medecins = ref([]);
const infirmiers = ref([]);
const loading = ref(false);
const showAddModal = ref(false);
const showFactureModal = ref(false);
const selectedConsultation = ref(null);

const consultForm = ref({
  id_rdv: '',
  id_medecin: '',
  id_infirmier: '',
  date: '',
  diagnostic: '',
  traitement: '',
  observations: ''
});

const factureForm = ref({
  id_consultation: '',
  date: '',
  montant_total: '',
  statut_paiement: 'payé',
  mode_paiement: 'Cash'
});

// ============ COMPUTED ============
const thisMonthCount = computed(() => {
  const now = new Date();
  return consultations.value.filter(c => {
    const date = new Date(c.date);
    return date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear();
  }).length;
});

const thisWeekCount = computed(() => {
  const now = new Date();
  const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
  return consultations.value.filter(c => new Date(c.date) >= startOfWeek).length;
});

// ============ LIFECYCLE ============
onMounted(() => {
  loadConsultations();
  loadSelectData();
});

// ============ METHODS ============
const loadConsultations = async () => {
  loading.value = true;
  try {
    const response = await getConsultations();
    consultations.value = response.data.data || [];
  } catch (error) {
    console.error('Erreur:', error);
    alert('Unable to load consultations');
  } finally {
    loading.value = false;
  }
};

const loadSelectData = async () => {
  try {
    const [rdvRes, mRes, iRes] = await Promise.all([
      getRendezVous(),
      getMedecins(),
      getInfirmiers()
    ]);
    rendezVousList.value = rdvRes.data.data || [];
    medecins.value = mRes.data.data || [];
    infirmiers.value = iRes.data.data || [];
  } catch (error) {
    console.error('Erreur chargement données:', error);
  }
};

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  return new Date(dateStr).toLocaleDateString('en-GB');
};

const openAddModal = () => {
  resetConsultForm();
  showAddModal.value = true;
};

const saveConsultation = async () => {
  try {
    await addConsultation(consultForm.value);
    alert('✅ Consultation added successfully!');
    closeAddModal();
    await loadConsultations();
  } catch (error) {
    console.error('Erreur:', error);
    alert('❌ Error adding consultation');
  }
};

const closeAddModal = () => {
  showAddModal.value = false;
  resetConsultForm();
};

const resetConsultForm = () => {
  consultForm.value = {
    id_rdv: '',
    id_medecin: '',
    id_infirmier: '',
    date: '',
    diagnostic: '',
    traitement: '',
    observations: ''
  };
};

const openFactureModal = (consultation) => {
  selectedConsultation.value = consultation;
  factureForm.value = {
    id_consultation: consultation.id_consultation,
    date: new Date().toISOString().split('T')[0],
    montant_total: '',
    statut_paiement: 'payé',
    mode_paiement: 'Cash'
  };
  showFactureModal.value = true;
};

const saveFacture = async () => {
  try {
    await addFacture(factureForm.value);
    alert('✅ Invoice created successfully!');
    closeFactureModal();
    await loadConsultations();
  } catch (error) {
    console.error('Erreur:', error);
    alert('❌ Error creating invoice');
  }
};

const closeFactureModal = () => {
  showFactureModal.value = false;
  selectedConsultation.value = null;
};

const voirFacture = (facture) => {
  alert(`Invoice #${facture.id_facture}\nAmount: ${facture.montant_total} DA\nStatus: ${facture.statut_paiement}\nMethod: ${facture.mode_paiement}`);
};
</script>

<style scoped>
.consultations-page {
  padding: 24px 32px;
  max-width: 1400px;
  margin: 0 auto;
}

/* Page Header */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
}

.header-title h1 {
  font-size: 28px;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.page-subtitle {
  color: #64748b;
  font-size: 14px;
  margin: 0;
}

.btn-add {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #2563eb;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  transition: all 0.2s;
}

.btn-add:hover {
  background: #1d4ed8;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-add .icon {
  font-size: 18px;
  font-weight: 400;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
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
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-label {
  font-size: 13px;
  color: #64748b;
  margin-bottom: 4px;
}

.stat-value {
  font-size: 24px;
  font-weight: 700;
  color: #1e293b;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon.blue {
  background: #eff6ff;
  color: #3b82f6;
}

.stat-icon.green {
  background: #ecfdf5;
  color: #10b981;
}

.stat-icon.purple {
  background: #f3e8ff;
  color: #a855f7;
}

/* Consultations Card */
.consultations-card {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.card-header {
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
}

.card-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
}

/* Consultations List */
.consultations-list {
  padding: 16px;
}

.consultation-item {
  padding: 20px;
  border-radius: 10px;
  border: 1px solid #f1f5f9;
  margin-bottom: 16px;
  transition: background 0.15s;
}

.consultation-item:last-child {
  margin-bottom: 0;
}

.consultation-item:hover {
  background: #f8fafc;
}

/* Consultation Header */
.consultation-header {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 16px;
}

.patient-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: #eff6ff;
  color: #3b82f6;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.patient-info {
  flex: 1;
}

.patient-name {
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 4px 0;
}

.consultation-meta {
  font-size: 13px;
  color: #64748b;
  margin: 0 0 4px 0;
}

.consultation-date {
  font-size: 12px;
  color: #94a3b8;
}

.header-actions {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 8px;
}

.badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.status-paye {
  background: #d1fae5;
  color: #065f46;
}

.btn-facture-small {
  padding: 6px 14px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 500;
  white-space: nowrap;
}

.btn-view {
  padding: 8px 16px;
  background: white;
  color: #374151;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}

.btn-view:hover {
  background: #f8fafc;
  border-color: #cbd5e1;
}

/* Consultation Details */
.consultation-details {
  padding-left: 64px;
}

.detail-section {
  margin-bottom: 12px;
}

.detail-label {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  display: block;
  margin-bottom: 4px;
}

.detail-text {
  font-size: 14px;
  color: #475569;
  margin: 0;
  line-height: 1.5;
}

/* Prescription Box */
.prescription-box {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 12px 16px;
  margin-top: 12px;
}

.prescription-label {
  font-size: 13px;
  font-weight: 600;
  color: #15803d;
  display: block;
  margin-bottom: 4px;
}

.prescription-text {
  font-size: 14px;
  color: #166534;
  margin: 0;
  line-height: 1.5;
}

/* Loading */
.loading {
  padding: 60px;
  text-align: center;
  color: #64748b;
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 80px;
  color: #94a3b8;
}

.empty-icon {
  margin-bottom: 16px;
  color: #cbd5e1;
}

.empty-state h3 {
  margin: 0 0 8px 0;
  color: #475569;
  font-size: 16px;
}

.empty-state p {
  margin: 0;
  font-size: 14px;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 20px;
}

.modal {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 560px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 24px 0;
}

.modal-header h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
}

.btn-close {
  background: none;
  border: none;
  font-size: 24px;
  color: #94a3b8;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: all 0.2s;
}

.btn-close:hover {
  background: #f1f5f9;
  color: #475569;
}

.info {
  color: #718096;
  margin: 0 24px 20px;
  font-size: 14px;
}

.modal-form {
  padding: 0 24px 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  margin-bottom: 6px;
  font-size: 13px;
  font-weight: 600;
  color: #374151;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  background: white;
  transition: all 0.2s;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.modal-footer {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 8px;
  padding-top: 16px;
  border-top: 1px solid #f1f5f9;
}

.btn-save {
  padding: 10px 24px;
  border: none;
  background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
  color: white;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-save:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(72, 187, 120, 0.3);
}

.btn-cancel {
  padding: 10px 20px;
  border: 1px solid #e5e7eb;
  background: white;
  color: #374151;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel:hover {
  background: #f9fafb;
}
</style>