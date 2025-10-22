<?php
use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    public function testSum(): void
    {
        $this->assertEquals(2, 1+1);
    }
}
