<?php
require_once __DIR__ . '/../config/database.php';

class Client {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Récupérer tous les clients
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM clients ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    // Récupérer un client par ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // Ajouter un client
    public function create($numCompte, $nom, $solde) {
        $stmt = $this->pdo->prepare("INSERT INTO clients (numCompte, nom, solde) VALUES (?, ?, ?)");
        return $stmt->execute([$numCompte, $nom, $solde]);
    }
    
    // Modifier un client
    public function update($id, $numCompte, $nom, $solde) {
        $stmt = $this->pdo->prepare("UPDATE clients SET numCompte = ?, nom = ?, solde = ? WHERE id = ?");
        return $stmt->execute([$numCompte, $nom, $solde, $id]);
    }
    
    // Supprimer un client
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM clients WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // Statistiques
    public function getStats() {
        $stmt = $this->pdo->query("SELECT 
            SUM(solde) as total, 
            MIN(solde) as min, 
            MAX(solde) as max, 
            COUNT(*) as count 
            FROM clients");
        return $stmt->fetch();
    }
}
?>