<template>
  <div class="page">
    <h1>👥 Gestion des Patients</h1>
    
    <button @click="openAddModal" class="btn-add">+ Ajouter un Patient</button>

    <div v-if="loading" class="loading">Chargement...</div>

    <table v-else-if="patients.length > 0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Date Naissance</th>
          <th>Sexe</th>
          <th>Téléphone</th>
          <th>Email</th>
          <th>Groupe Sanguin</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="patient in patients" :key="patient.id_patient">
          <td>{{ patient.id_patient }}</td>
          <td>{{ patient.nom }}</td>
          <td>{{ patient.prenom }}</td>
          <td>{{ patient.date_naissance }}</td>
          <td>{{ patient.sexe }}</td>
          <td>{{ patient.telephone }}</td>
          <td>{{ patient.email || '-' }}</td>
          <td><span class="badge">{{ patient.groupe_sanguin || '-' }}</span></td>
          <td>
            <button @click="editPatient(patient)" class="btn-edit">✏️</button>
            <button @click="removePatient(patient.id_patient)" class="btn-delete">🗑️</button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-else class="empty">Aucun patient enregistré</p>

    <!-- Modal -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h2>{{ isEditing ? '✏️ Modifier' : '➕ Ajouter' }} un Patient</h2>
        <form @submit.prevent="savePatient">
          <div class="form-group">
            <label>Nom *</label>
            <input v-model="form.nom" placeholder="Nom" required />
          </div>
          <div class="form-group">
            <label>Prénom *</label>
            <input v-model="form.prenom" placeholder="Prénom" required />
          </div>
          <div class="form-group">
            <label>Date de Naissance *</label>
            <input v-model="form.date_naissance" type="date" required />
          </div>
          <div class="form-group">
            <label>Sexe *</label>
            <select v-model="form.sexe" required>
              <option value="Homme">Homme</option>
              <option value="Femme">Femme</option>
            </select>
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
            <label>Adresse</label>
            <input v-model="form.adresse" placeholder="Adresse complète" />
          </div>
          <div class="form-group">
            <label>Groupe Sanguin</label>
            <input v-model="form.groupe_sanguin" placeholder="A+, O-, etc." />
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
import { getPatients, addPatient, updatePatient, deletePatient as apiDeletePatient } from '@/services/api.js';

const patients = ref([]);
const loading = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = ref({
  nom: '', prenom: '', date_naissance: '', sexe: 'Homme',
  telephone: '', email: '', adresse: '', groupe_sanguin: ''
});

onMounted(() => loadPatients());

const loadPatients = async () => {
  loading.value = true;
  try {
    const response = await getPatients();
    patients.value = response.data.data;
  } catch (error) {
    console.error('Erreur:', error);
    alert('Impossible de charger les patients. Vérifie que Laravel est démarré sur localhost:8000 !');
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

const editPatient = (patient) => {
  isEditing.value = true;
  editingId.value = patient.id_patient;
  form.value = { ...patient };
  showModal.value = true;
};

const savePatient = async () => {
  try {
    if (isEditing.value) {
      await updatePatient(editingId.value, form.value);
      alert('✅ Patient modifié avec succès !');
    } else {
      await addPatient(form.value);
      alert('✅ Patient ajouté avec succès !');
    }
    closeModal();
    await loadPatients();
  } catch (error) {
    console.error('Erreur:', error);
    alert('❌ Erreur lors de l\'enregistrement');
  }
};

const removePatient = async (id) => {
  if (!confirm('🗑️ Supprimer ce patient ?')) return;
  try {
    await apiDeletePatient(id);
    alert('✅ Patient supprimé !');
    await loadPatients();
  } catch (error) {
    alert('❌ Erreur lors de la suppression');
  }
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  form.value = { nom: '', prenom: '', date_naissance: '', sexe: 'Homme', telephone: '', email: '', adresse: '', groupe_sanguin: '' };
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
.badge { background: #e53e3e; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; }
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