<?php
namespace App\Controller;
use App\Model\CustomerModel;

class ExportController extends BaseController {
    public function exportCustomers() {
        $model = new CustomerModel();
        $customers = $model->getAll();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="clienti_export.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['nome','cognome','cf','documento','telefono','email','piva','ragione_sociale','rappresentante','sdi_pec']);
        foreach ($customers as $c) {
            fputcsv($out, [$c['nome'],$c['cognome'],$c['cf'],$c['documento'],$c['telefono'],$c['email'],$c['piva'],$c['ragione_sociale'],$c['rappresentante'],$c['sdi_pec']]);
        }
        fclose($out);
        exit;
    }
}
