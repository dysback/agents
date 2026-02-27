<?php

declare(strict_types=1);

namespace Dzelenika\Agents\Tests\Controller;

use Dzelenika\Agents\Controller\AuthController;
use Dzelenika\Agents\Model\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for AuthController::login().
 *
 * Injects the JSON body via the optional $rawBody parameter to avoid
 * reading from php://input in tests.
 */
class AuthControllerTest extends TestCase
{
    private User&MockObject $userMock;
    private AuthController $controller;

    protected function setUp(): void
    {
        $_ENV['JWT_SECRET'] = 'test-secret-that-is-at-least-32-chars!!';
        $_ENV['JWT_EXPIRY'] = '3600';

        $this->userMock   = $this->createMock(User::class);
        $this->controller = new AuthController($this->userMock);
    }

    public function testLoginReturns400ForMissingFields(): void
    {
        ob_start();
        $this->controller->login(json_encode(['username' => 'alice']) ?: '');
        $output = (string) ob_get_clean();

        $decoded = json_decode($output, true);

        self::assertSame(400, http_response_code());
        self::assertArrayHasKey('error', $decoded);
    }

    public function testLoginReturns400ForEmptyFields(): void
    {
        ob_start();
        $this->controller->login(json_encode(['username' => '  ', 'password' => 'x']) ?: '');
        $output = (string) ob_get_clean();

        $decoded = json_decode($output, true);

        self::assertSame(400, http_response_code());
        self::assertArrayHasKey('error', $decoded);
    }

    public function testLoginReturns401ForUnknownUser(): void
    {
        $this->userMock->method('findByUsername')->willReturn(null);

        ob_start();
        $this->controller->login(json_encode(['username' => 'nobody', 'password' => 'x']) ?: '');
        $output = (string) ob_get_clean();

        $decoded = json_decode($output, true);

        self::assertSame(401, http_response_code());
        self::assertSame('Invalid credentials', $decoded['error']);
    }

    public function testLoginReturns401ForWrongPassword(): void
    {
        $hash = password_hash('correct', PASSWORD_BCRYPT);
        $this->userMock->method('findByUsername')->willReturn([
            'id'       => 1,
            'username' => 'alice',
            'password' => $hash,
        ]);
        $this->userMock->method('verifyPassword')->willReturn(false);

        ob_start();
        $this->controller->login(json_encode(['username' => 'alice', 'password' => 'wrong']) ?: '');
        $output = (string) ob_get_clean();

        $decoded = json_decode($output, true);

        self::assertSame(401, http_response_code());
        self::assertSame('Invalid credentials', $decoded['error']);
    }

    public function testLoginReturns200WithTokenForValidCredentials(): void
    {
        $hash = password_hash('secret', PASSWORD_BCRYPT);
        $this->userMock->method('findByUsername')->willReturn([
            'id'       => 1,
            'username' => 'alice',
            'password' => $hash,
        ]);
        $this->userMock->method('verifyPassword')->willReturn(true);

        ob_start();
        $this->controller->login(json_encode(['username' => 'alice', 'password' => 'secret']) ?: '');
        $output = (string) ob_get_clean();

        $decoded = json_decode($output, true);

        self::assertSame(200, http_response_code());
        self::assertArrayHasKey('token', $decoded);
        self::assertNotEmpty($decoded['token']);
    }
}
