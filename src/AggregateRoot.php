<?php

declare(strict_types=1);

namespace SharedKernel;

/**
 * Marker interface for aggregate roots.
 *
 * Deliberately empty: it shares the *convention* across services without
 * sharing logic (PLAN.md §3). Each service implements its own event-recording
 * mechanism (e.g. a recordEvent()/releaseEvents() trait) in its own domain layer.
 */
interface AggregateRoot
{
}
