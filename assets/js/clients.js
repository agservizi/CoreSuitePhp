// JS gestione clienti: caricamento, aggiunta e refresh tabella
$(document).ready(function() {
    // Funzione per caricare i clienti con filtro di ricerca
    function loadClients(searchTerm = '') {
        let url = 'api/clients-list.php';
        if (searchTerm) {
            url += '?search=' + encodeURIComponent(searchTerm);
        }
        
        $.get(url, function(data) {
            var tbody = $('#clientsTable tbody');
            tbody.empty();
            
            if (data.length === 0) {
                tbody.append('<tr><td colspan="5" class="text-center">Nessun cliente trovato</td></tr>');
                return;
            }
            
            data.forEach(function(client) {
                tbody.append('<tr>' +
                    '<td>' + client.full_name + '</td>' +
                    '<td>' + (client.email || '-') + '</td>' +
                    '<td>' + (client.phone || '-') + '</td>' +
                    '<td>' + (client.fiscal_code || '-') + '</td>' +
                    '<td>' + 
                        '<div class="btn-group">' +
                            '<a href="client-details.php?id=' + client.id + '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>' +
                            '<button class="btn btn-sm btn-danger delete-client" data-id="' + client.id + '"><i class="fas fa-trash"></i></button>' +
                        '</div>' +
                    '</td>' +
                '</tr>');
            });
            
            // Assegna handler eventi ai pulsanti di eliminazione
            $('.delete-client').on('click', function() {
                var clientId = $(this).data('id');
                showDeleteConfirmation(clientId);
            });
        }, 'json');
    }
    loadClients();

    $('#addClientForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.post('api/client-save.php', formData, function(response) {
            if (response.success) {
                $('#addClientModal').modal('hide');
                loadClients();
            } else {
                alert(response.error || 'Errore nel salvataggio');
            }
        }, 'json');
    });

    // Mostra il modal di conferma eliminazione
    function showDeleteConfirmation(clientId) {
        $('#deleteClientId').val(clientId);
        $('#deleteClientModal').modal('show');
    }
    
    // Handler evento pulsante di conferma eliminazione
    $('#confirmDeleteClient').on('click', function() {
        var clientId = $('#deleteClientId').val();
        deleteClient(clientId);
    });
    
    // Funzione per eliminare un cliente
    function deleteClient(clientId) {
        $.ajax({
            url: 'api/clients/delete.php?id=' + clientId,
            type: 'DELETE',
            success: function(response) {
                if (response.success) {
                    $('#deleteClientModal').modal('hide');
                    // Mostra notifica di successo
                    if (typeof showNotification === 'function') {
                        showNotification('Successo', 'Cliente eliminato con successo', 'success');
                    } else {
                        alert('Cliente eliminato con successo');
                    }
                    // Ricarica la tabella clienti
                    loadClients();
                } else {
                    // Mostra notifica di errore
                    if (typeof showNotification === 'function') {
                        showNotification('Errore', response.message || 'Errore durante l\'eliminazione del cliente', 'error');
                    } else {
                        alert(response.message || 'Errore durante l\'eliminazione del cliente');
                    }
                }
            },
            error: function() {
                // Mostra notifica di errore
                if (typeof showNotification === 'function') {
                    showNotification('Errore', 'Si è verificato un errore durante l\'eliminazione del cliente', 'error');
                } else {
                    alert('Si è verificato un errore durante l\'eliminazione del cliente');
                }
            }
        });
    }
    
    // Gestione della ricerca
    $('#searchButton').on('click', function() {
        const searchTerm = $('#searchInput').val().trim();
        loadClients(searchTerm);
    });
    
    // Ricerca anche quando si preme Invio nel campo di ricerca
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) { // Codice tasto Invio
            e.preventDefault();
            const searchTerm = $(this).val().trim();
            loadClients(searchTerm);
        }
    });
});
