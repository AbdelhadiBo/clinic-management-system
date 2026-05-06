<template>
  <div class="dashboard">
    <h1>🏥 Tableau de Bord - Admin</h1>
    <p class="subtitle">Bienvenue dans l'espace d'administration de la clinique</p>
    
    <div class="stats-grid">
      <div class="stat-card blue">
        <div class="icon">👥</div>
        <h3>Patients</h3>
        <p class="number">{{ stats.total_patients }}</p>
      </div>
      <div class="stat-card green">
        <div class="icon">👨‍⚕️</div>
        <h3>Médecins</h3>
        <p class="number">{{ stats.total_medecins }}</p>
      </div>
      <div class="stat-card orange">
        <div class="icon">👩‍💼</div>
        <h3>Secrétaires</h3>
        <p class="number">{{ stats.total_secretaires }}</p>
      </div>
      <div class="stat-card purple">
        <div class="icon">👩‍⚕️</div>
        <h3>Infirmiers</h3>
        <p class="number">{{ stats.total_infirmiers }}</p>
      </div>
      <div class="stat-card red">
        <div class="icon">📅</div>
        <h3>RDV Aujourd'hui</h3>
        <p class="number">{{ stats.rdv_aujourdhui }}</p>
      </div>
      <div class="stat-card yellow">
        <div class="icon">⏳</div>
        <h3>RDV en Attente</h3>
        <p class="number">{{ stats.rdv_en_attente }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getDashboardStats } from '@/services/api.js';

const stats = ref({
  total_patients: 0,
  total_medecins: 0,
  total_secretaires: 0,
  total_infirmiers: 0,
  rdv_aujourdhui: 0,
  rdv_en_attente: 0,
  total_factures: 0,
  montant_total_factures: 0
});

onMounted(async () => {
  try {
    const response = await getDashboardStats();
    stats.value = response.data.data;
  } catch (error) {
    console.error('Erreur dashboard:', error);
    alert('Impossible de charger les statistiques. Vérifie que Laravel est démarré !');
  }
});
</script>

<style scoped>
.dashboard { padding: 30px; max-width: 1200px; margin: 0 auto; }
h1 { color: #2c3e50; margin-bottom: 5px; }
.subtitle { color: #7f8c8d; margin-bottom: 30px; }
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px; margin-top: 20px; }
.stat-card { padding: 30px; border-radius: 15px; text-align: center; color: white; box-shadow: 0 8px 25px rgba(0,0,0,0.15); transition: transform 0.3s; }
.stat-card:hover { transform: translateY(-5px); }
.icon { font-size: 40px; margin-bottom: 10px; }
.stat-card h3 { margin: 0; font-size: 16px; opacity: 0.9; font-weight: 500; }
.number { font-size: 42px; font-weight: bold; margin: 15px 0 0 0; }
.blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.orange { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.purple { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.red { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.yellow { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333; }
</style>