<template>
  <div class="page">
    <h1>📋 Gestion des Consultations</h1>

    <button @click="openAddModal" class="btn-add">+ Nouvelle Consultation</button>

    <div v-if="loading" class="loading">Chargement...</div>

    <table v-else-if="consultations.length > 0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Patient</th>
          <th>Médecin</th>
          <th>Infirmier</th>
          <th>Date</th>
          <th>Diagnostic</th>
          <th>Traitement</th>
          <th>Facture</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="consultation in consultations" :key="consultation.id_consultation">
          <td>#{{ consultation.id_consultation }}</td>
          <td>
            {{ consultation.rendez_vous?.patient?.nom || '-' }} 
            {{ consultation.rendez_vous?.patient?.prenom || '' }}
          </td>
          <td>Dr. {{ consultation.medecin?.nom || '-' }}</td>
          <td>{{ consultation.infirmier?.nom || '-' }}</td>
          <td>{{ consultation.date }}</td>
          <td>{{ consultation.diagnostic || '-' }}</td>
          <td>{{ consultation.traitement || '-' }}</td>
          <td>
            <span v-if="consultation.facture" class="badge status-paye">
              {{ consultation.facture.montant_total }} DA
            </span>
            <span v-else class="badge status-non-paye">Sans facture</span>
          </td>
          <td>
            <button v-if="!consultation.facture" @click="openFactureModal(consultation)" class="btn-facture">
              💰 Créer Facture
            </button>
            <button v-else @click="voirFacture(consultation.facture)" class="btn-voir">
              👁️ Voir
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-else class="empty">Aucune consultation enregistrée</p>

    <!-- Modal Ajouter Consultation -->
    <div v-if="showAddModal" class="modal-overlay" @click.self="closeAddModal">
      <div class="modal">
        <h2>➕ Nouvelle Consultation</h2>
        <form @submit.prevent="saveConsultation">
          <div class="form-group">
            <label>Rendez-vous *</label>
            <select v-model="consultForm.id_rdv" required>
              <option value="">-- Choisir un RDV --</option>
              <option v-for="rdv in rendezVousList" :key="rdv.id_rdv" :value="rdv.id_rdv">
                {{ rdv.patient?.nom }} {{ rdv.patient?.prenom }} - Dr. {{ rdv.medecin?.nom }} ({{ rdv.date_rdv }})
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Médecin *</label>
            <select v-model="consultForm.id_medecin" required>
              <option value="">-- Choisir un médecin --</option>
              <option v-for="m in medecins" :key="m.id_medecin" :value="m.id_medecin">
                Dr. {{ m.nom }} - {{ m.specialite }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Infirmier</label>
            <select v-model="consultForm.id_infirmier">
              <option value="">-- Choisir un infirmier --</option>
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
            <label>Diagnostic</label>
            <textarea v-model="consultForm.diagnostic" placeholder="Diagnostic du patient" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Traitement</label>
            <textarea v-model="consultForm.traitement" placeholder="Traitement prescrit" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Observations</label>
            <textarea v-model="consultForm.observations" placeholder="Observations complémentaires" rows="2"></textarea>
          </div>
          
          <div class="modal-buttons">
            <button type="submit" class="btn-save">💾 Enregistrer</button>
            <button type="button" @click="closeAddModal" class="btn-cancel">❌ Annuler</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Créer Facture -->
    <div v-if="showFactureModal" class="modal-overlay" @click.self="closeFactureModal">
      <div class="modal">
        <h2>💰 Créer une Facture</h2>
        <p class="info">
          Consultation #{{ selectedConsultation?.id_consultation }} - 
          {{ selectedConsultation?.rendez_vous?.patient?.nom }} 
          {{ selectedConsultation?.rendez_vous?.patient?.prenom }}
        </p>
        <form @submit.prevent="saveFacture">
          <div class="form-group">
            <label>Montant Total *</label>
            <input v-model="factureForm.montant_total" type="number" step="0.01" placeholder="2500.00" required />
          </div>
          <div class="form-group">
            <label>Date *</label>
            <input v-model="factureForm.date" type="date" required />
          </div>
          <div class="form-group">
            <label>Mode de Paiement</label>
            <select v-model="factureForm.mode_paiement">
              <option value="Espèces">Espèces</option>
              <option value="Carte bancaire">Carte bancaire</option>
              <option value="Chèque">Chèque</option>
              <option value="Virement">Virement</option>
            </select>
          </div>
          <div class="form-group">
            <label>Statut</label>
            <select v-model="factureForm.statut_paiement">
              <option value="payé">Payé</option>
              <option value="non payé">Non Payé</option>
            </select>
          </div>
          
          <div class="modal-buttons">
            <button type="submit" class="btn-save">💾 Créer la Facture</button>
            <button type="button" @click="closeFactureModal" class="btn-cancel">❌ Annuler</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
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
  mode_paiement: 'Espèces'
});

onMounted(() => {
  loadConsultations();
  loadSelectData();
});

const loadConsultations = async () => {
  loading.value = true;
  try {
    const response = await getConsultations();
    consultations.value = response.data.data;
  } catch (error) {
    console.error('Erreur:', error);
    alert('Impossible de charger les consultations');
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
    rendezVousList.value = rdvRes.data.data;
    medecins.value = mRes.data.data;
    infirmiers.value = iRes.data.data;
  } catch (error) {
    console.error('Erreur chargement données:', error);
  }
};

const openAddModal = () => {
  resetConsultForm();
  showAddModal.value = true;
};

const saveConsultation = async () => {
  try {
    await addConsultation(consultForm.value);
    alert('✅ Consultation ajoutée avec succès !');
    closeAddModal();
    await loadConsultations();
  } catch (error) {
    console.error('Erreur:', error);
    alert('❌ Erreur lors de l\'ajout de la consultation');
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
    mode_paiement: 'Espèces'
  };
  showFactureModal.value = true;
};

const saveFacture = async () => {
  try {
    await addFacture(factureForm.value);
    alert('✅ Facture créée avec succès !');
    closeFactureModal();
    await loadConsultations();
  } catch (error) {
    console.error('Erreur:', error);
    alert('❌ Erreur lors de la création de la facture');
  }
};

const closeFactureModal = () => {
  showFactureModal.value = false;
  selectedConsultation.value = null;
};

const voirFacture = (facture) => {
  alert(`Facture #${facture.id_facture}\nMontant: ${facture.montant_total} DA\nStatut: ${facture.statut_paiement}\nMode: ${facture.mode_paiement}`);
};
</script>

<style scoped>
.page { padding: 30px; max-width: 1400px; margin: 0 auto; }
h1 { color: #2c3e50; margin-bottom: 20px; }
.btn-add { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; margin-bottom: 20px; }
.loading { text-align: center; padding: 50px; color: #7f8c8d; font-size: 18px; }
table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
th { background: #4a5568; color: white; padding: 15px; text-align: left; }
td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; }
tr:hover { background: #f7fafc; }
.badge { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
.status-paye { background: #d1fae5; color: #065f46; }
.status-non-paye { background: #fee2e2; color: #991b1b; }
.btn-facture { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
.btn-voir { background: #4299e1; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; }
.empty { text-align: center; padding: 60px; color: #a0aec0; font-size: 18px; }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; justify-content: center; align-items: center; z-index: 1000; }
.modal { background: white; padding: 30px; border-radius: 15px; width: 500px; max-height: 90vh; overflow-y: auto; }
.modal h2 { margin-bottom: 10px; color: #2d3748; }
.info { color: #718096; margin-bottom: 20px; font-size: 14px; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; color: #4a5568; font-weight: 500; font-size: 14px; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 15px; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #667eea; }
.modal-buttons { display: flex; gap: 12px; margin-top: 25px; }
.btn-save, .btn-cancel { flex: 1; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; }
.btn-save { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; }
.btn-cancel { background: #e2e8f0; color: #4a5568; }
</style>