<template>
    <div class="add-client">
        <h2>➕ Ajouter un client</h2>
        
        <form @submit.prevent="handleSubmit" class="form-client">
            <div class="form-group">
                <label>Numéro de compte</label>
                <input 
                    v-model="form.numCompte" 
                    type="text" 
                    placeholder="Ex: C006" 
                    required
                >
            </div>
            
            <div class="form-group">
                <label>Nom du client</label>
                <input 
                    v-model="form.nom" 
                    type="text" 
                    placeholder="Ex: Jean Dupont" 
                    required
                >
            </div>
            
            <div class="form-group">
                <label>Solde (€)</label>
                <input 
                    v-model.number="form.solde" 
                    type="number" 
                    step="0.01" 
                    placeholder="Ex: 1500.00" 
                    required
                >
            </div>
            
            <button type="submit" class="btn-submit">
                {{ isLoading ? 'Ajout en cours...' : 'Ajouter le client' }}
            </button>
        </form>
        
        <div v-if="message" :class="['message', messageType]">
            {{ message }}
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { clientService } from '../services/api';

const form = reactive({
    numCompte: '',
    nom: '',
    solde: 0
});

const message = ref('');
const messageType = ref('');
const isLoading = ref(false);

const handleSubmit = async () => {
    isLoading.value = true;
    message.value = '';
    
    try {
        const response = await clientService.add(form);
        
        if (response.data.success) {
            message.value = '✅ ' + response.data.message;
            messageType.value = 'success';
            
            // Réinitialiser le formulaire
            form.numCompte = '';
            form.nom = '';
            form.solde = 0;
        } else {
            message.value = '❌ ' + response.data.message;
            messageType.value = 'error';
        }
    } catch (error) {
        message.value = '❌ Erreur de connexion au serveur';
        messageType.value = 'error';
    } finally {
        isLoading.value = false;
        setTimeout(() => { message.value = ''; }, 5000);
    }
};
</script>

<style scoped>
.add-client {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

h2 {
    color: #2c3e50;
    margin-bottom: 30px;
}

.form-client {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: #3498db;
}

.btn-submit {
    width: 100%;
    padding: 14px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-submit:hover {
    background: #2980b9;
}

.btn-submit:disabled {
    background: #95a5a6;
    cursor: not-allowed;
}

.message {
    margin-top: 20px;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
}

.message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>