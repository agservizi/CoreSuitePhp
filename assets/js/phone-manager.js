// File JavaScript per gestire la lista dei contratti telefonici
document.addEventListener('DOMContentLoaded', function() {
    // Riferimenti agli elementi DOM
    const phoneContractsTableBody = document.getElementById('phone-contracts-table-body');
    const createPhoneContractBtn = document.getElementById('create-new-phone-contract');
    const filterInput = document.getElementById('filter-phone-contracts');
    const phoneContractModal = new bootstrap.Modal(document.getElementById('phoneContractModal'));
    const phoneContractForm = document.getElementById('phone-contract-form');

    // Carica la lista dei contratti all'avvio
    loadPhoneContracts();

    // Event listener per il pulsante "Crea Nuovo Contratto"
    createPhoneContractBtn.addEventListener('click', () => {
        // Reset del form
        phoneContractForm.reset();
        document.getElementById('phoneContractModalLabel').textContent = 'Nuovo Contratto Telefonico';
        document.getElementById('contract-id').value = '';
        
        // Mostra il modale
        phoneContractModal.show();
    });    // Event listener per il filtro
    filterInput.addEventListener('input', function() {
        const filterValue = this.value.toLowerCase();
        const rows = phoneContractsTableBody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filterValue) ? '' : 'none';
        });
    });

    // Funzione per caricare i contratti telefonici
    async function loadPhoneContracts() {
        try {
            const response = await fetch('/api/contracts/list.php?type=phone');
            const data = await response.json();
            
            if (data.success) {
                renderPhoneContracts(data.contracts);
            } else {
                showNotification(data.message || 'Errore nel caricamento dei contratti', 'danger');
            }
        } catch (error) {
            showNotification('Errore durante il caricamento dei contratti', 'danger');
            console.error('Errore:', error);
        }
    }

    // Funzione per visualizzare i contratti nella tabella
    function renderPhoneContracts(contracts) {
        phoneContractsTableBody.innerHTML = contracts.length > 0 ? 
            contracts.map(contract => `
                <tr>
                    <td>${contract.id}</td>
                    <td>${contract.client_name}</td>
                    <td>${contract.provider}</td>
                    <td>${formatDate(contract.start_date)}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary" onclick="editPhoneContract(${contract.id})">
                                <i class="bi bi-pencil"></i> Modifica
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="deletePhoneContract(${contract.id})">
                                <i class="bi bi-trash"></i> Elimina
                            </button>
                            <button type="button" class="btn btn-sm btn-info" onclick="viewPhoneContract(${contract.id})">
                                <i class="bi bi-eye"></i> Visualizza
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('') : 
            '<tr><td colspan="5" class="text-center">Nessun contratto trovato</td></tr>';
    }

    // Funzione per formattare le date
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('it-IT');
    }

    // Funzione per mostrare le notifiche
    function showNotification(message, type = 'info') {
        const toastContainer = document.getElementById('notifications-container');
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
});

// Funzioni globali per le azioni sui contratti telefonici
function editPhoneContract(id) {
    window.location.href = `/phone-contract.php?id=${id}`;
}

function deletePhoneContract(id) {
    if (confirm('Sei sicuro di voler eliminare questo contratto?')) {
        fetch(`/api/contracts/delete.php`, {
            method: 'POST',
            body: JSON.stringify({ id }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showNotification('Contratto eliminato con successo', 'success');
                // Ricarica i contratti
                loadPhoneContracts();
            } else {
                showNotification(result.message || 'Errore nell\'eliminazione del contratto', 'danger');
            }
        })
        .catch(error => {
            showNotification('Errore durante l\'eliminazione', 'danger');
            console.error('Errore:', error);
        });
    }
}

function viewPhoneContract(id) {
    window.location.href = `/phone-contract.php?id=${id}&view=true`;
}

// Funzione per mostrare notifiche (usata dalle funzioni globali)
function showNotification(message, type = 'info') {
    const toastContainer = document.getElementById('notifications-container');
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
