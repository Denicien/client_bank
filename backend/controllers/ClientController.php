<?php
require_once __DIR__ . '/../models/Client.php';

// ✅ AJOUTER CES EN-TÊTES CORS
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

// ✅ GÉRER LA REQUÊTE OPTIONS (préflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

$controller = new ClientController();

class ClientController {
    private $client;
    
    public function __construct() {
        global $pdo;
        $this->client = new Client($pdo);
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $input = json_decode(file_get_contents('php://input'), true);
        
        try {
            switch ($method) {
                case 'GET':
                    if (isset($_GET['id'])) {
                        $this->getClient($_GET['id']);
                    } elseif (isset($_GET['stats'])) {
                        $this->getStats();
                    } else {
                        $this->getAllClients();
                    }
                    break;
                    
                case 'POST':
                    $this->addClient($input);
                    break;
                    
                case 'PUT':
                    if (!isset($_GET['id'])) {
                        throw new Exception('ID requis');
                    }
                    $this->updateClient($_GET['id'], $input);
                    break;
                    
                case 'DELETE':
                    if (!isset($_GET['id'])) {
                        throw new Exception('ID requis');
                    }
                    $this->deleteClient($_GET['id']);
                    break;
                    
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Méthode non autorisée']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    private function getAllClients() {
        $clients = $this->client->getAll();
        echo json_encode($clients);
    }
    
    private function getClient($id) {
        $client = $this->client->getById($id);
        if ($client) {
            echo json_encode($client);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Client non trouvé']);
        }
    }
    
    private function addClient($data) {
        if (!isset($data['numCompte']) || !isset($data['nom']) || !isset($data['solde'])) {
            throw new Exception('Données incomplètes');
        }
        
        $result = $this->client->create($data['numCompte'], $data['nom'], $data['solde']);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Insertion réussie' : 'Insertion échouée'
        ]);
    }
    
    private function updateClient($id, $data) {
        if (!isset($data['numCompte']) || !isset($data['nom']) || !isset($data['solde'])) {
            throw new Exception('Données incomplètes');
        }
        
        $result = $this->client->update($id, $data['numCompte'], $data['nom'], $data['solde']);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Modification réussie' : 'Modification échouée'
        ]);
    }
    
    private function deleteClient($id) {
        $result = $this->client->delete($id);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Suppression réussie' : 'Suppression échouée'
        ]);
    }
    
    private function getStats() {
        $stats = $this->client->getStats();
        echo json_encode($stats);
    }
}
?>