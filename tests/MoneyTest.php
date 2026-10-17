<?php

declare(strict_types=1);

namespace SharedKernel\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SharedKernel\Money;

final class MoneyTest extends TestCase
{
    public function testFromMinorExposesAmountAndCurrency(): void
    {
        $money = Money::fromMinor(1999, 'EUR');

        self::assertSame(1999, $money->amount);
        self::assertSame('EUR', $money->currency);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidCurrencies(): iterable
    {
        yield 'lowercase' => ['eur'];
        yield 'too short' => ['EU'];
        yield 'too long' => ['EURO'];
    }

    #[DataProvider('invalidCurrencies')]
    public function testRejectsNonIsoCurrencyCodes(string $currency): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Money::fromMinor(100, $currency);
    }

    public function testZeroHasAmountZero(): void
    {
        self::assertSame(0, Money::zero('EUR')->amount);
    }

    public function testAddSumsSameCurrency(): void
    {
        $sum = Money::fromMinor(100, 'EUR')->add(Money::fromMinor(250, 'EUR'));

        self::assertTrue($sum->equals(Money::fromMinor(350, 'EUR')));
    }

    public function testAddRejectsDifferentCurrency(): void
    {
        $this->expectException(\DomainException::class);

        Money::fromMinor(100, 'EUR')->add(Money::fromMinor(100, 'USD'));
    }

    public function testSubtractCanGoNegative(): void
    {
        $difference = Money::fromMinor(100, 'EUR')->subtract(Money::fromMinor(250, 'EUR'));

        self::assertSame(-150, $difference->amount);
        self::assertTrue($difference->isNegative());
    }

    public function testZeroIsNotNegative(): void
    {
        self::assertFalse(Money::zero('EUR')->isNegative());
    }

    public function testSubtractRejectsDifferentCurrency(): void
    {
        $this->expectException(\DomainException::class);

        Money::fromMinor(100, 'EUR')->subtract(Money::fromMinor(100, 'USD'));
    }

    public function testMultiplyScalesAmountAndKeepsCurrency(): void
    {
        $product = Money::fromMinor(1999, 'EUR')->multiply(3);

        self::assertSame(5997, $product->amount);
        self::assertSame('EUR', $product->currency);
    }

    public function testEqualsRequiresSameAmountAndCurrency(): void
    {
        $base = Money::fromMinor(100, 'EUR');

        self::assertTrue($base->equals(Money::fromMinor(100, 'EUR')));
        self::assertFalse($base->equals(Money::fromMinor(101, 'EUR')));
        self::assertFalse($base->equals(Money::fromMinor(100, 'USD')));
    }
}
