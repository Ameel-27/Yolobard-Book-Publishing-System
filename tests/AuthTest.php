<?php
use PHPUnit\Framework\TestCase;

final class AuthTest extends TestCase
{
    public function testInvalidLogin(): void
    {
        $email = "fake@email.com";
        $password = "wrong";

        $mockDb = $this->createMock(mysqli::class);
        $mockStmt = $this->createMock(mysqli_stmt::class);

        $mockDb->expects($this->once())
               ->method('prepare')
               ->willReturn($mockStmt);

        $mockStmt->expects($this->once())
                 ->method('execute')
                 ->willReturn(true);

        $mockResult = $this->createMock(mysqli_result::class);
        $mockResult->expects($this->once())
                   ->method('fetch_assoc')
                   ->willReturn(null); 

        $mockStmt->expects($this->once())
                 ->method('get_result')
                 ->willReturn($mockResult);

        $isValid = $this->validateLogin($mockDb, $email, $password);

        $this->assertFalse($isValid, "Login should fail for invalid credentials");
    }

    private function validateLogin(mysqli $db, string $email, string $password): bool
    {
        $stmt = $db->prepare("SELECT UserID, Password FROM Users WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            return false;
        }

        return $user['Password'] === $password;
    }
}
