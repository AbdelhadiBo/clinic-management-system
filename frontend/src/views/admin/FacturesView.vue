<template>
  <div class="page">
    <h1>💰 Gestion des Factures</h1>
    
    <!-- Statistiques -->
    <div class="stats-bar">
      <div class="stat-box">
        <span class="stat-label">Total Factures</span>
        <span class="stat-value">{{ totalFactures }}</span>
      </div>
      <div class="stat-box">
        <span class="stat-label">Montant Total</span>
        <span class="stat-value">{{ montantTotal }} DA</span>
      </div>
      <div class="stat-box">
        <span class="stat-label">Payées</span>
        <span class="stat-value green">{{ facturesPayees }}</span>
      </div>
      <div class="stat-box">
        <span class="stat-label">Non Payées</span>
        <span class="stat-value red">{{ facturesNonPayees }}</span>
      </div>
    </div>

    <button @click="openAddModal" class="btn-add">+ Nouvelle Facture</button>

    <div v-if="loading" class="loading">Chargement...</div>

    <table v-else-if="factures.length > 0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Consultation</th>
          <th>Patient</th>
          <th>Date</th>
          <th>Montant</th>
          <th>Statut</th>
          <th>Mode Paiement</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="facture in factures" :key="facture.id_facture">
          <td>#{{ facture.id_facture }}</td>
          <td>#{{ facture.id_consultation }}</td>
          <td>
            {{ facture.consultation?.rendez_vous?.patient?.nom || '-' }} 
            {{ facture.consultation?.rendez_vous?.patient?.prenom || '' }}
          </td>
          <td>{{ facture.date }}</td>
          <td class="montant">{{ facture.montant_total }} DA</td>
          <td>
            <span :class="['badge', facture.statut_paiement === 'payé' ? 'status-paye' : 'status-non-paye']">
              {{ facture.statut_paiement }}
            </span>
          </td>
          <td>{{ facture.mode_paiement || '-' }}</td>
          <td>
            <button @click="editFacture(facture)" class="btn-edit">✏️</button>
            <button @click="toggleStatus(facture)" class="btn-toggle">
              {{ facture.statut_paiement === 'payé' ? '↩️' : '✓' }}
            </button>
            <button @click="removeFacture(facture.id_facture)" class="btn-delete">🗑️</button>
          </td>
        </tr>
      </tbody>
    </table>

    <p v-else class="empty">Aucune facture enregistrée</p>

    <!-- Modal Ajouter/Modifier -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <h2>{{ isEditing ? '✏️ Modifier' : '➕ Nouvelle' }} Facture</h2>
        <form @submit.prevent="saveFacture">
          <div class="form-group">
            <label>Consultation ID *</label>
            <input v-model="form.id_consultation" type="number" placeholder="ID de la consultation" required />
          </div>
          <div class="form-group">
            <label>Date *</label>
            <input v-model="form.date" type="date" required />
          </div>
          <div class="form-group">
            <label>Montant Total *</label>
            <input v-model="form.montant_total" type="number" step="0.01" placeholder="2500.00" required />
          </div>
          <div class="form-group">
            <label>Statut Paiement</label>
            <select v-model="form.statut_paiement">
              <option value="non payé">Non Payé</option>
              <option value="payé">Payé</option>
            </select>
          </div>
          <div class="form-group">
            <label>Mode Paiement</label>
            <select v-model="form.mode_paiement">
              <option value="">-- Choisir --</option>
              <option value="Espèces">Espèces</option>
              <option value="Carte bancaire">Carte bancaire</option>
              <option value="Chèque">Chèque</option>
              <option value="Virement">Virement</option>
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
import { ref, onMounted, computed } from 'vue';
import { getFactures, addFacture, updateFacture, deleteFacture as apiDeleteFacture } from '@/services/api.js';

const factures = ref([]);
const loading = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref(null);

const form = ref({
  id_consultation: '',
  date: '',
  montant_total: '',
  statut_paiement: 'non payé',
  mode_paiement: ''
});

onMounted(() => loadFactures());

const loadFactures = async () => {
  loading.value = true;
  try {
    const response = await getFactures();
    factures.value = response.data.data;
  } catch (error) {
    console.error('Erreur:', error);
    alert('Impossible de charger les factures');
  } finally {
    loading.value = false;
  }
};

// Statistiques calculées
const totalFactures = computed(() => factures.value.length);
const montantTotal = computed(() => {
  return factures.value.reduce((sum, f) => sum + parseFloat(f.montant_total || 0), 0).toFixed(2);
});
const facturesPayees = computed(() => {
  return factures.value.filter(f => f.statut_paiement === 'payé').length;
});
const facturesNonPayees = computed(() => {
  return factures.value.filter(f => f.statut_paiement !== 'payé').length;
});

const openAddModal = () => {
  isEditing.value = false;
  editingId.value = null;
  resetForm();
  showModal.value = true;
};

const editFacture = (facture) => {
  isEditing.value = true;
  editingId.value = facture.id_facture;
  form.value = {
    id_consultation: facture.id_consultation,
    date: facture.date,
    montant_total: facture.montant_total,
    statut_paiement: facture.statut_paiement,
    mode_paiement: facture.mode_paiement || ''
  };
  showModal.value = true;
};

const saveFacture = async () => {
  try {
    if (isEditing.value) {
      await updateFacture(editingId.value, form.value);
      alert('✅ Facture modifiée avec succès !');
    } else {
      await addFacture(form.value);
      alert('✅ Facture ajoutée avec succès !');
    }
    closeModal();
    await loadFactures();
  } catch (error) {
    console.error('Erreur:', error);
    alert('❌ Erreur lors de l\'enregistrement');
  }
};

const toggleStatus = async (facture) => {
  const nouveauStatut = facture.statut_paiement === 'payé' ? 'non payé' : 'payé';
  if (!confirm(`Changer le statut en "${nouveauStatut}" ?`)) return;
  
  try {
    await updateFacture(facture.id_facture, {
      statut_paiement: nouveauStatut,
      mode_paiement: facture.mode_paiement || 'Espèces'
    });
    alert(`✅ Statut changé en : ${nouveauStatut}`);
    await loadFactures();
  } catch (error) {
    console.error('Erreur:', error);
    alert('❌ Erreur lors de la mise à jour');
  }
};

const removeFacture = async (id) => {
  if (!confirm('🗑️ Supprimer cette facture ?')) return;
  try {
    await apiDeleteFacture(id);
    alert('✅ Facture supprimée !');
    await loadFactures();
  } catch (error) {
    alert('❌ Erreur lors de la suppression');
  }
};

const closeModal = () => {
  showModal.value = false;
  resetForm();
};

const resetForm = () => {
  form.value = {
    id_consultation: '',
    date: '',
    montant_total: '',
    statut_paiement: 'non payé',
    mode_paiement: ''
  };
};
</script>

<style scoped>
.page { padding: 30px; max-width: 1400px; margin: 0 auto; }
h1 { color: #2c3e50; margin-bottom: 20px; }

.stats-bar {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-bottom: 30px;
}

.stat-box {
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
  text-align: center;
}

.stat-label {
  display: block;
  color: #718096;
  font-size: 14px;
  margin-bottom: 8px;
}

.stat-value {
  display: block;
  font-size: 28px;
  font-weight: bold;
  color: #2d3748;
}

.stat-value.green { color: #48bb78; }
.stat-value.red { color: #f56565; }

.btn-add {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 12px 25px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  margin-bottom: 20px;
}

.loading { text-align: center; padding: 50px; color: #7f8c8d; font-size: 18px; }

table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
th { background: #4a5568; color: white; padding: 15px; text-align: left; }
td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; }
tr:hover { background: #f7fafc; }

.montant {
  font-weight: bold;
  color: #2d3748;
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

.status-non-paye {
  background: #fee2e2;
  color: #991b1b;
}

.btn-edit, .btn-toggle, .btn-delete {
  padding: 6px 12px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  margin-right: 5px;
  font-size: 14px;
}

.btn-edit { background: #4299e1; color: white; }
.btn-toggle { background: #48bb78; color: white; }
.btn-delete { background: #f56565; color: white; }

.empty {
  text-align: center;
  padding: 60px;
  color: #a0aec0;
  font-size: 18px;
}

.modal-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal {
  background: white;
  padding: 30px;
  border-radius: 15px;
  width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal h2 { margin-bottom: 25px; color: #2d3748; }

.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; color: #4a5568; font-weight: 500; font-size: 14px; }
.form-group input, .form-group select { width: 100%; padding: 10px 12px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 15px; }
.form-group input:focus, .form-group select:focus { outline: none; border-color: #667eea; }

.modal-buttons { display: flex; gap: 12px; margin-top: 25px; }
.btn-save, .btn-cancel { flex: 1; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; }
.btn-save { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; }
.btn-cancel { background: #e2e8f0; color: #4a5568; }

@media (max-width: 768px) {
  .stats-bar { grid-template-columns: repeat(2, 1fr); }
}
</style>