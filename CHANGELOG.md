# Changelog

Versioning: while 0.x, a breaking change bumps the minor digit and an additive change bumps the patch digit (Composer's `^0.N` boundary). See README.

## 0.1.0

First tagged release.

- Identities: `OrderId`, `ProductId`, `ReservationId`, `PaymentId` (UUID, `AggregateId` base)
- Value objects: `Money` (minor units + ISO 4217), `Quantity` (strictly positive)
- Marker interfaces: `AggregateRoot`, `DomainEvent`
- Contracts: `envelope.v1.json`, `order.created.v1.json` + example fixture
- Unit tests, example-vs-schema contract test, GitHub Actions CI
