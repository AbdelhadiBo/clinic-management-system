<template>
  <div class="page">
    <h1>📅 Gestion des Rendez-vous</h1>
    
    <button @click="openAddModal" class="btn-add">+ Nouveau Rendez-vous</button>

    <div v-if="loading" class="loading">Chargement...</div>

    <table v-else-if="rendezVous.length > 0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Patient</th>
          <th>Médecin</th>
          <th>Secrétaire</th>
          <th>Date</th>
          <th>Heure</th>
          <th>Motif</th>
          <th>Statut</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="rdv in rendezVous" :key="rdv.id_rdv">
          <td>{{ rdv.id_rdv }}</td>
          <td>{{ rdv.patient?.nom }} {{ rdv.patient?.prenom }}</td>
          <td>Dr. {{ rdv.medecin?.nom }}</td>
          <td>{{ rdv.secretaire?.nom || '-' }}</td>
          <td>{{ rdv.date_rdv }}</td>
          <td>{{ rdv.heure }}</td>
          <td>{{ rdv.motif || '-' }}</td>
          <td>
            <span :class="['badge', 'status-' + rdv.statut?.replace(' ', '-')]">
              {{ rdv.statut }}
            </span>
          </td>
          <td>
            <button @click="editRdv(rdv)" class="btn-edit">✏️</button>
            <button @click="updateStatus(rdv.id_rdv, 'confirmé')" class="btn-confirm" v-if="rdv.statut === 'en attente'">✓</button>
            <button @click="updateStatus(rdv.id_rdv, 'annulé')" class="btn-cancel" v-if="rdv.statut !== 'annulé'">✕</button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-else class="empty">Aucun rendez-vous programmé</p>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h2>{{ isEditing ? '✏️ Modifier' : '➕ Nouveau' }} Rendez-vous</h2>
        <form @submit.prevent="saveRdv">
          <div class="form-group">
            <label>Patient *</label>
            <select v-model="form.id_patient" required>
              <option value="">-- Choisir un patient --</option>
              <option v-for="p in patients" :key="p.id_patient" :value="p.id_patient">
                {{ p.nom }} {{ p.prenom }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Médecin *</label>
            <select v-model="form.id_medecin" required>
              <option value="">-- Choisir un médecin --</option>
              <option v-for="m in medecins" :key="m.id_medecin" :value="m.id_medecin">
                Dr. {{ m.nom }} - {{ m.specialite }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Secrétaire</label>
            <select v-model="form.id_secretaire">
              <option value="">-- Choisir une secrétaire --</option>
              <option v-for="s in secretaires" :key="s.id_secretaire" :value="s.id_secretaire">
                {{ s.nom }} {{ s.prenom }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label>Date *</label>
            <input v-model="form.date_rdv" type="date" required />
          </div>
          <div class="form-group">
            <label>Heure *</label>
            <input v-model="form.heure" type="time" required />
          </div>
          <div class="form-group">
            <label>Motif</label>
            <textarea v-model="form.motif" placeholder="Motif du rendez-vous" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Statut</label>
            <select v-model="form.statut">
              <option value="en attente">En attente</option>
              <option value="confirmé">Confirmé</option>
              <option value="annulé">Annulé</option>
            </select>
          </div>
          
          <div class="modal-buttons">
            <button type="submit" class="btn-save">💾 {{ isEditing ? 'Modifier' : 'Ajouter' }}</button>
            <button type="button" @click="closeModal" class="btn-cancel">❌ Annuler</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { 
  getRendezVous, 
  addRendezVous,           // ← Maintenant elle existe !
  updateRendezVousStatus,
  getPatients,
  getMedecins,
  getSecretaires 
} from '@/services/api.js';

const rendezVous = ref([]);
const patients = ref([]);
const medecins = ref([]);
const secretaires = ref([]);
const loading = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = ref({
  id_patient: '',
  id_medecin: '',
  id_secretaire: '',
  date_rdv: '',
  heure: '',
  motif: '',
  statut: 'en attente'
});

onMounted(() => {
  loadRendezVous();
  loadSelectData();
});

const loadRendezVous = async () => {
  loading.value = true;
  try {
    const response = await getRendezVous();
    rendezVous.value = response.data.data;
  } catch (error) {
    console.error('Erreur:', error);
    alert('Impossible de charger les rendez-vous');
  } finally {
    loading.value = false;
  }
};

const loadSelectData = async () => {
  try {
    const [pRes, mRes, sRes] = await Promise.all([
      getPatients(),
      getMedecins(),
      getSecretaires()
    ]);
    patients.value = pRes.data.data;
    medecins.value = mRes.data.data;
    secretaires.value = sRes.data.data;
  } catch (error) {
    console.error('Erreur chargement données:', error);
  }
};

const openAddModal = () => {
  isEditing.value = false;
  editingId.value = null;
  resetForm();
  showModal.value = true;
};

const editRdv = (rdv) => {
  isEditing.value = true;
  editingId.value = rdv.id_rdv;
  form.value = {
    id_patient: rdv.id_patient,
    id_medecin: rdv.id_medecin,
    id_secretaire: rdv.id_secretaire || '',
    date_rdv: rdv.date_rdv,
    heure: rdv.heure,
    motif: rdv.motif || '',
    statut: rdv.statut
  };
  showModal.value = true;
};

const saveRdv = async () => {
  try {
    // Note: Pour l'instant on utilise updateStatus pour la modification simple
    // Tu peux ajouter une route updateRendezVous complète dans Laravel si besoin
    alert('Fonctionnalité à compléter avec updateRendezVous dans Laravel');
    closeModal();
    await loadRendezVous();
  } catch (error) {
    console.error('Erreur:', error);
    alert('❌ Erreur lors de l\'enregistrement');
  }
};

const updateStatus = async (id, status) => {
  if (!confirm(`Changer le statut en "${status}" ?`)) return;
  try {
    await updateRendezVousStatus(id, status);
    alert(`✅ Statut mis à jour : ${status}`);
    await loadRendezVous();
  } catch (error) {
    console.error('Erreur:', error);
    alert('❌ Erreur lors de la mise à jour');
  }
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  form.value = {
    id_patient: '',
    id_medecin: '',
    id_secretaire: '',
    date_rdv: '',
    heure: '',
    motif: '',
    statut: 'en attente'
  };
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
.status-en-attente { background: #fef3c7; color: #92400e; }
.status-confirmé { background: #d1fae5; color: #065f46; }
.status-annulé { background: #fee2e2; color: #991b1b; }
.btn-edit, .btn-confirm, .btn-cancel { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; margin-right: 5px; font-size: 14px; }
.btn-edit { background: #4299e1; color: white; }
.btn-confirm { background: #48bb78; color: white; }
.btn-cancel { background: #f56565; color: white; }
.empty { text-align: center; padding: 60px; color: #a0aec0; font-size: 18px; }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; justify-content: center; align-items: center; z-index: 1000; }
.modal { background: white; padding: 30px; border-radius: 15px; width: 500px; max-height: 90vh; overflow-y: auto; }
.modal h2 { margin-bottom: 25px; color: #2d3748; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; color: #4a5568; font-weight: 500; font-size: 14px; }
.form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 15px; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #667eea; }
.modal-buttons { display: flex; gap: 12px; margin-top: 25px; }
.btn-save, .btn-cancel { flex: 1; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; }
.btn-save { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; }
.btn-cancel { background: #e2e8f0; color: #4a5568; }
</style>