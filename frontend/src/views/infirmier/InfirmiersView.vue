<template>
  <div class="p-8">
    <div class="flex items-center mb-8">
      <span class="text-4xl mr-4">🏥</span>
      <h1 class="text-3xl font-bold text-gray-800">Consultations du jour (Infirmier)</h1>
    </div>

    <div v-if="loading" class="text-teal-600 font-bold text-lg animate-pulse">
      Chargement des patients...
    </div>

    <div v-else class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-teal-600">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Heure</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Patient</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Motif</th>
            <th class="px-6 py-4 text-left text-xs font-bold text-white uppercase tracking-wider">Statut</th>
            <th class="px-6 py-4 text-center text-xs font-bold text-white uppercase tracking-wider">Action</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="consultations.length === 0">
            <td colspan="5" class="px-6 py-8 text-center text-gray-500 font-medium">Aucune consultation prévue aujourd'hui.</td>
          </tr>
          <tr v-for="rdv in consultations" :key="rdv.id_rdv" class="hover:bg-teal-50 transition duration-150">
            <td class="px-6 py-4 font-bold text-gray-800">{{ rdv.heure }}</td>
            <td class="px-6 py-4 font-medium text-gray-900">{{ rdv.nom }} {{ rdv.prenom }}</td>
            <td class="px-6 py-4 text-gray-600">{{ rdv.motif }}</td>
            <td class="px-6 py-4">
              <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                {{ rdv.statut }}
              </span>
            </td>
            <td class="px-6 py-4 text-center">
              <button class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm">
                Saisir Constantes
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const consultations = ref([]);
const loading = ref(true);

const fetchConsultations = async () => {
  try {
    const response = await fetch('http://127.0.0.1:8000/api/infirmier/consultations');
    const data = await response.json();
    if (data.success) {
      consultations.value = data.consultations;
    }
  } catch (error) {
    console.error("Erreur :", error);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchConsultations();
});
</script>