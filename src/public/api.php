<?php

declare(strict_types=1);

require '../../vendor/autoload.php';

use Dotenv\Dotenv;
use Dzelenika\Agents\Controller\AuthController;
use Dzelenika\Agents\Model\User;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$path   = trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path   = (string) preg_replace('#^api/?#', '', $path);

if ($method === 'POST' && $path === 'login') {
    (new AuthController(new User()))->login();
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Not found']);
