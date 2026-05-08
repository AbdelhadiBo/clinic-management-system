<template>
  <div class="accueil">
    <div class="hero">
      <h1>🏥 Clinique Médicale</h1>
      <p>Gestion complète de votre clinique</p>
      <router-link to="/dashboard" class="btn-dashboard">
        Accéder au Dashboard
      </router-link>
    </div>

    <div class="features">
      <div class="feature-card">
        <div class="icon">👥</div>
        <h3>Patients</h3>
        <p>Gérez les dossiers patients</p>
      </div>
      <div class="feature-card">
        <div class="icon">👨‍⚕️</div>
        <h3>Médecins</h3>
        <p>Gérez le personnel médical</p>
      </div>
      <div class="feature-card">
        <div class="icon">📅</div>
        <h3>Rendez-vous</h3>
        <p>Planifiez les consultations</p>
      </div>
      <div class="feature-card">
        <div class="icon">💰</div>
        <h3>Factures</h3>
        <p>Gérez les paiements</p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.accueil {
  min-height: 100vh;
  background: #f5f7fa;
}

.hero {
  text-align: center;
  padding: 80px 20px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.hero h1 {
  font-size: 48px;
  margin-bottom: 15px;
}

.hero p {
  font-size: 20px;
  margin-bottom: 30px;
  opacity: 0.9;
}

.btn-dashboard {
  display: inline-block;
  background: white;
  color: #667eea;
  padding: 15px 40px;
  border-radius: 30px;
  text-decoration: none;
  font-weight: bold;
  font-size: 16px;
  transition: transform 0.3s, box-shadow 0.3s;
}

.btn-dashboard:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.features {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 30px;
  padding: 60px 40px;
  max-width: 1200px;
  margin: 0 auto;
}

.feature-card {
  background: white;
  padding: 40px 25px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  transition: transform 0.3s;
}

.feature-card:hover {
  transform: translateY(-5px);
}

.feature-card .icon {
  font-size: 50px;
  margin-bottom: 15px;
}

.feature-card h3 {
  color: #2d3748;
  margin-bottom: 10px;
  font-size: 20px;
}

.feature-card p {
  color: #718096;
  font-size: 14px;
}

@media (max-width: 768px) {
  .features {
    grid-template-columns: repeat(2, 1fr);
    padding: 40px 20px;
  }
  .hero h1 {
    font-size: 32px;
  }
}
</style>