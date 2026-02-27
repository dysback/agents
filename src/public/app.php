<?php

declare(strict_types=1);

require '../../vendor/autoload.php';

use Dotenv\Dotenv;
use Dzelenika\Agents\Pages\HomePage;
use Dzelenika\Agents\Pages\LoginPage;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$path = trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

if ($path === '') {
    $path = 'home';
}

$mustache = new Mustache_Engine([
    'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/../../pages/templates'),
]);

switch ($path) {
    case 'home':
        (new HomePage($mustache))->render();
        break;

    case 'login':
        (new LoginPage($mustache))->render();
        break;

    default:
        http_response_code(404);
        echo '404 Not Found';
        break;
}
