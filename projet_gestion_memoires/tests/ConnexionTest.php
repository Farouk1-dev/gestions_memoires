<?php

use PHPUnit\Framework\TestCase;

class ConnexionTest extends TestCase
{

    public function testConnexionBDD()
    {

        $conn = new PDO(
            "mysql:host=localhost;dbname=gestion_memoires",
            "root",
            ""
        );

        $this->assertInstanceOf(
            PDO::class,
            $conn
        );

    }

}