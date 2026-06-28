<template>
    <div id="app">
      <!-- Navigation -->
      <nav v-if="isAuthenticated" class="navbar">
        <div class="nav-brand">
          <span class="brand-icon">🏦</span>
          <span class="brand-name">Gestion Bancaire</span>
        </div>
        
        <div class="nav-links">
          <router-link to="/add" class="nav-link">
            <span>➕</span> Ajouter
          </router-link>
          <router-link to="/clients" class="nav-link">
            <span>📋</span> Liste
          </router-link>
          <router-link to="/dashboard" class="nav-link">
            <span>📊</span> Bilan
          </router-link>
        </div>
        
        <div class="nav-user">
          <span class="user-name">{{ user }}</span>
          <button @click="logout" class="btn-logout">🚪 Déconnexion</button>
        </div>
      </nav>
  
      <!-- Contenu principal -->
      <main class="main-content">
        <router-view />
      </main>
    </div>
  </template>
  
  <script>
  export default {
    name: 'App',
    data() {
      return {
        isAuthenticated: false,
        user: ''
      }
    },
    methods: {
      checkAuth() {
        const auth = localStorage.getItem('auth')
        this.isAuthenticated = auth === 'true'
        this.user = localStorage.getItem('user') || 'Admin'
      },
      logout() {
        localStorage.removeItem('auth')
        localStorage.removeItem('user')
        this.isAuthenticated = false
        this.$router.push('/login')
      }
    },
    mounted() {
      this.checkAuth()
    },
    watch: {
      '$route'() {
        this.checkAuth()
      }
    }
  }
  </script>
  
  <style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
    background: #f3f4f6;
  }
  
  #app {
    min-height: 100vh;
  }
  
  /* Navbar */
  .navbar {
    background: #1f2937;
    color: white;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
  }
  
  .nav-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 20px;
    font-weight: bold;
  }
  
  .brand-icon {
    font-size: 28px;
  }
  
  .brand-name {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  .nav-links {
    display: flex;
    gap: 20px;
  }
  
  .nav-link {
    color: #d1d5db;
    text-decoration: none;
    padding: 8px 16px;
    border-radius: 6px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  
  .nav-link:hover {
    color: white;
    background: rgba(255,255,255,0.1);
  }
  
  .nav-link.router-link-active {
    color: white;
    background: rgba(255,255,255,0.15);
  }
  
  .nav-user {
    display: flex;
    align-items: center;
    gap: 15px;
  }
  
  .user-name {
    color: #d1d5db;
    font-weight: 500;
  }
  
  .btn-logout {
    padding: 6px 16px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.3s;
  }
  
  .btn-logout:hover {
    background: #dc2626;
  }
  
  .main-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  </style>