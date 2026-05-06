<template>
  <div class="app">
    <nav class="sidebar">
      <div class="logo">🏥 Clinique</div>
      <ul>
        <li><router-link to="/dashboard">📊 Tableau de Bord</router-link></li>
        <li><router-link to="/patients">👥 Patients</router-link></li>
        <li><router-link to="/medecins">👨‍⚕️ Médecins</router-link></li>
        <li><router-link to="/secretaires">👩‍💼 Secrétaires</router-link></li>
        <li><router-link to="/infirmiers">👩‍⚕️ Infirmiers</router-link></li>
      </ul>
    </nav>
    
    <main class="content">
      <router-view />  <!-- ← C'EST ICI QUE LES PAGES S'AFFICHENT ! -->
    </main>
  </div>
</template>

<style>
/* ... tes styles ... */
</style>