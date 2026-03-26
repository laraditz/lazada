# Laravel Lazada

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laraditz/lazada.svg?style=flat-square)](https://packagist.org/packages/laraditz/lazada)
[![Total Downloads](https://img.shields.io/packagist/dt/laraditz/lazada.svg?style=flat-square)](https://packagist.org/packages/laraditz/lazada)
[![License](https://img.shields.io/packagist/l/laraditz/lazada?style=flat-square)](./LICENSE.md)
![GitHub Actions](https://github.com/laraditz/lazada/actions/workflows/main.yml/badge.svg)

A comprehensive Laravel package for seamless integration with the Lazada Open Platform API. This package provides a clean, intuitive interface for managing Lazada sellers, handling authentication, processing orders, managing products, and receiving webhooks.

<a href="https://www.buymeacoffee.com/raditzfarhan" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" alt="Buy Me A Coffee" style="height: 50px !important;width: 200px !important;" ></a>

## Features

- 🔐 **Complete Authentication Flow** - Automatic token management with refresh capabilities
- 🏪 **Multi-Seller Support** - Manage multiple Lazada sellers within a single application
- 📦 **Product Management** - Retrieve products and update sellable stock quantities
- 🛒 **Order Processing** - Comprehensive order management and document retrieval
- 💰 **Finance & Transactions** - Query payout status, account transactions, and finance details
- 📡 **Webhook Integration** - Real-time push event handling with built-in endpoints
- 🗄️ **Database Logging** - Automatic request/response logging for debugging and monitoring
- 🔄 **Auto Token Refresh** - Scheduled token refresh to maintain API connectivity

## Requirements

- PHP 8.2 and above.
- Laravel 10 and above.

## Installation

You can install the package via composer:

```bash
composer require laraditz/lazada
```

## Quick Start

### 1. Lazada App Setup

Before using this package, you need a Lazada Open Platform app:

1. Visit [Lazada Open Platform](https://open.lazada.com/)
2. Create a new app or use an existing one
3. Note your `App Key` and `App Secret`
4. Configure the App Callback URL: `https://your-app-url.com/lazada/seller/authorized`

### 2. Environment Configuration

Add your Lazada credentials to your `.env` file:

```env
LAZADA_APP_KEY=your_app_key_here
LAZADA_APP_SECRET=your_app_secret_here
LAZADA_SELLER_SHORT_CODE=MYXXXXXXXX   # Optional: default seller short code (also called Seller ID in the console)
```

### 3. Publish Migration

```bash
php artisan vendor:publish --provider="Laraditz\Lazada\LazadaServiceProvider" --tag="migrations"
```

### 4. Run Migration

```bash
php artisan migrate
```

### 5. Configuration (Optional)

```bash
php artisan vendor:publish --provider="Laraditz\Lazada\LazadaServiceProvider" --tag="config"
```

### 6. Authorization Flow

To authorize a seller:

1. Generate and share the authorization URL with the seller:

```php
$url = Lazada::auth()->authorizationUrl();
// Redirect the seller to this URL
```

2. The seller logs in and authorizes your app
3. Lazada redirects to `https://your-app-url.com/lazada/seller/authorized`
4. The package automatically handles the token exchange and storage
5. The seller is now ready for API calls

## Available Methods

Here's the full list of methods available in this package. Parameters follow the [Lazada Open Platform Documentation](https://open.lazada.com/apps/doc/api) exactly. Common parameters like `app_key`, `sign`, `timestamp`, and `access_token` are added automatically.

### Authentication Service `auth()`

| Method                    | Description                                         |
| ------------------------- | --------------------------------------------------- |
| `authorizationUrl()`      | Get the authorization URL to share with the seller. |
| `accessToken()`           | Exchange an authorization code for an access token. |
| `refreshToken()`          | Refresh an access token before it expires.          |
| `accessTokenWithOpenId()` | Generate an access token using an OpenID.           |

### Seller Service `seller()`

| Method              | Description                                                       |
| ------------------- | ----------------------------------------------------------------- |
| `get()`             | Fetch seller information from Lazada API and sync to DB.          |
| `info()`            | Return the resolved `LazadaSeller` model for the current context. |
| `pickUpStoreList()` | Return the list of pick-up store information for the seller.      |

### Order Service `order()`

| Method       | Description                                                   |
| ------------ | ------------------------------------------------------------- |
| `list()`     | Get an order list from a specified date range.                |
| `get()`      | Get single order detail by order ID.                          |
| `items()`    | Get the item information of an order.                         |
| `document()` | Retrieve order-related documents (invoices, shipping labels). |

### Finance Service `finance()`

| Method                  | Description                                                    |
| ----------------------- | -------------------------------------------------------------- |
| `payoutStatus()`        | Get transaction statements created after the provided date.    |
| `accountTransactions()` | Query account transactions.                                    |
| `logisticsFeeDetail()`  | Query logistics fee details.                                   |
| `transactionDetail()`   | Query seller transaction details within a specific date range. |

### Product Service `product()`

| Method                     | Description                               |
| -------------------------- | ----------------------------------------- |
| `get()`                    | Get the product list for the seller.      |
| `item()`                   | Get a single product item by item ID.     |
| `updateSellableQuantity()` | Update sellable stock quantity for a SKU. |

## Usage Examples

### Basic Usage

```php
use Laraditz\Lazada\Facades\Lazada;

// Using facade (recommended)
Lazada::order()->list(created_after: '2023-11-17T00:00:00+08:00');

// Using service container
app('lazada')->order()->list(created_after: '2023-11-17T00:00:00+08:00');
```

### Authentication

```php
use Laraditz\Lazada\Facades\Lazada;
use Laraditz\Lazada\Exceptions\LazadaAPIError;

// Step 1: Get the authorization URL and redirect the seller to it
$url = Lazada::auth()->authorizationUrl();

// Step 2: After the seller authorizes, exchange the code for a token
// (The package handles this automatically via the built-in callback route)
// If using a custom callback, call this manually:
try {
    $token = Lazada::auth()->accessToken(code: '0_123456_XxxXXXXxxXXxxXXXXxxxxxxXXXXxx');
} catch (LazadaAPIError $e) {
    logger()->error('Lazada token error', [
        'message' => $e->getMessage(),
        'code'    => $e->getMessageCode(),
        'request' => $e->getRequestId(),
    ]);
}
```

### Working with Orders

```php
// Get orders created after a date
$orders = Lazada::order()->list(created_after: '2023-11-17T00:00:00+08:00');

// Get a specific order
$order = Lazada::order()->get(order_id: '16090');

// Get items in an order
$items = Lazada::order()->items(order_id: '16090');

// Get order documents (invoice, shipping label)
$document = Lazada::order()->document(
    order_item_ids: [1234567, 1234568],
    doc_type: 'invoice',
);
```

### Working with Products

```php
// Get product list
$products = Lazada::product()->get(
    filter: 'live',
    offset: 0,
    limit: 50,
);

// Get a specific product item
$item = Lazada::product()->item(item_id: 123456789);

// Update sellable stock quantity for a SKU
Lazada::product()->updateSellableQuantity(
    payload: json_encode([
        'skuId' => 'your-sku-id',
        'sellableQuantity' => 100,
    ])
);
```

### Working with Finance

```php
// Get payout status
$payout = Lazada::finance()->payoutStatus(
    created_after: '2023-01-01T00:00:00+08:00',
);

// Query account transactions
$transactions = Lazada::finance()->accountTransactions(
    start_time: '2023-11-01T00:00:00+08:00',
    end_time: '2023-11-30T23:59:59+08:00',
);

// Query transaction details
$details = Lazada::finance()->transactionDetail(
    trans_type: 3,
    start_time: '2023-11-01T00:00:00+08:00',
    end_time: '2023-11-30T23:59:59+08:00',
);
```

### Working with Sellers

```php
// Sync seller info from Lazada API to DB
Lazada::seller()->get();

// Get the resolved seller model (no API call)
$seller = Lazada::seller()->info();
echo $seller->name;
echo $seller->short_code;

// Get pick-up store list
$stores = Lazada::seller()->pickUpStoreList();
```

### Multi-Seller Support

By default the package uses `LAZADA_SELLER_SHORT_CODE` from your `.env`. For multi-seller applications, specify the seller per request:

```php
// Method 1: Using make() — reuse the same instance for multiple calls
$lazada = Lazada::make(seller_id: 'MYXXXXXXXX');
$orders   = $lazada->order()->list(created_after: '2023-11-17T00:00:00+08:00');
$products = $lazada->product()->get(filter: 'live', offset: 0, limit: 50);

// Method 2: Pass seller_id on any service call to switch sellers inline
Lazada::order(seller_id: 'MYXXXXXXXX')->list(created_after: '2023-11-17T00:00:00+08:00');
```

### Error Handling

```php
use Laraditz\Lazada\Facades\Lazada;
use Laraditz\Lazada\Exceptions\LazadaAPIError;

try {
    $orders = Lazada::order()->list(created_after: '2023-11-17T00:00:00+08:00');
} catch (LazadaAPIError $e) {
    // Handle Lazada API errors
    logger()->error('Lazada API Error', [
        'message'    => $e->getMessage(),
        'code'       => $e->getMessageCode(),
        'request_id' => $e->getRequestId(),
        'result'     => $e->getResult(), // raw response
    ]);
} catch (\Throwable $th) {
    throw $th;
}
```

## Events & Webhooks

### Webhook URL

Configure this URL in your Lazada Open Platform App Management under the **Push Mechanism** section so Lazada pushes content updates to your app:

```
https://your-app-url.com/lazada/webhooks
```

### Available Events

| Event                                    | Description                             |
| ---------------------------------------- | --------------------------------------- |
| `Laraditz\Lazada\Events\WebPushReceived` | Triggered when Lazada sends a web push. |

### Creating Event Listeners

```php
// app/Listeners/LazadaWebPushListener.php

namespace App\Listeners;

use Laraditz\Lazada\Events\WebPushReceived;

class LazadaWebPushListener
{
    public function handle(WebPushReceived $event): void
    {
        $pushType = $event->pushType;
        $data     = $event->data;

        // Handle the push content based on type
    }
}
```

Register the listener in your `EventServiceProvider` (Laravel 10 and below):

```php
// app/Providers/EventServiceProvider.php

protected $listen = [
    \Laraditz\Lazada\Events\WebPushReceived::class => [
        \App\Listeners\LazadaWebPushListener::class,
    ],
];
```

Read more about the Lazada Push Mechanism (LPM) in the [official documentation](https://open.lazada.com/apps/doc/doc?nodeId=29526&docId=120168).

## Token Management

### Artisan Commands

```bash
# Refresh existing access tokens before they expire
php artisan lazada:refresh-token

# Remove expired access tokens from the database
php artisan lazada:flush-expired-token
```

### Automated Token Refresh

Set up scheduled token refresh in your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Refresh tokens daily (live access tokens expire in 30 days)
    $schedule->command('lazada:refresh-token')
             ->daily()
             ->withoutOverlapping();

    // Clean up expired tokens weekly
    $schedule->command('lazada:flush-expired-token')
             ->weekly();
}
```

### Token Lifecycle

| Environment | Token Type    | Duration |
| ----------- | ------------- | -------- |
| **Live**    | Access Token  | 30 days  |
| **Live**    | Refresh Token | 180 days |
| **Testing** | Access Token  | 7 days   |
| **Testing** | Refresh Token | 30 days  |

> **Important:** If both tokens expire, the seller must re-authorize your app to generate a new token pair.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about recent changes.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security vulnerabilities, please email [raditzfarhan@gmail.com](mailto:raditzfarhan@gmail.com) instead of using the issue tracker.

## Support

- 📖 [Lazada Open Platform Documentation](https://open.lazada.com/apps/doc/api)
- 🐛 [Issue Tracker](https://github.com/laraditz/lazada/issues)

## Credits

- [Raditz Farhan](https://github.com/laraditz) - Creator and maintainer
- [All Contributors](../../contributors) - Thank you for your contributions!

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
