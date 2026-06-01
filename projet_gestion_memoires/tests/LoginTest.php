<?php

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{

    public function testPasswordHash()
    {

        $password = "123456";

        $hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $this->assertTrue(

            password_verify(
                "123456",
                $hash
            )

        );

    }

}