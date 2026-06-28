<?php
require_once __DIR__ . '/controllers/ClientController.php';

$controller = new ClientController();
$controller->handleRequest();
?>