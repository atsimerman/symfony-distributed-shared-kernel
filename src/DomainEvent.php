<?php

declare(strict_types=1);

namespace SharedKernel;

/**
 * Marker interface for domain events.
 *
 * Deliberately empty. Event payload shape is NOT defined here — the
 * inter-service contracts are the JSON Schemas in contracts/events/, and each
 * consumer defines its own local DTO for the events it consumes.
 */
interface DomainEvent
{
}
