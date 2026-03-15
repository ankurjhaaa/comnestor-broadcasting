# Comnestor Broadcasting Driver

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ankurjha/comnestor.svg?style=flat-square)](https://packagist.org/packages/ankurjha/comnestor)
[![Total Downloads](https://img.shields.io/packagist/dt/ankurjha/comnestor.svg?style=flat-square)](https://packagist.org/packages/ankurjha/comnestor)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Health Score](https://img.shields.io/badge/Health%20Score-A-brightgreen.svg?style=flat-square)](https://packagist.org/packages/ankurjha/comnestor)
[![Tests](https://img.shields.io/badge/Tests-Not%20Configured-lightgrey.svg?style=flat-square)](#troubleshooting)

Comnestor Broadcasting Driver is a Laravel broadcasting driver for sending realtime events through Comnestor websocket infrastructure.

With this package, your Laravel app can broadcast events using the standard `ShouldBroadcast` pattern. The `comnestor` driver forwards those events to the Comnestor API, and Comnestor delivers them to websocket clients.

Package name: `ankurjha/comnestor`

> Note: The tests badge is currently a placeholder. Once CI is added, replace it with your real workflow status badge.

---

## Table of Contents

- [Key Features](#key-features)
- [Requirements](#requirements)
- [Quick Start](#quick-start)
- [Step 1: Install via Composer](#step-1-install-via-composer)
- [Step 2: Run Installer Command](#step-2-run-installer-command)
- [Step 3: Configure Environment Variables](#step-3-configure-environment-variables)
- [Step 4: Verify Broadcasting Configuration](#step-4-verify-broadcasting-configuration)
- [Step 5: Create a Broadcast Event](#step-5-create-a-broadcast-event)
- [Step 6: Trigger the Event](#step-6-trigger-the-event)
- [Step 7: Listen from JavaScript](#step-7-listen-from-javascript)
- [How Realtime Broadcasting Works](#how-realtime-broadcasting-works)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## Key Features

- Custom Laravel broadcasting driver named `comnestor`
- Uses native Laravel broadcasting with `ShouldBroadcast`
- Signed API requests from Laravel to Comnestor
- Easy setup with `php artisan comnestor:install`
- Works with Laravel broadcasting conventions similar to Pusher, Ably, and Reverb

---

## Requirements

- PHP `^8.1`
- Laravel `10`, `11`, or `12`

---

## Quick Start

```bash
composer require ankurjha/comnestor
php artisan comnestor:install
```

Then set these values in `.env`:

```env
BROADCAST_CONNECTION=comnestor

COMNESTOR_BASE_URL=https://www.comnestor.cloud
COMNESTOR_APP_KEY=your-app-key
COMNESTOR_APP_SECRET=your-app-secret
```

You can now dispatch Laravel broadcast events normally.

---

## Step 1: Install via Composer

Install the package in your Laravel app:

```bash
composer require ankurjha/comnestor
```

---

## Step 2: Run Installer Command

Run the package installer:

```bash
php artisan comnestor:install
```

This command will:

1. Create `app/Services/ComnestorBroadcasting.php`
2. Ensure `config/broadcasting.php` exists
3. Add a `comnestor` connection in broadcasting config

---

## Step 3: Configure Environment Variables

Add or update these variables in your `.env` file:

```env
BROADCAST_CONNECTION=comnestor

COMNESTOR_BASE_URL=https://www.comnestor.cloud
COMNESTOR_APP_KEY=your-app-key
COMNESTOR_APP_SECRET=your-app-secret
```

Variable reference:

- `BROADCAST_CONNECTION`: selects the active Laravel broadcast driver
- `COMNESTOR_BASE_URL`: Comnestor API base URL (use `https` in production)
- `COMNESTOR_APP_KEY`: public identifier for your app
- `COMNESTOR_APP_SECRET`: private secret used to sign API requests

---

## Step 4: Verify Broadcasting Configuration

After installation, `config/broadcasting.php` should include this connection:

```php
'connections' => [

        'comnestor' => [
                'driver' => 'comnestor',
                'base_url' => env('COMNESTOR_BASE_URL'),
                'app_key' => env('COMNESTOR_APP_KEY'),
                'app_secret' => env('COMNESTOR_APP_SECRET'),
        ],

],
```

If you update environment variables, clear cached config:

```bash
php artisan optimize:clear
```

---

## Step 5: Create a Broadcast Event

Create an event implementing `ShouldBroadcast`.

```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreated implements ShouldBroadcast
{
        use Dispatchable, InteractsWithSockets, SerializesModels;

        public function __construct(public array $order)
        {
        }

        public function broadcastOn(): array
        {
                return [new Channel('orders')];
        }

        public function broadcastAs(): string
        {
                return 'order.created';
        }

        public function broadcastWith(): array
        {
                return ['order' => $this->order];
        }
}
```

Tip for new Laravel developers:

- `broadcastOn()` defines the channel
- `broadcastAs()` defines the client-facing event name
- `broadcastWith()` defines the payload sent to clients

---

## Step 6: Trigger the Event

Use a route to quickly test event delivery:

```php
use App\Events\OrderCreated;
use Illuminate\Support\Facades\Route;

Route::get('/broadcast-test', function () {
        broadcast(new OrderCreated([
                'id' => 101,
                'status' => 'created',
                'total' => 499.99,
        ]));

        return response()->json([
                'message' => 'Broadcast event sent successfully.',
        ]);
});
```

Open `/broadcast-test` in your browser or API client to dispatch one event.

---

## Step 7: Listen from JavaScript

Comnestor provides a lightweight JavaScript client that allows frontend applications to listen for realtime events easily.

Include the Comnestor CDN in your page:

```html
<div id="order-events"></div>

<script src="https://comnestor.cloud/cdn/comnestor.js"></script>

<script>

    const socket = Comnestor.connect("APP_KEY", {
        host: "www.comnestor.cloud"
    })

    const channel = socket.subscribe("orders")

    channel.listen("order.created", (data) => {

        const div = document.createElement("div")

        div.className = "p-3 bg-green-100 border rounded"

        div.innerHTML = `
            <b>New Order Received</b><br>
            Order ID: ${data.order.id}<br>
            Status: ${data.order.status}<br>
            Total: ${data.order.total}
        `

        document.getElementById("order-events").prepend(div)

    })

</script>
```

---

## How Realtime Broadcasting Works

1. Laravel dispatches an event implementing `ShouldBroadcast`
2. Laravel resolves the active driver (`comnestor`)
3. This package signs and sends the payload to Comnestor API
4. Comnestor validates the request and emits to websocket channels
5. Subscribed clients receive the event in realtime

---

## Troubleshooting

### Driver is not recognized

- Confirm package installation completed without errors
- Run `php artisan optimize:clear`
- Re-run installer: `php artisan comnestor:install`

### Events are not reaching Comnestor

- Ensure `BROADCAST_CONNECTION=comnestor` is set
- Verify `COMNESTOR_BASE_URL`, `COMNESTOR_APP_KEY`, `COMNESTOR_APP_SECRET`
- Confirm your Laravel server can access the Comnestor API URL
- Check application logs for `Comnestor broadcast failed`

### Websocket client does not receive events

- Verify the client subscribed to the same channel name
- Verify event name matches `broadcastAs()`
- Verify websocket URL and protocol (`ws://` or `wss://`)

### Configuration changes are not applied

- Run `php artisan optimize:clear`
- Restart your queue workers or app runtime if needed

---

## License

This package is open-sourced software licensed under the MIT license.