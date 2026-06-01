<?php

use PHPUnit\Framework\TestCase;

class SoumissionTest extends TestCase
{

    public function testTitreMemoire()
    {

        $titre = "Gestion des mémoires";

        $this->assertNotEmpty(
            $titre
        );

    }

}