# Changelog

All notable changes to `laraditz/lazada` will be documented in this file

## 1.1.0 - 2026-03-26

### Added

- Add `ProductService` with product catalog and SKU management APIs.
- Add `FinanceService` with transaction and payout status APIs.
- Add `LazadaProduct`, `LazadaProductSku`, and `LazadaFinanceDetail` models and corresponding tables.
- Add `lazada_finance_details` table and `afterTransactionDetailRequest` hook on `FinanceService` to auto-persist finance records.
- Add get order document API.
- Add `seller_id` to `lazada_messages` table.
- Add support for Laravel 11.

### Fixed

- Fix seller mismatch by resolving seller via `short_code` instead of numeric ID.
- Fix bug on `setAppCallbackUrl`.

### Changed

- Enhance `BaseService` and `OrderService` to utilize `seller_id` and improve error handling.

## 1.0.1 - 2023-12-01

### Added

- Add `lazada:refresh-token` and `lazada:flush-expired-token` commands.

## 1.0.0 - 2023-11-28

- Initial release

### Added

- Add `Auth`, `Finance`, `Order` and `Seller` services.
- Add `WebPushReceived` event.
- Add `Seller` and `Webhook` controllers.
- Add `LazadaAccessToken`, `LazadaMessage`, `LazadaOrder`, `LazadaReverseOrder`, `LazadaSeller` model and corresponding tables.
- Add `ActiveStatus`, `Affirmative` and `WebPushType` enums.
