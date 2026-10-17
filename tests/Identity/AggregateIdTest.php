<?php

declare(strict_types=1);

namespace SharedKernel\Tests\Identity;

use PHPUnit\Framework\TestCase;
use SharedKernel\Identity\OrderId;
use SharedKernel\Identity\ProductId;

final class AggregateIdTest extends TestCase
{
    public function testGenerateProducesLowercaseV4Uuid(): void
    {
        self::assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/',
            OrderId::generate()->value,
        );
    }

    public function testGenerateProducesDistinctIds(): void
    {
        self::assertFalse(OrderId::generate()->equals(OrderId::generate()));
    }

    public function testFromStringLowercasesValue(): void
    {
        $id = OrderId::fromString('0F1E2D3C-4B5A-4798-9C0D-1A2B3C4D5E6F');

        self::assertSame('0f1e2d3c-4b5a-4798-9c0d-1a2b3c4d5e6f', $id->value);
    }

    public function testFromStringRejectsInvalidUuid(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        OrderId::fromString('not-a-uuid');
    }

    public function testEqualsRequiresSameTypeAndValue(): void
    {
        $id = OrderId::fromString('0f1e2d3c-4b5a-4798-9c0d-1a2b3c4d5e6f');

        self::assertTrue($id->equals(OrderId::fromString('0f1e2d3c-4b5a-4798-9c0d-1a2b3c4d5e6f')));
        self::assertFalse($id->equals(ProductId::fromString('0f1e2d3c-4b5a-4798-9c0d-1a2b3c4d5e6f')));
    }

    public function testStringCastReturnsValue(): void
    {
        $id = OrderId::fromString('0f1e2d3c-4b5a-4798-9c0d-1a2b3c4d5e6f');

        self::assertSame($id->value, (string) $id);
    }
}
