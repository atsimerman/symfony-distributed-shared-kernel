<?php

declare(strict_types=1);

namespace SharedKernel;

/**
 * A strictly positive amount of a product — on an order line item or a
 * reservation line.
 *
 * NOT a stock level: stock can be zero and is decremented, so Inventory owns
 * its own StockLevel value object (PLAN.md §3–4). Zero is deliberately not
 * representable here.
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
