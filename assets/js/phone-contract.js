// JS per autocompletamento cliente e gestione upload file nei form contratti
$(document).ready(function() {
    // Inizializza select2 per ricerca clienti
    $('.select2').select2({
        ajax: {
            url: 'api/clients-search.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return {
                    results: data.map(function(client) {
                        return { id: client.id, text: client.full_name };
                    })
                };
            },
            cache: true
        },
        minimumInputLength: 2,
        placeholder: 'Cerca cliente...'
    });

    // Popola dati cliente al cambio
    $('.select2').on('select2:select', function(e) {
        var clientId = e.params.data.id;
        $.get('api/client-details.php', { id: clientId }, function(data) {
            $('#clientName').val(data.full_name);
            $('#clientFiscalCode').val(data.fiscal_code);
            $('#clientPhone').val(data.phone);
        }, 'json');
    });

    // Mostra i file selezionati
    $('.custom-file-input').on('change', function() {
        var files = this.files;
        var fileList = $('#fileList');
        fileList.empty();
        for (var i = 0; i < files.length; i++) {
            fileList.append('<div>' + files[i].name + ' (' + Math.round(files[i].size/1024) + ' KB)</div>');
        }
    });

    // Submit form
    $('#phoneContractForm, #energyContractForm').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        $.ajax({
            url: 'api/contract-save.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Contratto salvato con successo!');
                    if (typeof notifyContractSaved === 'function') notifyContractSaved();
                    window.location.href = 'index.php';
                } else {
                    alert(response.error || 'Errore nel salvataggio');
                }
            },
            error: function() {
                alert('Errore di rete');
            }
        });
    });
});
