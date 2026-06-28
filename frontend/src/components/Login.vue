<template>
    <div class="login-container">
        <div class="login-box">
            <h2>🏦 Gestion Bancaire</h2>
            <h3>Connexion</h3>
            
            <form @submit.prevent="login">
                <div class="form-group">
                    <label>Identifiant</label>
                    <input 
                        v-model="username" 
                        type="text" 
                        placeholder="admin" 
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input 
                        v-model="password" 
                        type="password" 
                        placeholder="admin" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn-login">Se connecter</button>
                
                <p v-if="error" class="error-message">{{ error }}</p>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const username = ref('');
const password = ref('');
const error = ref('');
const router = useRouter();

const login = () => {
    // Authentification simple (à améliorer avec JWT plus tard)
    if (username.value === 'admin' && password.value === 'admin') {
        localStorage.setItem('auth', 'true');
        localStorage.setItem('user', username.value);
        router.push('/clients');
    } else {
        error.value = '❌ Identifiants incorrects';
        setTimeout(() => { error.value = ''; }, 3000);
    }
};
</script>

<style scoped>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-box {
    background: white;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 400px;
}

.login-box h2 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 10px;
}

.login-box h3 {
    text-align: center;
    color: #7f8c8d;
    font-weight: normal;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    color: #2c3e50;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 12px;
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: #667eea;
}

.btn-login {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: transform 0.2s;
}

.btn-login:hover {
    transform: translateY(-2px);
}

.error-message {
    margin-top: 15px;
    color: #e74c3c;
    text-align: center;
    font-weight: bold;
}
</style>