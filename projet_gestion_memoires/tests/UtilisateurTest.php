<?php

use PHPUnit\Framework\TestCase;

class UtilisateurTest extends TestCase
{

    public function testNomUtilisateur()
    {

        $nom = "Farouk";

        $this->assertEquals(
            "Farouk",
            $nom
        );

    }

}