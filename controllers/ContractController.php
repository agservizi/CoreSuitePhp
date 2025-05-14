<?php
require_once __DIR__ . '/ApiController.php';

class ContractController extends ApiController {
    public function create() {
        try {
            $data = $this->getRequestData();
            $this->validateRequired($data, [
                'client_id',
                'contract_type',
                'provider',
                'activation_address'
            ]);

            $data = $this->sanitizeInput($data);

            $this->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO contracts (
                    client_id,
                    contract_type,
                    provider,
                    activation_address,
                    installation_address,
                    migration_code,
                    phone_number,
                    created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['client_id'],
                $data['contract_type'],
                $data['provider'],
                $data['activation_address'],
                $data['installation_address'] ?? null,
                $data['migration_code'] ?? null,
                $data['phone_number'] ?? null,
                $this->user
            ]);

            $contractId = $this->db->lastInsertId();

            // Gestione allegati
            if (isset($_FILES['attachments'])) {
                foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $_FILES['attachments']['name'][$key],
                            'type' => $_FILES['attachments']['type'][$key],
                            'tmp_name' => $tmp_name,
                            'error' => $_FILES['attachments']['error'][$key],
                            'size' => $_FILES['attachments']['size'][$key]
                        ];

                        $fileInfo = $this->handleFileUpload($file);

                        $stmt = $this->db->prepare("
                            INSERT INTO attachments (
                                contract_id,
                                file_name,
                                file_path,
                                file_size,
                                uploaded_by
                            ) VALUES (?, ?, ?, ?, ?)
                        ");

                        $stmt->execute([
                            $contractId,
                            $fileInfo['name'],
                            $fileInfo['path'],
                            $fileInfo['size'],
                            $this->user
                        ]);
                    }
                }
            }

            $this->commit();

            $this->sendResponse(201, [
                'success' => true,
                'message' => 'Contratto creato con successo',
                'contract_id' => $contractId
            ]);

        } catch (Exception $e) {
            $this->rollback();
            $this->sendResponse(500, [
                'error' => 'Errore durante la creazione del contratto: ' . $e->getMessage()
            ]);
        }
    }

    public function update($id) {
        try {
            $data = $this->getRequestData();
            $data = $this->sanitizeInput($data);

            $this->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE contracts 
                SET 
                    provider = ?,
                    activation_address = ?,
                    installation_address = ?,
                    migration_code = ?,
                    phone_number = ?,
                    status = ?
                WHERE id = ? AND created_by = ?
            ");

            $stmt->execute([
                $data['provider'],
                $data['activation_address'],
                $data['installation_address'] ?? null,
                $data['migration_code'] ?? null,
                $data['phone_number'] ?? null,
                $data['status'] ?? 'pending',
                $id,
                $this->user
            ]);

            // Gestione nuovi allegati
            if (isset($_FILES['attachments'])) {
                // ... (stesso codice della creazione per gli allegati)
            }

            $this->commit();

            $this->sendResponse(200, [
                'success' => true,
                'message' => 'Contratto aggiornato con successo'
            ]);

        } catch (Exception $e) {
            $this->rollback();
            $this->sendResponse(500, [
                'error' => 'Errore durante l\'aggiornamento del contratto: ' . $e->getMessage()
            ]);
        }
    }

    public function get($id = null) {
        try {
            if ($id) {
                $stmt = $this->db->prepare("
                    SELECT c.*, cl.first_name, cl.last_name, cl.email, cl.phone
                    FROM contracts c
                    JOIN clients cl ON c.client_id = cl.id
                    WHERE c.id = ?
                ");
                $stmt->execute([$id]);
                $contract = $stmt->fetch();

                if (!$contract) {
                    $this->sendResponse(404, ['error' => 'Contratto non trovato']);
                }

                // Recupera gli allegati
                $stmt = $this->db->prepare("
                    SELECT id, file_name, file_path, file_size 
                    FROM attachments 
                    WHERE contract_id = ?
                ");
                $stmt->execute([$id]);
                $contract['attachments'] = $stmt->fetchAll();

                $this->sendResponse(200, $contract);
            } else {
                $stmt = $this->db->prepare("
                    SELECT c.*, cl.first_name, cl.last_name
                    FROM contracts c
                    JOIN clients cl ON c.client_id = cl.id
                    ORDER BY c.created_at DESC
                    LIMIT 100
                ");
                $stmt->execute();
                $contracts = $stmt->fetchAll();

                $this->sendResponse(200, $contracts);
            }
        } catch (Exception $e) {
            $this->sendResponse(500, [
                'error' => 'Errore durante il recupero dei contratti: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($id) {
        try {
            $this->beginTransaction();

            // Elimina gli allegati
            $stmt = $this->db->prepare("
                SELECT file_path 
                FROM attachments 
                WHERE contract_id = ?
            ");
            $stmt->execute([$id]);
            $attachments = $stmt->fetchAll();

            foreach ($attachments as $attachment) {
                $filePath = __DIR__ . '/../uploads/' . $attachment['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Elimina i record degli allegati
            $stmt = $this->db->prepare("
                DELETE FROM attachments 
                WHERE contract_id = ?
            ");
            $stmt->execute([$id]);

            // Elimina il contratto
            $stmt = $this->db->prepare("
                DELETE FROM contracts 
                WHERE id = ? AND created_by = ?
            ");
            $stmt->execute([$id, $this->user]);

            $this->commit();

            $this->sendResponse(200, [
                'success' => true,
                'message' => 'Contratto eliminato con successo'
            ]);

        } catch (Exception $e) {
            $this->rollback();
            $this->sendResponse(500, [
                'error' => 'Errore durante l\'eliminazione del contratto: ' . $e->getMessage()
            ]);
        }
    }

    public function search() {
        try {
            $data = $this->getRequestData();
            $search = $data['search'] ?? '';
            $type = $data['type'] ?? '';
            $provider = $data['provider'] ?? '';
            $status = $data['status'] ?? '';

            $sql = "
                SELECT c.*, cl.first_name, cl.last_name
                FROM contracts c
                JOIN clients cl ON c.client_id = cl.id
                WHERE 1=1
            ";
            $params = [];

            if ($search) {
                $sql .= " AND (
                    cl.first_name LIKE ? OR 
                    cl.last_name LIKE ? OR
                    c.activation_address LIKE ? OR
                    c.migration_code LIKE ?
                )";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            if ($type) {
                $sql .= " AND c.contract_type = ?";
                $params[] = $type;
            }

            if ($provider) {
                $sql .= " AND c.provider = ?";
                $params[] = $provider;
            }

            if ($status) {
                $sql .= " AND c.status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY c.created_at DESC LIMIT 100";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $contracts = $stmt->fetchAll();

            $this->sendResponse(200, $contracts);

        } catch (Exception $e) {
            $this->sendResponse(500, [
                'error' => 'Errore durante la ricerca: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Ottiene un contratto specifico
     *
     * @param int $id ID del contratto
     * @return array|false I dati del contratto o false se non trovato
     */
    public function getContract($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM contracts
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logError('Errore nel recupero del contratto: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un contratto
     *
     * @param int $id ID del contratto da eliminare
     * @return bool True se l'eliminazione è riuscita, altrimenti False
     */
    public function deleteContract($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM contracts WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (Exception $e) {
            $this->logError('Errore nell\'eliminazione del contratto: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Aggiorna un contratto esistente
     *
     * @param int $id ID del contratto
     * @param array $data Dati da aggiornare
     * @return bool True se l'aggiornamento è riuscito, altrimenti False
     */
    public function updateContract($id, $data) {
        try {
            $fields = [];
            $values = [];
            
            // Costruisci la query dinamicamente in base ai campi forniti
            foreach ($data as $field => $value) {
                if (in_array($field, [
                    'client_id', 'contract_type', 'provider', 'activation_address',
                    'installation_address', 'migration_code', 'phone_number',
                    'contract_date', 'expiration_date', 'monthly_fee', 'status'
                ])) {
                    $fields[] = "$field = ?";
                    $values[] = $value;
                }
            }
            
            if (empty($fields)) {
                return false; // Nessun campo valido da aggiornare
            }
            
            $fields[] = "updated_at = NOW()";
            $values[] = $id; // Per la WHERE condition
            
            $query = "UPDATE contracts SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute($values);
        } catch (Exception $e) {
            $this->logError('Errore nell\'aggiornamento del contratto: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ottiene tutti i contratti di un cliente
     *
     * @param int $clientId ID del cliente
     * @return array Lista dei contratti
     */
    public function getClientContracts($clientId) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM contracts
                WHERE client_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$clientId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logError('Errore nel recupero dei contratti del cliente: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Registra un errore nel log
     *
     * @param string $message Messaggio di errore
     */
    private function logError($message) {
        // In un ambiente di produzione, questo potrebbe scrivere su un file di log
        // o inviare una notifica a un sistema di monitoraggio
        error_log($message);
    }
}
