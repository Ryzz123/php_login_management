<?php

namespace FebriAnandaLubis\Belajar\PHP\MVC\Config;

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    // Tes Untuk mengecek koneksi ke database
    public function testGetConnection()
    {
        $connection = Database::getConnection();
        self::assertNotNull($connection);
    }

    // Tes mengembalikan object yang sama
    public function testGetConnectionSingleton()
    {
        $connection1 = Database::getConnection();
        $connection2 = Database::getConnection();

        // melakukan test koneksi di 2 variabel connection nilainya sama
        self::assertSame($connection1, $connection2);
    }

}
