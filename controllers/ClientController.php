<?php
require_once __DIR__ . '/../classes/ApiController.php';

class ClientController extends ApiController {
    /**
     * Crea un nuovo cliente
     *
     * @param array $data Dati del cliente
     * @return int|false ID del cliente creato o false in caso di errore
     */
    public function create($data) {
        try {
            $this->validateRequired($data, [
                'first_name',
                'last_name',
                'email'
            ]);

            $data = $this->sanitizeInput($data);

            // Verifica se l'email è già in uso
            if (!empty($data['email'])) {
                $stmt = $this->db->prepare("SELECT id FROM clients WHERE email = ?");
                $stmt->execute([$data['email']]);
                if ($stmt->rowCount() > 0) {
                    throw new Exception("Email già in uso da un altro cliente");
                }
            }

            $this->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO clients (
                    user_id,
                    first_name,
                    last_name,
                    email,
                    phone,
                    fiscal_code,
                    vat_number,
                    address,
                    city,
                    postal_code,
                    province,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $_SESSION['user_id'], // Associa il cliente all'utente corrente
                $data['first_name'],
                $data['last_name'],
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['fiscal_code'] ?? null,
                $data['vat_number'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['postal_code'] ?? null,
                $data['province'] ?? null,
                $data['status'] ?? 1
            ]);

            if (!$result) {
                $this->rollback();
                return false;
            }

            $clientId = $this->db->lastInsertId();
            $this->commit();

            return $clientId;
        } catch (Exception $e) {
            $this->rollback();
            $this->logError('Errore nella creazione del cliente: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ottiene un cliente specifico
     *
     * @param int $id ID del cliente
     * @return array|false I dati del cliente o false se non trovato
     */
    public function getClient($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM clients
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logError('Errore nel recupero del cliente: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Aggiorna un cliente esistente
     *
     * @param int $id ID del cliente
     * @param array $data Dati da aggiornare
     * @return bool True se l'aggiornamento è riuscito, altrimenti False
     */
    public function updateClient($id, $data) {
        try {
            // Verifica che il cliente esista
            $client = $this->getClient($id);
            if (!$client) {
                return false;
            }

            // Verifica se l'email è già in uso da un altro cliente
            if (!empty($data['email']) && $data['email'] !== $client['email']) {
                $stmt = $this->db->prepare("SELECT id FROM clients WHERE email = ? AND id != ?");
                $stmt->execute([$data['email'], $id]);
                if ($stmt->rowCount() > 0) {
                    throw new Exception("Email già in uso da un altro cliente");
                }
            }

            $fields = [];
            $values = [];
            
            // Costruisci la query dinamicamente in base ai campi forniti
            foreach ($data as $field => $value) {
                if (in_array($field, [
                    'first_name', 'last_name', 'email', 'phone',
                    'fiscal_code', 'vat_number', 'address', 'city',
                    'postal_code', 'province', 'status'
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
            
            $query = "UPDATE clients SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute($values);
        } catch (Exception $e) {
            $this->logError('Errore nell\'aggiornamento del cliente: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un cliente
     *
     * @param int $id ID del cliente da eliminare
     * @return bool True se l'eliminazione è riuscita, altrimenti False
     */
    public function deleteClient($id) {
        try {
            // Verifica che il cliente esista e sia di proprietà dell'utente corrente
            $stmt = $this->db->prepare("SELECT id FROM clients WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $_SESSION['user_id']]);
            if ($stmt->rowCount() === 0 && $_SESSION['user_role'] !== 'admin') {
                return false; // Cliente non trovato o non autorizzato
            }

            $this->beginTransaction();

            // Elimina prima tutti gli allegati associati ai contratti del cliente
            $stmt = $this->db->prepare("
                DELETE FROM attachments 
                WHERE client_id = ? OR contract_id IN (SELECT id FROM contracts WHERE client_id = ?)
            ");
            $stmt->execute([$id, $id]);

            // Elimina tutti i contratti del cliente
            $stmt = $this->db->prepare("DELETE FROM contracts WHERE client_id = ?");
            $stmt->execute([$id]);

            // Elimina tutte le note del cliente
            $stmt = $this->db->prepare("DELETE FROM client_notes WHERE client_id = ?");
            $stmt->execute([$id]);

            // Elimina il cliente
            $stmt = $this->db->prepare("DELETE FROM clients WHERE id = ?");
            $result = $stmt->execute([$id]);

            if ($result) {
                $this->commit();
                return true;
            } else {
                $this->rollback();
                return false;
            }
        } catch (Exception $e) {
            $this->rollback();
            $this->logError('Errore nell\'eliminazione del cliente: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ottiene tutti i clienti dell'utente corrente
     *
     * @param array $filters Filtri opzionali (search, limit, offset)
     * @return array Lista dei clienti
     */
    public function getClients($filters = []) {
        try {
            $params = [];
            $conditions = [];
            
            // Se non è admin, mostra solo i clienti dell'utente corrente
            if ($_SESSION['user_role'] !== 'admin') {
                $conditions[] = "user_id = ?";
                $params[] = $_SESSION['user_id'];
            }
            
            // Filtro di ricerca
            if (!empty($filters['search'])) {
                $searchTerm = '%' . $filters['search'] . '%';
                $conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
                array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            }
            
            $whereClause = empty($conditions) ? "" : "WHERE " . implode(' AND ', $conditions);
            
            // Query base
            $query = "
                SELECT * FROM clients
                $whereClause
                ORDER BY created_at DESC
            ";
            
            // Paginazione
            if (isset($filters['limit']) && is_numeric($filters['limit'])) {
                $query .= " LIMIT ?";
                $params[] = (int)$filters['limit'];
                
                if (isset($filters['offset']) && is_numeric($filters['offset'])) {
                    $query .= " OFFSET ?";
                    $params[] = (int)$filters['offset'];
                }
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logError('Errore nel recupero dei clienti: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Conta il numero totale di clienti (utile per la paginazione)
     *
     * @param array $filters Filtri opzionali (search)
     * @return int Numero totale di clienti
     */
    public function countClients($filters = []) {
        try {
            $params = [];
            $conditions = [];
            
            // Se non è admin, conta solo i clienti dell'utente corrente
            if ($_SESSION['user_role'] !== 'admin') {
                $conditions[] = "user_id = ?";
                $params[] = $_SESSION['user_id'];
            }
            
            // Filtro di ricerca
            if (!empty($filters['search'])) {
                $searchTerm = '%' . $filters['search'] . '%';
                $conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
                array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
            }
            
            $whereClause = empty($conditions) ? "" : "WHERE " . implode(' AND ', $conditions);
            
            $query = "SELECT COUNT(*) as total FROM clients $whereClause";
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (Exception $e) {
            $this->logError('Errore nel conteggio dei clienti: ' . $e->getMessage());
            return 0;
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
