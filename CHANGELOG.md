# Changelog

All notable changes to `laraditz/lazada` will be documented in this file

## 1.2.0 - 2026-10-29

### Added

- Support Laravel 13.
- `AuthService::authorizationUrl(?string $redirectUri = null, ?string $state = null)` accepts an optional redirect URI and `state`.
- `Lazada::verifyPushSignature(string $rawBody, string $authorization): bool` to verify push (webhook) signatures.
- `SellerAuthorized` event, fired after a seller's access token is obtained.
- Opt-in token encryption: `LAZADA_ENCRYPT_TOKENS` (default `false`) and the `lazada:encrypt-tokens` command. Publish and run the new migration that widens the token columns to `text` before enabling it.
- Opt-in request log controls: `LAZADA_LOG_REQUESTS` (default `true`), `LAZADA_LOG_REDACT_SECRETS` (default `false`) and `LAZADA_LOG_PRUNE_AFTER_DAYS` for pruning `LazadaMessage` with `model:prune`.

### Fixed

- After an access token or refresh token request, the seller lookup now uses the same Lazada instance (seller, sign method and sandbox mode) instead of the default one.
- Import `LazadaTokenException` in `AuthService`, so a missing seller id throws the intended exception.

## 1.1.7 - 2026-10-28

### Fixed

- Fix seller authorization failing because `authorizationUrl()` url-encoded the `redirect_uri`, which Lazada now rejects. The callback URL is now passed as-is.

### Added

- Add `AuthServiceTest` covering the generated authorization URL.
- Document in README that the callback URL must not contain a query string.

## 1.1.6 - 2026-05-09

### Changed

- Refactor Lazada `make()` function.

## 1.1.5 - 2026-04-24

### Changed

- Change migration file `2026_02_26_060930_add_seller_id_to_lazada_messages_table` `seller_id` column from `varchar` to `bigInteger`.

## 1.1.4 - 2026-04-22

### Added

- Add `home_url` config key (`LAZADA_HOME_URL`) for customising the back-button destination on the seller authorisation page; falls back to `app.url` when not set.

### Changed

- Redesign seller authorisation page (`authorized.blade.php`) with Lazada brand colours (orange, pink, blue), animated SVG success ring, copy-to-clipboard token buttons, and a back-to-home button.

### Fixed

- Fix `SellerController` using bare `Lazada` alias instead of the fully-qualified facade `Laraditz\Lazada\Facades\Lazada`.

## 1.1.3 - 2026-03-26

### Fixed

- Change `$latestResponse` property visibility from `private` to `public` in test classes (`LazadaTest`, `BaseServiceTest`, `SellerServiceTest`).

## 1.1.2 - 2026-03-26

### Fixed

- Add missing `$latestResponse` property to test classes (`LazadaTest`, `BaseServiceTest`, `SellerServiceTest`).

## 1.1.1 - 2026-03-26

### Added

- Multi-seller support: switch sellers per call using `seller_id` named argument on any service method.
- `Lazada::make(seller_id: 'X')` for fresh isolated instances (required for queues and Octane).
- `checkSeller()` resolves sellers by numeric Lazada ID or portal short code.
- `SellerService::info()` — returns the resolved `LazadaSeller` model for the current context without making an API call.
- Test infrastructure: PHPUnit configuration, model factories (`LazadaSeller`, `LazadaAccessToken`), and base `TestCase`.

### Changed

- Config key `lazada.seller_id` renamed to `lazada.seller_short_code` to match Lazada portal naming.
- `BaseService` no longer caches seller state internally; seller is resolved once via `checkSeller()` and read from `$lazada->seller`.

### Breaking Changes

- Rename `LAZADA_SELLER_ID` → `LAZADA_SELLER_SHORT_CODE` in your `.env`.
- Rename `seller_id` → `seller_short_code` in your published `config/lazada.php`.

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
