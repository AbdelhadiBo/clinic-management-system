<template>
  <div class="app">
    <nav class="sidebar">
      <div class="logo">🏥 Clinique</div>
      <ul>
        <li><router-link to="/">🏠 Accueil</router-link></li>
        <li><router-link to="/dashboard">📊 Dashboard</router-link></li>
        <li><router-link to="/patients">👥 Patients</router-link></li>
        <li><router-link to="/medecins">👨‍⚕️ Médecins</router-link></li>
        <li><router-link to="/secretaires">👩‍💼 Secrétaires</router-link></li>
        <li><router-link to="/infirmiers">👩‍⚕️ Infirmiers</router-link></li>
        <li><router-link to="/rendez-vous">📅 Rendez-vous</router-link></li>
        <li><router-link to="/consultations">📋 Consultations</router-link></li>
        <li><router-link to="/factures">💰 Factures</router-link></li>
      </ul>
    </nav>
    
    <main class="content">
      <router-view />
    </main>
  </div>
</template>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; }
.app { display: flex; min-height: 100vh; }
.sidebar { width: 260px; background: #2d3748; color: white; padding: 25px 0; position: fixed; height: 100vh; }
.logo { font-size: 24px; font-weight: bold; padding: 0 25px 30px; border-bottom: 1px solid #4a5568; margin-bottom: 20px; }
.sidebar ul { list-style: none; }
.sidebar li { margin: 5px 0; }
.sidebar a { display: block; padding: 15px 25px; color: #a0aec0; text-decoration: none; transition: all 0.3s; font-size: 16px; }
.sidebar a:hover, .sidebar a.router-link-active { background: #4a5568; color: white; border-left: 4px solid #667eea; }
.content { margin-left: 260px; flex: 1; min-height: 100vh; }
</style>