<?php
namespace App\Controller;
use App\Model\AttachmentModel;

class AttachmentController extends BaseController {
    public function uploadForm($contract_id, $error = '') {
        $attachments = (new AttachmentModel())->getByContract($contract_id);
        include __DIR__ . '/../../View/attachment/list.php';
    }
    public function upload($contract_id) {
        $error = '';
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Errore upload file.';
            $this->uploadForm($contract_id, $error);
            return;
        }
        $file = $_FILES['file'];
        if ($file['size'] > 5*1024*1024) {
            $error = 'File troppo grande (max 5MB).';
            $this->uploadForm($contract_id, $error);
            return;
        }
        $allowed = ['pdf','jpg','jpeg','png','doc','docx'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $error = 'Formato file non consentito.';
            $this->uploadForm($contract_id, $error);
            return;
        }
        $newname = uniqid('att_') . '.' . $ext;
        $dest = __DIR__ . '/../../../uploads/' . $newname;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            $error = 'Errore salvataggio file.';
            $this->uploadForm($contract_id, $error);
            return;
        }
        (new AttachmentModel())->create($contract_id, $file['name'], $newname);
        $this->redirect('/index.php?route=attachment_list&contract_id=' . $contract_id);
    }
    public function delete($id, $contract_id) {
        $model = new AttachmentModel();
        $attachments = $model->getByContract($contract_id);
        foreach ($attachments as $a) {
            if ($a['id'] == $id) {
                $path = __DIR__ . '/../../../uploads/' . $a['file_path'];
                if (file_exists($path)) unlink($path);
                $model->delete($id);
                break;
            }
        }
        $this->redirect('/index.php?route=attachment_list&contract_id=' . $contract_id);
    }
}
