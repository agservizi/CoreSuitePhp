document.addEventListener('DOMContentLoaded', function() {
    // Riferimenti agli elementi DOM
    const contractsTableBody = document.getElementById('contracts-table-body');
    const createContractBtn = document.getElementById('create-new-contract');
    const contractModal = new bootstrap.Modal(document.getElementById('contractModal'));
    const contractForm = document.getElementById('contract-form');

    // Carica la lista dei contratti all'avvio
    loadContracts();

    // Event listener per il pulsante "Crea Nuovo Contratto"
    createContractBtn.addEventListener('click', () => {
        contractForm.reset();
        document.getElementById('contractModalLabel').textContent = 'Nuovo Contratto';
        contractModal.show();
    });

    // Event listener per il form di salvataggio
    contractForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(contractForm);
        
        try {
            const response = await fetch('/api/contracts/save.php', {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Contratto salvato con successo', 'success');
                contractModal.hide();
                loadContracts(); // Ricarica la lista dei contratti
            } else {
                showNotification(result.message || 'Errore nel salvataggio del contratto', 'danger');
            }
        } catch (error) {
            showNotification('Errore durante il salvataggio', 'danger');
            console.error('Errore:', error);
        }
    });

    // Funzione per caricare i contratti
    async function loadContracts() {
        try {
            const response = await fetch('/api/contracts/list.php');
            const data = await response.json();
            
            if (data.success) {
                renderContracts(data.contracts);
            } else {
                showNotification(data.message || 'Errore nel caricamento dei contratti', 'danger');
            }
        } catch (error) {
            showNotification('Errore durante il caricamento dei contratti', 'danger');
            console.error('Errore:', error);
        }
    }

    // Funzione per visualizzare i contratti nella tabella
    function renderContracts(contracts) {
        contractsTableBody.innerHTML = contracts.map(contract => `
            <tr>
                <td>${contract.id}</td>
                <td>${contract.client_name}</td>
                <td>${formatDate(contract.start_date)}</td>
                <td>${formatDate(contract.end_date)}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary" onclick="editContract(${contract.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteContract(${contract.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-info" onclick="viewContract(${contract.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
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

// Funzioni globali per le azioni sui contratti
function editContract(id) {
    // Implementa la logica per modificare un contratto
    window.location.href = `/contract-details.php?id=${id}`;
}

function deleteContract(id) {
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
                loadContracts();
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

function viewContract(id) {
    // Implementa la logica per visualizzare i dettagli di un contratto
    window.location.href = `/contract-details.php?id=${id}&view=true`;
}
