<template>
    <div class="dashboard">
        <h2>📊 Bilan financier</h2>
        
        <div v-if="isLoading" class="loading">Chargement des données...</div>
        
        <div v-else>
            <!-- Statistiques -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <div class="stat-label">Solde total</div>
                        <div class="stat-value">{{ stats.total ? Number(stats.total).toFixed(2) : '0.00' }} €</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⬇️</div>
                    <div class="stat-info">
                        <div class="stat-label">Solde minimum</div>
                        <div class="stat-value">{{ stats.min ? Number(stats.min).toFixed(2) : '0.00' }} €</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⬆️</div>
                    <div class="stat-info">
                        <div class="stat-label">Solde maximum</div>
                        <div class="stat-value">{{ stats.max ? Number(stats.max).toFixed(2) : '0.00' }} €</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <div class="stat-label">Nombre de clients</div>
                        <div class="stat-value">{{ stats.count || 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Graphiques -->
            <div class="charts-grid">
                <div class="chart-box">
                    <h3>Répartition des observations</h3>
                    <canvas ref="pieChart"></canvas>
                </div>
                <div class="chart-box">
                    <h3>Comparaison des soldes</h3>
                    <canvas ref="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { clientService } from '../services/api';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const stats = ref({});
const clients = ref([]);
const isLoading = ref(false);
const pieChart = ref(null);
const barChart = ref(null);

const loadData = async () => {
    isLoading.value = true;
    try {
        // Charger les statistiques
        const statsResponse = await clientService.getStats();
        stats.value = statsResponse.data;

        // Charger la liste des clients pour les graphiques
        const clientsResponse = await clientService.getAll();
        clients.value = clientsResponse.data;

        // Attendre que le DOM soit prêt pour les canvas
        await nextTick();
        createCharts();
    } catch (error) {
        console.error('Erreur de chargement:', error);
    } finally {
        isLoading.value = false;
    }
};

const createCharts = () => {
    // Préparer les données pour les graphiques
    const categories = {
        insuffisant: 0,
        moyen: 0,
        eleve: 0
    };

    clients.value.forEach(client => {
        const solde = Number(client.solde);
        if (solde < 1000) categories.insuffisant++;
        else if (solde <= 5000) categories.moyen++;
        else categories.eleve++;
    });

    // Graphique Camembert (Pie Chart)
    const ctxPie = document.querySelector('[ref="pieChart"]');
    if (ctxPie) {
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Insuffisant (<1000€)', 'Moyen (1000-5000€)', 'Élevé (>5000€)'],
                datasets: [{
                    data: [categories.insuffisant, categories.moyen, categories.eleve],
                    backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Graphique Barres (Bar Chart)
    const ctxBar = document.querySelector('[ref="barChart"]');
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: clients.value.map(c => c.nom),
                datasets: [{
                    label: 'Solde (€)',
                    data: clients.value.map(c => Number(c.solde)),
                    backgroundColor: clients.value.map(c => {
                        const solde = Number(c.solde);
                        if (solde < 1000) return '#ef4444';
                        if (solde <= 5000) return '#f59e0b';
                        return '#10b981';
                    }),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' €';
                            }
                        }
                    }
                }
            }
        });
    }
};

onMounted(loadData);
</script>

<style scoped>
.dashboard {
    padding: 20px;
}

h2 {
    color: #2c3e50;
    margin-bottom: 30px;
}

.loading {
    text-align: center;
    padding: 40px;
    color: #7f8c8d;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 32px;
}

.stat-info {
    flex: 1;
}

.stat-label {
    color: #6b7280;
    font-size: 14px;
    font-weight: 500;
}

.stat-value {
    color: #1f2937;
    font-size: 24px;
    font-weight: bold;
    margin-top: 5px;
}

.charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.chart-box {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.chart-box h3 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 20px;
    font-size: 16px;
}

.chart-box canvas {
    max-height: 300px;
    width: 100% !important;
}

@media (max-width: 768px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}
</style>