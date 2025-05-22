<?php
// Richiede $contract fornito dal controller
session_start();
use CoreSuite\Models\Attachment;
$userId = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
$attachments = Attachment::allByContractForUser($contract['id'], $userId, $role);
?>
<div class="card card-secondary mt-4">
    <div class="card-header">
        <h3 class="card-title">Allegati contratto</h3>
    </div>
    <div class="card-body">
        <?php if ($role === 'admin' || $contract['user_id'] == $userId): ?>
        <form id="uploadForm" enctype="multipart/form-data" method="post" action="/attachments_upload.php" class="mb-3">
            <input type="hidden" name="contract_id" id="contract_id_hidden" value="<?= htmlspecialchars($contract['id']) ?>">
            <div class="form-group">
                <input type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Carica</button>
        </form>
        <?php endif; ?>
        <div id="uploadResult"></div>
        <div id="attachmentsList">
            <?php
            include __DIR__ . '/../attachments/list.php';
            ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var uploadForm = document.getElementById('uploadForm');
        if (uploadForm) {
            var contractId = document.getElementById('contract_id_hidden').value;
            uploadForm.onsubmit = function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                fetch('/attachments_upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(function(data) {
                    var html = '';
                    data.forEach(function(f) {
                        if (f.success) {
                            html += "<div class='alert alert-success p-2 mb-1'><i class='fas fa-check'></i> " + f.file + " caricato!</div>";
                        } else {
                            html += "<div class='alert alert-danger p-2 mb-1'><i class='fas fa-times'></i> " + f.file + ": " + f.error + "</div>";
                        }
                    });
                    document.getElementById('uploadResult').innerHTML = html;
                    // reload allegati
                    fetch('/attachments_list.php?contract_id=' + contractId)
                        .then(r => r.text())
                        .then(function(html) {
                            document.getElementById('attachmentsList').innerHTML = html;
                        });
                });
            };
        }
    });
</script>
