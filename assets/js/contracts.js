/**
 * assets/js/contracts.js - Gestione contratti
 * CoreSuite
 */

// Variabili di stato
let currentPage = 1;
const itemsPerPage = 10;
let totalItems = 0;
let currentFilter = 'all';
let searchQuery = '';

// Funzione per caricare i contratti
function loadContracts(page = 1, filter = 'all', query = '') {
    currentPage = page;
    currentFilter = filter;
    searchQuery = query;
    
    // Mostra indicatore di caricamento
    $('#contractsTable tbody').html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Caricamento contratti...</td></tr>');
    
    // Aggiorna la UI per mostrare il filtro attivo
    $('.btn-group button').removeClass('active');
    $(`.btn-group button[data-filter="${filter}"]`).addClass('active');
    
    // Prepara i parametri per la richiesta API
    const params = new URLSearchParams();
    params.append('page', page);
    params.append('items_per_page', itemsPerPage);
    
    if (filter !== 'all') {
        params.append('filter', filter);
    }
    
    if (query) {
        params.append('search', query);
    }
    
    // Chiamata AJAX per ottenere i contratti
    $.ajax({
        url: 'api/contracts/list.php?' + params.toString(),
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayContracts(response.data.contracts);
                totalItems = response.data.total;
                setupPagination();
            } else {
                $('#contractsTable tbody').html('<tr><td colspan="7" class="text-center text-danger">Errore: ' + response.message + '</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Errore nella richiesta:', error);
            $('#contractsTable tbody').html('<tr><td colspan="7" class="text-center text-danger">Errore di connessione al server</td></tr>');
        }
    });
}

// Funzione per visualizzare i contratti nella tabella
function displayContracts(contracts) {
    if (!contracts || contracts.length === 0) {
        $('#contractsTable tbody').html('<tr><td colspan="7" class="text-center">Nessun contratto trovato</td></tr>');
        return;
    }
    
    let html = '';
    contracts.forEach(contract => {
        const statusClass = getStatusClass(contract.status);
        const contractType = contract.contract_type === 'phone' ? 'Telefonia' : 'Energia';
        const contractTypeIcon = contract.contract_type === 'phone' ? 'fa-mobile-alt' : 'fa-bolt';
        
        html += `
            <tr>
                <td>${contract.id}</td>
                <td><a href="client-details.php?id=${contract.client_id}">${contract.client_name}</a></td>
                <td><i class="fas ${contractTypeIcon}"></i> ${contractType}</td>
                <td>${contract.provider}</td>
                <td>${formatDate(contract.contract_date)}</td>
                <td><span class="badge ${statusClass}">${getStatusLabel(contract.status)}</span></td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-info" onclick="viewContractDetails(${contract.id})"><i class="fas fa-eye"></i></button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="editContract(${contract.id}, '${contract.contract_type}')"><i class="fas fa-edit"></i></button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteContract(${contract.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    $('#contractsTable tbody').html(html);
}

// Funzione per formattare la data
function formatDate(dateString) {
    if (!dateString) return 'N/D';
    
    const date = new Date(dateString);
    return date.toLocaleDateString('it-IT');
}

// Funzione per ottenere la classe CSS per lo stato del contratto
function getStatusClass(status) {
    switch (status) {
        case 'active':
            return 'badge-success';
        case 'pending':
            return 'badge-warning';
        case 'cancelled':
            return 'badge-danger';
        default:
            return 'badge-secondary';
    }
}

// Funzione per ottenere l'etichetta dello stato del contratto
function getStatusLabel(status) {
    switch (status) {
        case 'active':
            return 'Attivo';
        case 'pending':
            return 'In attesa';
        case 'cancelled':
            return 'Annullato';
        default:
            return status;
    }
}

// Funzione per impostare la paginazione
function setupPagination() {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    let paginationHtml = '';
    
    // Pulsante precedente
    paginationHtml += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadContracts(${currentPage - 1}, '${currentFilter}', '${searchQuery}')" aria-label="Precedente">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;
    
    // Pagine
    for (let i = 1; i <= totalPages; i++) {
        paginationHtml += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadContracts(${i}, '${currentFilter}', '${searchQuery}')">${i}</a>
            </li>
        `;
    }
    
    // Pulsante successivo
    paginationHtml += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="javascript:void(0)" onclick="loadContracts(${currentPage + 1}, '${currentFilter}', '${searchQuery}')" aria-label="Successivo">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    $('#pagination').html(paginationHtml);
}

// Funzione per visualizzare i dettagli di un contratto
function viewContractDetails(contractId) {
    $.ajax({
        url: `api/contracts/get.php?id=${contractId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const contract = response.data;
                
                // Riempi le informazioni del contratto
                $('#contractId').text(contract.id);
                $('#contractType').text(contract.contract_type === 'phone' ? 'Telefonia' : 'Energia');
                $('#contractProvider').text(contract.provider);
                $('#contractDate').text(formatDate(contract.contract_date));
                $('#contractExpiration').text(formatDate(contract.expiration_date));
                $('#contractMonthlyFee').text(`€ ${parseFloat(contract.monthly_fee).toFixed(2)}`);
                $('#contractStatus').html(`<span class="badge ${getStatusClass(contract.status)}">${getStatusLabel(contract.status)}</span>`);
                
                // Riempi le informazioni del cliente
                $('#clientName').text(`${contract.client.first_name} ${contract.client.last_name}`);
                $('#clientEmail').text(contract.client.email || 'N/D');
                $('#clientPhone').text(contract.client.phone || 'N/D');
                $('#clientAddress').text(contract.client.address || 'N/D');
                $('#clientCity').text(contract.client.city || 'N/D');
                
                // Mostra i dettagli specifici in base al tipo di contratto
                if (contract.contract_type === 'phone') {
                    $('#specificDetailsPhone').show();
                    $('#specificDetailsEnergy').hide();
                    $('#phoneNumber').text(contract.phone_number || 'N/D');
                    $('#migrationCode').text(contract.migration_code || 'N/D');
                } else {
                    $('#specificDetailsPhone').hide();
                    $('#specificDetailsEnergy').show();
                    $('#activationAddress').text(contract.activation_address || 'N/D');
                    $('#installationAddress').text(contract.installation_address || 'N/D');
                }
                
                // Carica gli allegati
                loadAttachments(contract.id);
                
                // Imposta l'URL per la modifica
                $('#editContractBtn').attr('href', `${contract.contract_type}-contract.php?id=${contract.id}`);
                
                // Mostra il modal
                $('#contractDetailsModal').modal('show');
            } else {
                showNotification('Errore', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Errore nella richiesta:', error);
            showNotification('Errore', 'Impossibile caricare i dettagli del contratto', 'error');
        }
    });
}

// Funzione per caricare gli allegati di un contratto
function loadAttachments(contractId) {
    $.ajax({
        url: `api/attachments/list.php?contract_id=${contractId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = '';
                response.data.forEach(attachment => {
                    html += `
                        <tr>
                            <td>${attachment.filename}</td>
                            <td>${formatFileSize(attachment.filesize)}</td>
                            <td>${formatDate(attachment.created_at)}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="${attachment.filepath}" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-download"></i></a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteAttachment(${attachment.id})"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                
                $('#attachmentsList').html(html);
            } else {
                $('#attachmentsList').html('<tr><td colspan="4" class="text-center">Nessun allegato trovato</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Errore nella richiesta:', error);
            $('#attachmentsList').html('<tr><td colspan="4" class="text-center text-danger">Errore nel caricamento degli allegati</td></tr>');
        }
    });
}

// Funzione per formattare la dimensione del file
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Funzione per eliminare un allegato
function deleteAttachment(attachmentId) {
    if (confirm('Sei sicuro di voler eliminare questo allegato?')) {
        $.ajax({
            url: 'api/attachments/delete.php',
            type: 'POST',
            data: JSON.stringify({ id: attachmentId }),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    showNotification('Successo', 'Allegato eliminato con successo', 'success');
                    loadAttachments($('#contractId').text());
                } else {
                    showNotification('Errore', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore nella richiesta:', error);
                showNotification('Errore', 'Impossibile eliminare l\'allegato', 'error');
            }
        });
    }
}

// Funzione per reindirizzare alla pagina di modifica contratto
function editContract(contractId, contractType) {
    window.location.href = `${contractType}-contract.php?id=${contractId}`;
}

// Funzione per eliminare un contratto
function deleteContract(contractId) {
    $('#deleteContractId').val(contractId);
    $('#deleteContractModal').modal('show');
}

/**
 * Funzione per esportare i contratti in formato CSV
 */
function exportContractsCSV() {
    // Costruisci l'URL con i parametri attuali
    const params = new URLSearchParams();
    
    if (currentFilter !== 'all') {
        params.append('filter', currentFilter);
    }
    
    if (searchQuery) {
        params.append('search', searchQuery);
    }
    
    // Crea un link temporaneo per il download
    const url = `api/contracts/export.php?${params.toString()}`;
    const a = document.createElement('a');
    a.href = url;
    a.download = `contratti_${formatDateForFilename(new Date())}.csv`;
    
    // Aggiungi e clicca il link nascosto per avviare il download
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
    // Mostra una notifica di successo
    showNotification('Esportazione avviata', 'Il download del file CSV è in corso', 'success');
}

/**
 * Funzione per formattare una data per il nome del file
 * @param {Date} date Data da formattare
 * @returns {string} Data nel formato YYYY-MM-DD
 */
function formatDateForFilename(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Al caricamento del documento
$(document).ready(function() {
    // Carica i contratti
    loadContracts();
    
    // Gestione filtri
    $('.btn-group button[data-filter]').on('click', function() {
        const filter = $(this).data('filter');
        loadContracts(1, filter, searchQuery);
    });
    
    // Gestione ricerca
    $('#searchButton').on('click', function() {
        const query = $('#searchInput').val().trim();
        loadContracts(1, currentFilter, query);
    });
    
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) {
            const query = $(this).val().trim();
            loadContracts(1, currentFilter, query);
        }
    });
    
    // Gestione esportazione CSV
    $('#exportCsvBtn').on('click', function() {
        exportContractsCSV();
    });
    
    // Gestione eliminazione contratto
    $('#confirmDeleteContract').on('click', function() {
        const contractId = $('#deleteContractId').val();
        
        $.ajax({
            url: 'api/contracts/delete.php',
            type: 'POST',
            data: JSON.stringify({ id: contractId }),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    $('#deleteContractModal').modal('hide');
                    showNotification('Successo', 'Contratto eliminato con successo', 'success');
                    loadContracts(currentPage, currentFilter, searchQuery);
                } else {
                    showNotification('Errore', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Errore nella richiesta:', error);
                showNotification('Errore', 'Impossibile eliminare il contratto', 'error');
            }
        });
    });
    
    // Registrazione Service Worker per le notifiche push
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/service-worker.js')
            .then(function(registration) {
                console.log('Service Worker registrato con successo:', registration);
            })
            .catch(function(error) {
                console.log('Registrazione Service Worker fallita:', error);
            });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const contractsTableBody = document.getElementById('contracts-table-body');
    const createNewContractButton = document.getElementById('create-new-contract');
    const contractForm = document.getElementById('contract-form');

    // Funzione per caricare i contratti
    function loadContracts() {
        fetch('/api/contracts/list.php')
            .then(response => response.json())
            .then(data => {
                contractsTableBody.innerHTML = '';
                data.forEach(contract => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${contract.id}</td>
                        <td>${contract.client_name}</td>
                        <td>${contract.start_date}</td>
                        <td>${contract.end_date || 'N/A'}</td>
                        <td>
                            <button class="btn btn-sm btn-warning">Modifica</button>
                            <button class="btn btn-sm btn-danger">Elimina</button>
                        </td>
                    `;
                    contractsTableBody.appendChild(row);
                });
            })
            .catch(error => console.error('Errore nel caricamento dei contratti:', error));
    }

    // Mostra il modale per creare un nuovo contratto
    createNewContractButton.addEventListener('click', function () {
        const contractModal = new bootstrap.Modal(document.getElementById('contractModal'));
        contractModal.show();
    });

    // Gestione del salvataggio del contratto
    contractForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(contractForm);
        fetch('/api/contracts/save.php', {
            method: 'POST',
            body: JSON.stringify(Object.fromEntries(formData)),
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Contratto salvato con successo!');
                    loadContracts();
                    const contractModal = bootstrap.Modal.getInstance(document.getElementById('contractModal'));
                    contractModal.hide();
                } else {
                    alert('Errore nel salvataggio del contratto: ' + data.message);
                }
            })
            .catch(error => console.error('Errore nel salvataggio del contratto:', error));
    });

    // Carica i contratti all'avvio
    loadContracts();
});
