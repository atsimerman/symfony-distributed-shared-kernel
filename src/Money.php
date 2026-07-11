<?php

declare(strict_types=1);

namespace SharedKernel;

/**
 * Money in minor units (cents) with an ISO 4217 currency code.
 *
 * The single arithmetic implementation shared by all contexts (PLAN.md §3).
 * Negative amounts are representable (arithmetic results); whether a negative
 * total is *valid* is each context's own rule, not the kernel's.
 */
final readonly class Money
{
    private function __construct(
        public int $amount,
        public string $currency,
    ) {
    }

    public static function fromMinor(int $amount, string $currency): self
    {
        if (preg_match('/^[A-Z]{3}$/', $currency) !== 1) {
            throw new \InvalidArgumentException(sprintf(
                'Currency must be a 3-letter uppercase ISO 4217 code, got "%s"',
                $currency,
            ));
        }

        return new self($amount, $currency);
    }

    public static function zero(string $currency): self
    {
        return self::fromMinor(0, $currency);
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function multiply(int $factor): self
    {
        return new self($this->amount * $factor, $this->currency);
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \DomainException(sprintf(
                'Currency mismatch: %s vs %s',
                $this->currency,
                $other->currency,
            ));
        }
    }
}
