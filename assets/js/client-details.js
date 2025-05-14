/**
 * client-details.js - Gestione dettaglio cliente
 * CoreSuite
 */

document.addEventListener('DOMContentLoaded', function() {
    // Ottieni l'ID del cliente dall'URL
    const urlParams = new URLSearchParams(window.location.search);
    const clientId = urlParams.get('id');
    
    if (!clientId) {
        showNotification('Errore', 'ID cliente non specificato', 'error');
        setTimeout(() => {
            window.location.href = 'clients.php';
        }, 2000);
        return;
    }
    
    // Aggiorna i link per i nuovi contratti
    document.getElementById('newPhoneContract').href += clientId;
    document.getElementById('newEnergyContract').href += clientId;
    
    // Carica i dati del cliente
    loadClientDetails(clientId);
    
    // Carica i contratti del cliente
    loadClientContracts(clientId);
    
    // Carica le note del cliente
    loadClientNotes(clientId);
    
    // Carica gli allegati del cliente
    loadClientAttachments(clientId);
    
    // Evento click per il pulsante di modifica cliente
    document.getElementById('editClientBtn').addEventListener('click', function() {
        // Prepopola il form con i dati attuali del cliente
        $('#editClientModal').modal('show');
    });
    
    // Evento submit per il form di modifica cliente
    document.getElementById('saveClientBtn').addEventListener('click', function() {
        updateClient(clientId);
    });
    
    // Evento submit per il form di aggiunta nota
    document.getElementById('addNoteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addClientNote(clientId);
    });
    
    // Setup area upload file
    setupFileUpload(clientId);
    
    // Evento per il pulsante di caricamento allegato
    document.getElementById('uploadBtn').addEventListener('click', function() {
        uploadClientAttachment(clientId);
    });
});

/**
 * Carica i dettagli del cliente dal server
 */
function loadClientDetails(clientId) {
    fetch(`api/clients/get.php?id=${clientId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Errore nel caricamento dei dati del cliente');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayClientDetails(data.client);
                // Prepopola il form di modifica
                populateEditForm(data.client);
            } else {
                showNotification('Errore', data.message || 'Cliente non trovato', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Errore', 'Si è verificato un errore durante il caricamento dei dati', 'error');
        });
}

/**
 * Mostra i dettagli del cliente nell'interfaccia
 */
function displayClientDetails(client) {
    document.getElementById('clientName').textContent = `${client.first_name} ${client.last_name}`;
    document.getElementById('clientEmail').textContent = client.email;
    document.getElementById('clientPhone').textContent = client.phone || 'Non specificato';
    document.getElementById('clientAddress').textContent = client.address || 'Non specificato';
    document.getElementById('clientFiscalCode').textContent = client.fiscal_code || 'Non specificato';
    document.getElementById('clientVAT').textContent = client.vat_number || 'Non specificato';
    document.getElementById('registrationDate').textContent = formatDate(client.created_at);
    document.getElementById('clientStatus').textContent = client.status == 1 ? 'Attivo' : 'Inattivo';
    
    // Aggiorna anche il titolo della pagina
    document.title = `CoreSuite - Cliente: ${client.first_name} ${client.last_name}`;
}

/**
 * Carica i contratti del cliente dal server
 */
function loadClientContracts(clientId) {
    fetch(`api/contracts/list.php?client_id=${clientId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Errore nel caricamento dei contratti');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayClientContracts(data.contracts);
                document.getElementById('contractsCount').textContent = data.contracts.length;
            } else {
                document.getElementById('contractsList').innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center">Nessun contratto trovato</td>
                    </tr>
                `;
                document.getElementById('contractsCount').textContent = '0';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Errore', 'Si è verificato un errore durante il caricamento dei contratti', 'error');
        });
}

/**
 * Mostra i contratti del cliente nell'interfaccia
 */
function displayClientContracts(contracts) {
    if (contracts.length === 0) {
        document.getElementById('contractsList').innerHTML = `
            <tr>
                <td colspan="6" class="text-center">Nessun contratto trovato</td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    contracts.forEach(contract => {
        const statusClass = getStatusClass(contract.status);
        html += `
            <tr>
                <td>${contract.id}</td>
                <td>${contract.type === 'phone' ? 'Telefonia' : 'Energia'}</td>
                <td>${contract.provider}</td>
                <td>${formatDate(contract.contract_date)}</td>
                <td><span class="badge ${statusClass}">${getStatusText(contract.status)}</span></td>
                <td>
                    <div class="btn-group">
                        <a href="${contract.type === 'phone' ? 'phone-contract.php' : 'energy-contract.php'}?id=${contract.id}" class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDeleteContract(${contract.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    document.getElementById('contractsList').innerHTML = html;
}

/**
 * Prepara il form di modifica con i dati attuali del cliente
 */
function populateEditForm(client) {
    document.getElementById('editClientId').value = client.id;
    document.getElementById('editFirstName').value = client.first_name;
    document.getElementById('editLastName').value = client.last_name;
    document.getElementById('editEmail').value = client.email;
    document.getElementById('editPhone').value = client.phone || '';
    document.getElementById('editAddress').value = client.address || '';
    document.getElementById('editFiscalCode').value = client.fiscal_code || '';
    document.getElementById('editVatNumber').value = client.vat_number || '';
    document.getElementById('editStatus').value = client.status;
}

/**
 * Aggiorna i dati del cliente
 */
function updateClient(clientId) {
    const formData = {
        id: clientId,
        first_name: document.getElementById('editFirstName').value,
        last_name: document.getElementById('editLastName').value,
        email: document.getElementById('editEmail').value,
        phone: document.getElementById('editPhone').value,
        address: document.getElementById('editAddress').value,
        fiscal_code: document.getElementById('editFiscalCode').value,
        vat_number: document.getElementById('editVatNumber').value,
        status: document.getElementById('editStatus').value
    };
    
    fetch('api/clients/update.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#editClientModal').modal('hide');
            showNotification('Successo', 'Cliente aggiornato con successo', 'success');
            // Ricarica i dettagli del cliente
            loadClientDetails(clientId);
        } else {
            showNotification('Errore', data.message || 'Errore durante l\'aggiornamento del cliente', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Errore', 'Si è verificato un errore durante l\'aggiornamento del cliente', 'error');
    });
}

/**
 * Funzione per caricare le note del cliente
 */
function loadClientNotes(clientId) {
    fetch(`api/notes/list.php?client_id=${clientId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Errore nel caricamento delle note');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.notes.length > 0) {
                displayClientNotes(data.notes);
            } else {
                document.getElementById('clientNotes').innerHTML = `
                    <div class="alert alert-info">Nessuna nota presente per questo cliente.</div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Errore', 'Si è verificato un errore durante il caricamento delle note', 'error');
        });
}

/**
 * Mostra le note del cliente nell'interfaccia
 */
function displayClientNotes(notes) {
    let html = '';
    notes.forEach(note => {
        html += `
            <div class="post">
                <div class="user-block">
                    <span class="username">
                        ${note.user_name}
                        <a href="#" class="float-right btn-tool" onclick="deleteNote(${note.id})">
                            <i class="fas fa-times"></i>
                        </a>
                    </span>
                    <span class="description">${formatDate(note.created_at)}</span>
                </div>
                <p>${note.content}</p>
            </div>
            <hr>
        `;
    });
    
    document.getElementById('clientNotes').innerHTML = html;
}

/**
 * Aggiunge una nuova nota al cliente
 */
function addClientNote(clientId) {
    const content = document.getElementById('noteContent').value.trim();
    
    if (!content) {
        showNotification('Attenzione', 'Il contenuto della nota non può essere vuoto', 'warning');
        return;
    }
    
    const formData = {
        client_id: clientId,
        content: content
    };
    
    fetch('api/notes/add.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('noteContent').value = '';
            showNotification('Successo', 'Nota aggiunta con successo', 'success');
            // Ricarica le note
            loadClientNotes(clientId);
        } else {
            showNotification('Errore', data.message || 'Errore durante l\'aggiunta della nota', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Errore', 'Si è verificato un errore durante l\'aggiunta della nota', 'error');
    });
}

/**
 * Elimina una nota
 */
function deleteNote(noteId) {
    if (!confirm('Sei sicuro di voler eliminare questa nota?')) {
        return;
    }
    
    fetch(`api/notes/delete.php?id=${noteId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Successo', 'Nota eliminata con successo', 'success');
            // Ricarica le note
            loadClientNotes(urlParams.get('id'));
        } else {
            showNotification('Errore', data.message || 'Errore durante l\'eliminazione della nota', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Errore', 'Si è verificato un errore durante l\'eliminazione della nota', 'error');
    });
}

/**
 * Setup dell'area di upload file
 */
function setupFileUpload(clientId) {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileUpload');
    
    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });
    
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('bg-light');
    });
    
    uploadArea.addEventListener('dragleave', function() {
        uploadArea.classList.remove('bg-light');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('bg-light');
        
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            showNotification('Info', `File selezionato: ${fileInput.files[0].name}`, 'info');
        }
    });
    
    fileInput.addEventListener('change', function() {
        if (fileInput.files.length) {
            showNotification('Info', `File selezionato: ${fileInput.files[0].name}`, 'info');
        }
    });
}

/**
 * Carica un allegato per il cliente
 */
function uploadClientAttachment(clientId) {
    const fileInput = document.getElementById('fileUpload');
    const description = document.getElementById('fileDescription').value.trim();
    
    if (!fileInput.files.length) {
        showNotification('Attenzione', 'Nessun file selezionato', 'warning');
        return;
    }
    
    if (!description) {
        showNotification('Attenzione', 'La descrizione è obbligatoria', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('client_id', clientId);
    formData.append('description', description);
    formData.append('file', fileInput.files[0]);
    
    fetch('api/attachments/upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fileInput.value = '';
            document.getElementById('fileDescription').value = '';
            showNotification('Successo', 'File caricato con successo', 'success');
            // Ricarica gli allegati
            loadClientAttachments(clientId);
        } else {
            showNotification('Errore', data.message || 'Errore durante il caricamento del file', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Errore', 'Si è verificato un errore durante il caricamento del file', 'error');
    });
}

/**
 * Carica gli allegati del cliente
 */
function loadClientAttachments(clientId) {
    fetch(`api/attachments/list.php?client_id=${clientId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Errore nel caricamento degli allegati');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.attachments.length > 0) {
                displayClientAttachments(data.attachments);
            } else {
                document.getElementById('attachmentsList').innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center">Nessun allegato trovato</td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Errore', 'Si è verificato un errore durante il caricamento degli allegati', 'error');
        });
}

/**
 * Mostra gli allegati del cliente nell'interfaccia
 */
function displayClientAttachments(attachments) {
    let html = '';
    attachments.forEach(attachment => {
        html += `
            <tr>
                <td>${attachment.description}</td>
                <td>${attachment.filename}</td>
                <td>${formatDate(attachment.created_at)}</td>
                <td>
                    <div class="btn-group">
                        <a href="uploads/${attachment.filepath}" target="_blank" class="btn btn-sm btn-info">
                            <i class="fas fa-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteAttachment(${attachment.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
    
    document.getElementById('attachmentsList').innerHTML = html;
}

/**
 * Elimina un allegato
 */
function deleteAttachment(attachmentId) {
    if (!confirm('Sei sicuro di voler eliminare questo allegato?')) {
        return;
    }
    
    fetch(`api/attachments/delete.php?id=${attachmentId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Successo', 'Allegato eliminato con successo', 'success');
            // Ricarica gli allegati
            loadClientAttachments(urlParams.get('id'));
        } else {
            showNotification('Errore', data.message || 'Errore durante l\'eliminazione dell\'allegato', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Errore', 'Si è verificato un errore durante l\'eliminazione dell\'allegato', 'error');
    });
}

/**
 * Prepara la conferma eliminazione contratto
 */
function confirmDeleteContract(contractId) {
    document.getElementById('deleteContractId').value = contractId;
    $('#deleteContractModal').modal('show');
}

/**
 * Elimina un contratto
 */
document.getElementById('confirmDeleteContract').addEventListener('click', function() {
    const contractId = document.getElementById('deleteContractId').value;
    
    fetch(`api/contracts/delete.php?id=${contractId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#deleteContractModal').modal('hide');
            showNotification('Successo', 'Contratto eliminato con successo', 'success');
            // Ricarica i contratti
            loadClientContracts(urlParams.get('id'));
            // Aggiorna anche il numero di contratti
            loadClientDetails(urlParams.get('id'));
        } else {
            showNotification('Errore', data.message || 'Errore durante l\'eliminazione del contratto', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Errore', 'Si è verificato un errore durante l\'eliminazione del contratto', 'error');
    });
});

/**
 * Funzioni di utility
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('it-IT') + ' ' + date.toLocaleTimeString('it-IT', {hour: '2-digit', minute: '2-digit'});
}

function getStatusClass(status) {
    switch (status) {
        case 'active':
        case 1:
        case '1':
            return 'bg-success';
        case 'pending':
        case 2:
        case '2':
            return 'bg-warning';
        case 'inactive':
        case 0:
        case '0':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

function getStatusText(status) {
    switch (status) {
        case 'active':
        case 1:
        case '1':
            return 'Attivo';
        case 'pending':
        case 2:
        case '2':
            return 'In attesa';
        case 'inactive':
        case 0:
        case '0':
            return 'Inattivo';
        default:
            return 'Sconosciuto';
    }
}
