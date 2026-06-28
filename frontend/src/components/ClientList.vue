<template>
    <div class="client-list-container">
        <div class="page-header">
            <h2>📋 Liste des clients</h2>
            <div class="header-actions">
                <span class="client-count">Total : {{ clients.length }} clients</span>
            </div>
        </div>

        <div v-if="isLoading" class="loading-state">
            <div class="spinner"></div>
            <p>Chargement des clients...</p>
        </div>

        <div v-else-if="clients.length === 0" class="empty-state">
            <p>📭 Aucun client enregistré</p>
            <router-link to="/add" class="btn-add-first">➕ Ajouter un client</router-link>
        </div>

        <div v-else>
            <!-- Version Desktop -->
            <div class="table-wrapper desktop-view">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>N° Compte</th>
                            <th>Nom du client</th>
                            <th>Solde</th>
                            <th>Observation</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="client in clients" :key="client.id">
                            <td><span class="compte-badge">#{{ client.numCompte }}</span></td>
                            <td>
                                <div class="client-info">
                                    <div class="avatar" :style="{ background: getColor(client.nom) }">
                                        {{ getInitials(client.nom) }}
                                    </div>
                                    <span class="client-name">{{ client.nom }}</span>
                                </div>
                            </td>
                            <td><span class="solde-value">{{ Number(client.solde).toFixed(2) }} €</span></td>
                            <td>
                                <span :class="['observation-badge', getObservationClass(client.solde)]">
                                    {{ getObservation(client.solde) }}
                                </span>
                            </td>
                            <td class="actions-cell">
                                <button @click="editClient(client)" class="btn-edit" title="Modifier">✏️</button>
                                <button @click="deleteClient(client.id)" class="btn-delete" title="Supprimer">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Version Mobile : Cartes -->
            <div class="mobile-view">
                <div v-for="client in clients" :key="client.id" class="client-card">
                    <div class="card-header">
                        <div class="card-left">
                            <div class="avatar" :style="{ background: getColor(client.nom), width: '48px', height: '48px', fontSize: '18px' }">
                                {{ getInitials(client.nom) }}
                            </div>
                            <div class="card-name">
                                <span class="client-name-mobile">{{ client.nom }}</span>
                                <span class="compte-badge-mobile">Compte #{{ client.numCompte }}</span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <button @click="editClient(client)" class="btn-edit-mobile">✏️</button>
                            <button @click="deleteClient(client.id)" class="btn-delete-mobile">🗑️</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-row">
                            <span class="card-label">💰 Solde</span>
                            <span class="solde-value-mobile">{{ Number(client.solde).toFixed(2) }} €</span>
                        </div>
                        <div class="card-row">
                            <span class="card-label">📊 Observation</span>
                            <span :class="['observation-badge-mobile', getObservationClass(client.solde)]">
                                {{ getObservation(client.solde) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="editing" class="modal-overlay" @click.self="closeEdit">
            <div class="modal-card">
                <div class="modal-header">
                    <h3>✏️ Modifier le client</h3>
                    <button @click="closeEdit" class="modal-close">✕</button>
                </div>
                <form @submit.prevent="updateClient" class="modal-form">
                    <div class="form-group">
                        <label>Numéro de compte</label>
                        <input v-model="editForm.numCompte" type="text" required>
                    </div>
                    <div class="form-group">
                        <label>Nom du client</label>
                        <input v-model="editForm.nom" type="text" required>
                    </div>
                    <div class="form-group">
                        <label>Solde (€)</label>
                        <input v-model.number="editForm.solde" type="number" step="0.01" required>
                    </div>
                    <div class="modal-actions">
                        <button type="submit" class="btn-save">💾 Sauvegarder</button>
                        <button type="button" @click="closeEdit" class="btn-cancel">❌ Annuler</button>
                    </div>
                </form>
                <p v-if="editMessage" :class="['message', editMessageType]">{{ editMessage }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { clientService } from '../services/api';

const clients = ref([]);
const isLoading = ref(false);
const editing = ref(false);
const editForm = ref({});
const editMessage = ref('');
const editMessageType = ref('');

const loadClients = async () => {
    isLoading.value = true;
    try {
        const response = await clientService.getAll();
        clients.value = response.data || [];
    } catch (error) {
        console.error('Erreur:', error);
        alert('❌ Erreur de connexion au serveur');
    } finally {
        isLoading.value = false;
    }
};

const getObservation = (solde) => {
    const montant = Number(solde);
    if (montant < 1000) return 'Insuffisant';
    if (montant <= 5000) return 'Moyen';
    return 'Élevé';
};

const getObservationClass = (solde) => {
    const montant = Number(solde);
    if (montant < 1000) return 'insuffisant';
    if (montant <= 5000) return 'moyen';
    return 'eleve';
};

const getInitials = (nom) => {
    if (!nom) return '?';
    const parts = nom.trim().split(' ');
    if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
    return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
};

const getColor = (nom) => {
    const colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b', '#fa709a', '#30cfd0', '#a8edea', '#fbc2eb', '#a6c1ee', '#fddb92', '#d4fc79', '#f6d365', '#ff6b6b', '#48dbfb', '#ff9ff3'];
    let hash = 0;
    for (let i = 0; i < nom.length; i++) {
        hash = nom.charCodeAt(i) + ((hash << 5) - hash);
    }
    return colors[Math.abs(hash) % colors.length];
};

const deleteClient = async (id) => {
    const client = clients.value.find(c => c.id === id);
    if (confirm(`⚠️ Supprimer "${client.nom}" ?`)) {
        try {
            const response = await clientService.delete(id);
            alert(response.data.message);
            loadClients();
        } catch (error) {
            alert('❌ Erreur lors de la suppression');
        }
    }
};

const editClient = (client) => {
    editing.value = true;
    editForm.value = { ...client };
    editMessage.value = '';
};

const closeEdit = () => {
    editing.value = false;
    editForm.value = {};
    editMessage.value = '';
};

const updateClient = async () => {
    try {
        const response = await clientService.update(editForm.value.id, editForm.value);
        if (response.data.success) {
            editMessage.value = '✅ ' + response.data.message;
            editMessageType.value = 'success';
            setTimeout(() => { closeEdit(); loadClients(); }, 1500);
        } else {
            editMessage.value = '❌ ' + response.data.message;
            editMessageType.value = 'error';
        }
    } catch (error) {
        editMessage.value = '❌ Erreur de connexion';
        editMessageType.value = 'error';
    }
};

onMounted(loadClients);
</script>

<style scoped>
.client-list-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 10px;
}

.page-header h2 {
    color: #1f2937;
    font-size: 24px;
    margin: 0;
}

.client-count {
    background: #e5e7eb;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 14px;
    color: #374151;
    font-weight: 500;
}

.loading-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.spinner {
    border: 4px solid #e5e7eb;
    border-top: 4px solid #3b82f6;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #f9fafb;
    border-radius: 12px;
}

.empty-state p {
    font-size: 18px;
    color: #6b7280;
    margin-bottom: 20px;
}

.btn-add-first {
    display: inline-block;
    padding: 12px 24px;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.3s;
}

.btn-add-first:hover {
    background: #2563eb;
}

/* ===== DESKTOP ===== */
.table-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.table-modern {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px;
}

.table-modern thead {
    background: #f8fafc;
    border-bottom: 2px solid #e5e7eb;
}

.table-modern th {
    padding: 14px 16px;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-modern td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: middle;
}

.table-modern tbody tr:hover {
    background: #f9fafb;
}

.table-modern tbody tr:last-child td {
    border-bottom: none;
}

.compte-badge {
    display: inline-block;
    background: #eff6ff;
    color: #1e40af;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    font-family: monospace;
}

.client-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: white;
    font-weight: 600;
    font-size: 13px;
    flex-shrink: 0;
    font-family: monospace;
}

.client-name {
    font-weight: 500;
    color: #1f2937;
    font-size: 14px;
}

.solde-value {
    font-weight: 600;
    color: #1f2937;
    font-size: 15px;
}

.observation-badge {
    display: inline-block;
    padding: 4px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}

.observation-badge.insuffisant { background: #fee2e2; color: #991b1b; }
.observation-badge.moyen { background: #fef3c7; color: #92400e; }
.observation-badge.eleve { background: #d1fae5; color: #065f46; }

.actions-cell {
    text-align: center;
    white-space: nowrap;
}

.btn-edit, .btn-delete {
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
    margin: 0 3px;
}

.btn-edit { background: #eff6ff; color: #1e40af; }
.btn-edit:hover { background: #dbeafe; transform: scale(1.05); }
.btn-delete { background: #fef2f2; color: #991b1b; }
.btn-delete:hover { background: #fecaca; transform: scale(1.05); }

.text-center { text-align: center; }

/* ===== MOBILE ===== */
.mobile-view {
    display: none;
    flex-direction: column;
    gap: 16px;
}

.client-card {
    background: white;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.card-left {
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
}

.card-name {
    display: flex;
    flex-direction: column;
}

.client-name-mobile {
    font-weight: 600;
    font-size: 16px;
    color: #1f2937;
}

.compte-badge-mobile {
    font-size: 12px;
    color: #6b7280;
    font-family: monospace;
}

.card-actions {
    display: flex;
    gap: 8px;
}

.btn-edit-mobile, .btn-delete-mobile {
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.2s;
}

.btn-edit-mobile { background: #eff6ff; color: #1e40af; }
.btn-edit-mobile:hover { background: #dbeafe; }
.btn-delete-mobile { background: #fef2f2; color: #991b1b; }
.btn-delete-mobile:hover { background: #fecaca; }

.card-body {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding-top: 12px;
    border-top: 1px solid #f3f4f6;
}

.card-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-label {
    color: #6b7280;
    font-size: 14px;
    font-weight: 500;
}

.solde-value-mobile {
    font-weight: 600;
    color: #1f2937;
    font-size: 15px;
}

.observation-badge-mobile {
    display: inline-block;
    padding: 4px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.observation-badge-mobile.insuffisant { background: #fee2e2; color: #991b1b; }
.observation-badge-mobile.moyen { background: #fef3c7; color: #92400e; }
.observation-badge-mobile.eleve { background: #d1fae5; color: #065f46; }

/* ===== MODAL ===== */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    animation: fadeIn 0.3s ease;
    padding: 20px;
}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.modal-card {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    animation: slideUp 0.3s ease;
    max-height: 90vh;
    overflow-y: auto;
}

@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 { margin: 0; color: #1f2937; font-size: 18px; }

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #9ca3af;
    transition: color 0.2s;
}
.modal-close:hover { color: #374151; }

.modal-form { padding: 24px; }

.form-group { margin-bottom: 18px; }
.form-group label { display: block; margin-bottom: 6px; font-weight: 500; color: #374151; font-size: 14px; }
.form-group input {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}
.form-group input:focus { outline: none; border-color: #3b82f6; }

.modal-actions { display: flex; gap: 10px; margin-top: 20px; }
.btn-save { flex: 1; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
.btn-save:hover { background: #2563eb; }
.btn-cancel { flex: 1; padding: 12px; background: #f3f4f6; color: #374151; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
.btn-cancel:hover { background: #e5e7eb; }

.message { margin: 0 24px 24px; padding: 12px; border-radius: 8px; text-align: center; font-weight: 500; }
.message.success { background: #d1fae5; color: #065f46; }
.message.error { background: #fee2e2; color: #991b1b; }

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .desktop-view { display: none; }
    .mobile-view { display: flex; }
    .page-header h2 { font-size: 20px; }
}

@media (max-width: 480px) {
    .client-list-container { padding: 10px; }
    .page-header { flex-direction: column; align-items: flex-start; gap: 8px; }
    .client-card { padding: 12px; }
    .card-header { flex-wrap: wrap; gap: 10px; }
    .card-actions { width: 100%; justify-content: flex-end; }
    .modal-card { margin: 10px; }
}
</style>