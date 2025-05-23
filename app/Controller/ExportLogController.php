<?php
namespace App\Controller;
use App\Model\LogModel;

class ExportLogController extends BaseController {
    public function exportLogs() {
        $model = new LogModel();
        $logs = $model->getAll(1000);
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="logs_export.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['data','utente','azione','dettagli']);
        foreach ($logs as $l) {
            fputcsv($out, [$l['created_at'],$l['email'],$l['action'],$l['details']]);
        }
        fclose($out);
        exit;
    }
}
