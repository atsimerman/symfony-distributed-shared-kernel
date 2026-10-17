# Symfony Distributed Shared Kernel

Shared kernel for the Symfony Distributed Lab services: aggregate identities, universal value types, and the inter-service event contracts.

## Contents

- `src/Identity/` — `OrderId`, `ProductId`, `ReservationId`, `PaymentId` (UUID wrappers)
- `src/Money.php`, `src/Quantity.php` — value objects
- `contracts/events/` — JSON Schemas for the event envelope and each event payload
- `contracts/examples/` — canonical example messages, validated against the schemas in CI

## What belongs here

Something enters the kernel only when it appears in more than one context's events.

Never here: statuses and state machines, event DTO classes (each consumer defines its own), outbox or idempotency code, HTTP contracts.

## Releasing

Versioning follows Composer's `^0.N` boundary while the version is `0.x`:

| Change | Bump |
|---|---|
| New event schema, new example fixture, new optional schema field, tests, docs | patch (`0.1.0 → 0.1.1`) |
| Removing or renaming anything public, tightening a value-object invariant | minor (`0.1.x → 0.2.0`) — every consumer must change its constraint |

Release steps:

1. Add a `CHANGELOG.md` entry.
2. Wait for CI on `main` to pass.
3. `git tag -a vX.Y.Z -m "vX.Y.Z"` and push `main` plus the tag.
4. In each consumer that should upgrade: `composer update atsimerman/symfony-distributed-shared-kernel`.

The versioning decision is recorded as ADR 0006 in the `symfony-distributed-infrastructure` repository.

## Running tests

```bash
composer install
vendor/bin/phpunit
```

Requires PHP 8.4 or newer.
