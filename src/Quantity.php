<?php

declare(strict_types=1);

namespace SharedKernel;

/**
 * A strictly positive quantity — line items and stock both speak in quantities.
 *
 * Zero is deliberately not representable: "no stock" is a Stock-context state
 * (available = absent or a context-local rule), not a Quantity value.
 */
final readonly class Quantity
{
    private function __construct(public int $value)
    {
    }

    public static function fromInt(int $value): self
    {
        if ($value < 1) {
            throw new \InvalidArgumentException(sprintf(
                'Quantity must be a positive integer, got %d',
                $value,
            ));
        }

        return new self($value);
    }

    public function add(self $other): self
    {
        return new self($this->value + $other->value);
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
