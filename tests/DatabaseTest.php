<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../backend/lib/Database.php';

final class DatabaseTest extends TestCase
{
    public function testDatabaseConnection(): void
    {
        $db = Database::getInstance()->getConnection();

        $this->assertNotNull($db, 'Database connection should not be null');

        $this->assertInstanceOf(mysqli::class, $db, 'Database connection should be an instance of mysqli');

        $this->assertTrue($db->ping(), 'Database connection should be alive');
    }
}
