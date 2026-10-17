<?php

declare(strict_types=1);

namespace SharedKernel\Tests;

use PHPUnit\Framework\TestCase;
use SharedKernel\Quantity;

final class QuantityTest extends TestCase
{
    public function testFromIntExposesValue(): void
    {
        self::assertSame(1, Quantity::fromInt(1)->value);
    }

    public function testRejectsZero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Quantity::fromInt(0);
    }

    public function testRejectsNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Quantity::fromInt(-1);
    }

    public function testAddSums(): void
    {
        self::assertSame(5, Quantity::fromInt(2)->add(Quantity::fromInt(3))->value);
    }

    public function testEquals(): void
    {
        self::assertTrue(Quantity::fromInt(2)->equals(Quantity::fromInt(2)));
        self::assertFalse(Quantity::fromInt(2)->equals(Quantity::fromInt(3)));
    }
}
