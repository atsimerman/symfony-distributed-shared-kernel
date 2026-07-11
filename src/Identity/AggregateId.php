<?php

declare(strict_types=1);

namespace SharedKernel\Identity;

/**
 * Base for aggregate identities that cross context boundaries in events.
 *
 * Identities are opaque UUIDs: services store and pass them, never branch
 * logic on their internal structure (see the shared-kernel rules in PLAN.md §3).
 */
abstract readonly class AggregateId implements \Stringable
{
    final private function __construct(public string $value)
    {
    }

    final public static function fromString(string $value): static
    {
        if (!Uuid::isValid($value)) {
            throw new \InvalidArgumentException(sprintf(
                '%s is not a valid UUID for %s',
                $value,
                static::class,
            ));
        }

        return new static(strtolower($value));
    }

    final public static function generate(): static
    {
        return new static(Uuid::v4());
    }

    final public function equals(self $other): bool
    {
        // Different ID types never compare equal, even with the same UUID value.
        return $other::class === static::class && $other->value === $this->value;
    }

    final public function __toString(): string
    {
        return $this->value;
    }
}
