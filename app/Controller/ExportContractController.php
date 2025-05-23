<?php
namespace App\Controller;
use App\Model\ContractModel;

class ExportContractController extends BaseController {
    public function exportContracts() {
        $model = new ContractModel();
        $contracts = $model->getAll();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="contratti_export.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['id','cliente','gestore','tipo','stato','data_inizio','data_fine']);
        foreach ($contracts as $c) {
            fputcsv($out, [$c['id'],$c['nome'].' '.$c['cognome'],$c['provider_name'],$c['type'],$c['status'],$c['data_inizio'],$c['data_fine']]);
        }
        fclose($out);
        exit;
    }
}
