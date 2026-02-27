<?php

declare(strict_types=1);

namespace Dzelenika\Agents\Controller;

use Dzelenika\Agents\Model\User;
use Firebase\JWT\JWT;

/**
 * Handles authentication API endpoints.
 */
class AuthController
{
    /**
     * @param User $userModel The user model used for credential validation.
     */
    public function __construct(private readonly User $userModel) {}

    /**
     * Handles POST /api/login.
     *
     * Reads JSON body: {"username": "...", "password": "..."}.
     * Returns 200 {"token": "..."} on success.
     * Returns 400 {"error": "..."} for missing/empty fields.
     * Returns 401 {"error": "Invalid credentials"} for bad credentials.
     *
     * @param string|null $rawBody Raw JSON request body; reads php://input when null.
     *
     * @return void
     */
    public function login(?string $rawBody = null): void
    {
        header('Content-Type: application/json');

        $body = json_decode($rawBody ?? (string) file_get_contents('php://input'), true);

        if (!isset($body['username'], $body['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'username and password are required']);

            return;
        }

        $username = trim((string) $body['username']);
        $password = (string) $body['password'];

        if ($username === '' || $password === '') {
            http_response_code(400);
            echo json_encode(['error' => 'username and password must not be empty']);

            return;
        }

        $user = $this->userModel->findByUsername($username);

        if ($user === null || !$this->userModel->verifyPassword($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);

            return;
        }

        $now    = time();
        $expiry = (int) ($_ENV['JWT_EXPIRY'] ?? 3600);

        $payload = [
            'iss'      => 'agents-app',
            'sub'      => (string) $user['id'],
            'iat'      => $now,
            'exp'      => $now + $expiry,
            'username' => $user['username'],
        ];

        $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        http_response_code(200);
        echo json_encode(['token' => $token]);
    }
}
