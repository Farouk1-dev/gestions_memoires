<?php

use PHPUnit\Framework\TestCase;

class LikeTest extends TestCase
{

    public function testLike()
    {

        $likes = 1;

        $this->assertEquals(
            1,
            $likes
        );

    }

}