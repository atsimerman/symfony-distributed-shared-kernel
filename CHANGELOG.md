# Changelog

Versioning: while 0.x, a breaking change bumps the minor digit and an additive change bumps the patch digit (Composer's `^0.N` boundary). See README.

## 0.2.2

Additive only; services can stay on `^0.2`.

- Contracts: payload schemas + example fixtures for `inventory.reserved`, `inventory.reservation_failed` and `inventory.stock_changed` (v1). `inventory.reservation_released` follows with the Phase 3 compensation work.

## 0.2.1

No change to behaviour or public signatures; services can stay on `^0.2`.

- Quality harness: PHPStan level 10 (no baseline), Rector, PHP-CS-Fixer, and a `composer qa` script. CI runs it on PHP 8.4.
- Formatting in `src/`: empty constructor bodies and empty classes on one line, typed class constants.
- Contract test: fixtures decode to typed objects; a non-object fixture fails with a clear message.

## 0.2.0

**Breaking.**

- Removed marker interfaces `SharedKernel\AggregateRoot` and `SharedKernel\DomainEvent`. They never cross a context boundary; each service defines its own (e.g. order-service's `App\Domain\Shared\*`). Upgrade: replace the imports, then require `^0.2`.

## 0.1.0

First tagged release.

- Identities: `OrderId`, `ProductId`, `ReservationId`, `PaymentId` (UUID, `AggregateId` base)
- Value objects: `Money` (minor units + ISO 4217), `Quantity` (strictly positive)
- Marker interfaces: `AggregateRoot`, `DomainEvent`
- Contracts: `envelope.v1.json`, `order.created.v1.json` + example fixture
- Unit tests, example-vs-schema contract test, GitHub Actions CI
