<?php

declare(strict_types=1);

namespace Dzelenika\Agents\Tests\Model;

use Dzelenika\Agents\Model\User;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the User model.
 *
 * Uses PDO mocks so no real database is required.
 */
class UserTest extends TestCase
{
    private PDO&MockObject $pdoMock;
    private PDOStatement&MockObject $stmtMock;
    private User $user;

    protected function setUp(): void
    {
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->pdoMock  = $this->createMock(PDO::class);
        $this->user     = new User($this->pdoMock);
    }

    public function testFindByUsernameReturnsRowWhenFound(): void
    {
        $expectedRow = [
            'id'       => 1,
            'username' => 'alice',
            'password' => password_hash('secret', PASSWORD_BCRYPT),
        ];

        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn($expectedRow);
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);

        $result = $this->user->findByUsername('alice');

        self::assertSame($expectedRow, $result);
    }

    public function testFindByUsernameReturnsNullWhenNotFound(): void
    {
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn(false);
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);

        $result = $this->user->findByUsername('nobody');

        self::assertNull($result);
    }

    public function testVerifyPasswordReturnsTrueForCorrectPassword(): void
    {
        $hash   = password_hash('correct-password', PASSWORD_BCRYPT);
        $result = $this->user->verifyPassword('correct-password', $hash);

        self::assertTrue($result);
    }

    public function testVerifyPasswordReturnsFalseForWrongPassword(): void
    {
        $hash   = password_hash('correct-password', PASSWORD_BCRYPT);
        $result = $this->user->verifyPassword('wrong-password', $hash);

        self::assertFalse($result);
    }
}
