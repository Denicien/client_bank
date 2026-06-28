import axios from 'axios';

// ✅ Utiliser l'URL complète du backend
const API_BASE_URL = 'http://localhost:8000';

const api = axios.create({
    baseURL: API_BASE_URL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    timeout: 10000
});

export const clientService = {
    // Récupérer tous les clients
    getAll: () => api.get('/'),
    
    // Récupérer un client par ID
    getById: (id) => api.get(`/?id=${id}`),
    
    // Ajouter un client
    add: (data) => api.post('/', data),
    
    // Modifier un client
    update: (id, data) => api.put(`/?id=${id}`, data),
    
    // Supprimer un client
    delete: (id) => api.delete(`/?id=${id}`),
    
    // Récupérer les statistiques
    getStats: () => api.get('/?stats=1')
};