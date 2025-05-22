<?php
// $attachments fornito dal controller
?>
<ul class="list-group">
<?php foreach ($attachments as $a): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="/uploads/<?= intval($a['contract_id']) ?>/<?= htmlspecialchars($a['filename']) ?>" target="_blank">
            <i class="fas fa-paperclip mr-2"></i> <?= htmlspecialchars($a['original_name']) ?>
        </a>
        <span class="badge badge-info badge-pill"><?= round($a['file_size']/1024,1) ?> KB</span>
    </li>
<?php endforeach; ?>
</ul>
