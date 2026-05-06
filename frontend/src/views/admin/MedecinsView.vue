<template>
  <div class="page">
    <h1>👨‍⚕️ Gestion des Médecins</h1>
    
    <button @click="openAddModal" class="btn-add">+ Ajouter un Médecin</button>

    <div v-if="loading" class="loading">Chargement...</div>

    <table v-else-if="medecins.length > 0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Spécialité</th>
          <th>Téléphone</th>
          <th>Email</th>
          <th>Matricule</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="medecin in medecins" :key="medecin.id_medecin">
          <td>{{ medecin.id_medecin }}</td>
          <td>{{ medecin.nom }}</td>
          <td>{{ medecin.prenom }}</td>
          <td><span class="badge badge-specialite">{{ medecin.specialite }}</span></td>
          <td>{{ medecin.telephone }}</td>
          <td>{{ medecin.email || '-' }}</td>
          <td>{{ medecin.matricule }}</td>
          <td>
            <button @click="editMedecin(medecin)" class="btn-edit">✏️</button>
            <button @click="removeMedecin(medecin.id_medecin)" class="btn-delete">🗑️</button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-else class="empty">Aucun médecin enregistré</p>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h2>{{ isEditing ? '✏️ Modifier' : '➕ Ajouter' }} un Médecin</h2>
        <form @submit.prevent="saveMedecin">
          <div class="form-group">
            <label>Nom *</label>
            <input v-model="form.nom" placeholder="Nom" required />
          </div>
          <div class="form-group">
            <label>Prénom *</label>
            <input v-model="form.prenom" placeholder="Prénom" required />
          </div>
          <div class="form-group">
            <label>Spécialité *</label>
            <input v-model="form.specialite" placeholder="Cardiologie, etc." required />
          </div>
          <div class="form-group">
            <label>Téléphone *</label>
            <input v-model="form.telephone" placeholder="0555123456" required />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input v-model="form.email" placeholder="email@exemple.com" type="email" />
          </div>
          <div class="form-group">
            <label>Matricule *</label>
            <input v-model="form.matricule" placeholder="MAT-001" required />
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
import { getMedecins, addMedecin, updateMedecin, deleteMedecin } from '@/services/api.js';

const medecins = ref([]);
const loading = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = ref({
  nom: '',
  prenom: '',
  specialite: '',
  telephone: '',
  email: '',
  matricule: ''
});

onMounted(() => loadMedecins());

const loadMedecins = async () => {
  loading.value = true;
  try {
    const response = await getMedecins();
    medecins.value = response.data.data;
  } catch (error) {
    console.error('Erreur:', error);
    alert('Impossible de charger les médecins');
  } finally {
    loading.value = false;
  }
};

const openAddModal = () => {
  isEditing.value = false;
  editingId.value = null;
  resetForm();
  showModal.value = true;
};

const editMedecin = (medecin) => {
  isEditing.value = true;
  editingId.value = medecin.id_medecin;
  form.value = { ...medecin };
  showModal.value = true;
};

const saveMedecin = async () => {
  try {
    if (isEditing.value) {
      await updateMedecin(editingId.value, form.value);
      alert('✅ Médecin modifié !');
    } else {
      await addMedecin(form.value);
      alert('✅ Médecin ajouté !');
    }
    closeModal();
    await loadMedecins();
  } catch (error) {
    console.error('Erreur:', error);
    alert('❌ Erreur. Le matricule existe peut-être déjà.');
  }
};

const removeMedecin = async (id) => {
  if (!confirm('🗑️ Supprimer ce médecin ?')) return;
  try {
    await deleteMedecin(id);
    alert('✅ Médecin supprimé !');
    await loadMedecins();
  } catch (error) {
    alert('❌ Erreur lors de la suppression');
  }
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  form.value = { nom: '', prenom: '', specialite: '', telephone: '', email: '', matricule: '' };
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
.badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
.badge-specialite { background: #c6f6d5; color: #22543d; }
.btn-edit, .btn-delete { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; margin-right: 5px; font-size: 14px; }
.btn-edit { background: #4299e1; color: white; }
.btn-delete { background: #f56565; color: white; }
.empty { text-align: center; padding: 60px; color: #a0aec0; font-size: 18px; }
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: flex; justify-content: center; align-items: center; z-index: 1000; }
.modal { background: white; padding: 30px; border-radius: 15px; width: 500px; max-height: 90vh; overflow-y: auto; }
.modal h2 { margin-bottom: 25px; color: #2d3748; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; color: #4a5568; font-weight: 500; font-size: 14px; }
.form-group input, .form-group select { width: 100%; padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 15px; }
.form-group input:focus { outline: none; border-color: #667eea; }
.modal-buttons { display: flex; gap: 12px; margin-top: 25px; }
.btn-save, .btn-cancel { flex: 1; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; }
.btn-save { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; }
.btn-cancel { background: #e2e8f0; color: #4a5568; }
</style>