// Dashboard Charts and Interactions

document.addEventListener('DOMContentLoaded', function() {
    // Grafico Contratti per Tipo
    var contractsCtx = document.getElementById('contractsChart').getContext('2d');
    var contractsChart = new Chart(contractsCtx, {
        type: 'pie',
        data: {
            labels: ['Telefonia', 'Energia'],
            datasets: [{
                data: [30, 70],
                backgroundColor: [
                    '#3498db',
                    '#e74c3c'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    padding: 10
                }
            }
        }
    });

    // Grafico Provider
    var providersCtx = document.getElementById('providersChart').getContext('2d');
    var providersChart = new Chart(providersCtx, {
        type: 'bar',
        data: {
            labels: ['Fastweb', 'Windtre', 'Pianeta Fibra', 'Enel', 'A2A'],
            datasets: [{
                label: 'Contratti per Provider',
                data: [65, 59, 80, 81, 56],
                backgroundColor: [
                    '#3498db',
                    '#2ecc71',
                    '#f1c40f',
                    '#e74c3c',
                    '#9b59b6'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    
    // Grafico Contratti per Mese
    var monthlyCtx = document.getElementById('monthlyChart');
    var monthlyChart = null;
    
    if (monthlyCtx) {
        monthlyChart = new Chart(monthlyCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu'],
                datasets: [{
                    label: 'Contratti per Mese',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            precision: 0
                        },
                        gridLines: {
                            drawBorder: false
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            display: false
                        }
                    }]
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    bodyFontColor: '#fff',
                    titleFontColor: '#fff',
                    titleFontSize: 14,
                    bodyFontSize: 13,
                    displayColors: false
                }
            }
        });
    }

    // Aggiornamento dati in tempo reale
    function updateDashboardData() {
        fetch('api/dashboard-data.php')
            .then(response => response.json())
            .then(data => {
                // Controllo per errori
                if (data.error) {
                    console.error('Errore API:', data.error);
                    showNotification(data.error, 'error');
                    return;
                }
                
                // Aggiorna i contatori
                updateCounter('.contracts-total', data.totalContracts);
                updateCounter('.contracts-active', data.activeContracts);
                updateCounter('.clients-total', data.totalClients);
                updateCounter('.contracts-pending', data.pendingContracts);
                
                // Aggiorna i conteggi di scadenze se l'elemento esiste
                if (document.querySelector('.contracts-expiring')) {
                    updateCounter('.contracts-expiring', data.expiringContracts);
                }

                // Aggiorna i grafici
                contractsChart.data.datasets[0].data = [
                    data.phoneContracts,
                    data.energyContracts
                ];
                contractsChart.update();

                // Aggiorna grafici provider
                if (data.providerLabels && data.providerLabels.length > 0) {
                    providersChart.data.labels = data.providerLabels;
                    providersChart.data.datasets[0].data = data.providerStats;
                    providersChart.update();
                }
                
                // Aggiorna grafico mensile se esiste
                if (monthlyChart && data.monthLabels && data.monthLabels.length > 0) {
                    monthlyChart.data.labels = data.monthLabels;
                    monthlyChart.data.datasets[0].data = data.monthData;
                    monthlyChart.update();
                }

                // Aggiorna la tabella degli ultimi contratti
                updateLatestContracts(data.latestContracts);
            })
            .catch(error => {
                console.error('Errore durante l\'aggiornamento dashboard:', error);
                // Mostra notifica di errore
                showNotification('Errore nel caricamento dei dati', 'error');
            });
    }
    
    // Funzione per aggiornare i contatori con animazione
    function updateCounter(selector, newValue) {
        const element = document.querySelector(selector);
        if (!element) return;
        
        const currentValue = parseInt(element.textContent, 10) || 0;
        if (currentValue === newValue) return;
        
        // Usa la libreria CountUp.js se è disponibile
        if (typeof CountUp !== 'undefined') {
            new CountUp(element, currentValue, newValue, 0, 1.5, {
                useEasing: true,
                useGrouping: true,
                separator: ',',
                decimal: '.'
            }).start();
        } else {
            // Fallback semplice
            element.textContent = newValue;
        }
    }

    // Aggiorna la tabella degli ultimi contratti
    function updateLatestContracts(contracts) {
        const tableBody = document.getElementById('latestContractsTable');
        if (!tableBody) return;
        
        tableBody.innerHTML = '';
        
        if (contracts.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = '<td colspan="6" class="text-center">Nessun contratto trovato</td>';
            tableBody.appendChild(emptyRow);
            return;
        }
        
        contracts.forEach(contract => {
            const row = document.createElement('tr');
            
            // Determina lo stile del badge di stato
            let statusBadge = 'badge-secondary';
            if (contract.status === 'active') statusBadge = 'badge-success';
            if (contract.status === 'pending') statusBadge = 'badge-warning';
            if (contract.status === 'cancelled') statusBadge = 'badge-danger';
            
            // Traduce lo stato in italiano
            let statusText = 'Sconosciuto';
            if (contract.status === 'active') statusText = 'Attivo';
            if (contract.status === 'pending') statusText = 'In attesa';
            if (contract.status === 'cancelled') statusText = 'Annullato';
            
            row.innerHTML = `
                <td>${contract.id}</td>
                <td>${contract.client_name}</td>
                <td>${contract.type === 'phone' ? 'Telefonia' : (contract.type === 'energy' ? 'Energia' : contract.type)}</td>
                <td>${contract.provider}</td>
                <td><span class="badge ${statusBadge}">${statusText}</span></td>
                <td>${contract.date}</td>
            `;
            
            tableBody.appendChild(row);
        });
    }

    // Prima richiesta dati all'avvio
    updateDashboardData();
    
    // Aggiorna dati ogni 60 secondi
    setInterval(updateDashboardData, 60000);
    
    // Gestore per il pulsante di aggiornamento
    document.getElementById('refreshDashboard')?.addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-sync fa-spin"></i>';
        this.disabled = true;
        
        updateDashboardData();
        
        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-sync"></i>';
            this.disabled = false;
            showNotification('Dashboard aggiornata', 'success');
        }, 1000);
    });
    
    // Funzione per mostrare notifiche toast
    function showNotification(message, type = 'info') {
        const container = document.getElementById('notifications-container');
        if (!container) return;
        
        const toast = document.createElement('div');
        toast.className = `toast bg-${type === 'error' ? 'danger' : type}`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.setAttribute('data-delay', '5000');
        
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="mr-auto">${type === 'error' ? 'Errore' : (type === 'success' ? 'Successo' : 'Informazione')}</strong>
                <small>${new Date().toLocaleTimeString()}</small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body text-white">
                ${message}
            </div>
        `;
        
        container.appendChild(toast);
        $(toast).toast('show');
        
        // Rimuovi il toast dopo che è stato nascosto
        $(toast).on('hidden.bs.toast', function() {
            this.remove();
        });
    }
    
    // Gestore per export dati
    document.getElementById('exportDashboardData')?.addEventListener('click', function() {
        fetch('api/dashboard-data.php')
            .then(response => response.json())
            .then(data => {
                const now = new Date();
                const dateStr = now.toISOString().slice(0, 10);
                const timeStr = now.toTimeString().slice(0, 8).replace(/:/g, '-');
                const filename = `dashboard-report-${dateStr}-${timeStr}.csv`;
                
                // Crea le righe del CSV
                let csvContent = 'Categoria,Valore\n';
                csvContent += `Contratti totali,${data.totalContracts}\n`;
                csvContent += `Contratti attivi,${data.activeContracts}\n`;
                csvContent += `Contratti in attesa,${data.pendingContracts}\n`;
                csvContent += `Clienti totali,${data.totalClients}\n`;
                csvContent += `Contratti telefonia,${data.phoneContracts}\n`;
                csvContent += `Contratti energia,${data.energyContracts}\n`;
                csvContent += `Contratti in scadenza,${data.expiringContracts}\n`;
                
                // Aggiungi provider
                csvContent += '\nProvider,Numero contratti\n';
                data.providerLabels.forEach((provider, index) => {
                    csvContent += `${provider},${data.providerStats[index]}\n`;
                });
                
                // Aggiungi dati mensili
                csvContent += '\nMese,Numero contratti\n';
                data.monthLabels.forEach((month, index) => {
                    csvContent += `${month},${data.monthData[index]}\n`;
                });
                
                // Crea e scarica il file
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                showNotification('Report esportato con successo', 'success');
            })
            .catch(error => {
                console.error('Errore durante l\'esportazione:', error);
                showNotification('Errore durante l\'esportazione dei dati', 'error');
            });
    });
});
