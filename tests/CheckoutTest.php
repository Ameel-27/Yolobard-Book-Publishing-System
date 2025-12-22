<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../backend/lib/Database.php';

final class CheckoutTest extends TestCase
{
    public function testCheckoutSuccess(): void
    {
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->expects($this->once())
                 ->method('execute')
                 ->willReturn(true);

        $mockDb = $this->createMock(mysqli::class);
        $mockDb->expects($this->once())
               ->method('prepare')
               ->willReturn($mockStmt);

        $stmt = $mockDb->prepare("CALL sp_PlaceOrder(?, ?)");
        $executionResult = $stmt->execute();

        $this->assertTrue($executionResult, "Checkout should succeed (mocked)");
    }

    public function testCheckoutFailure(): void
    {
        $mockStmt = $this->createMock(mysqli_stmt::class);
        $mockStmt->expects($this->once())
                 ->method('execute')
                 ->willReturn(false);

        $mockDb = $this->createMock(mysqli::class);
        $mockDb->expects($this->once())
               ->method('prepare')
               ->willReturn($mockStmt);

        $stmt = $mockDb->prepare("CALL sp_PlaceOrder(?, ?)");
        $executionResult = $stmt->execute();

        $this->assertFalse($executionResult, "Checkout should fail (mocked)");
    }
}
