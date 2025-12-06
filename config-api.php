<?php
header('Content-Type: application/json');

require_once 'auth-check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

$action = $input['action'] ?? '';

require_once 'config-helpers.php';
require_once 'config-actions.php';

$config = loadConfig();

try {
    handleAction($action, $config, $input);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
