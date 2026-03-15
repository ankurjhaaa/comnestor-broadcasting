# Comnestor Broadcasting for Laravel

Comnestor is a custom Laravel broadcasting driver that sends events to your Comnestor server using signed HTTP requests.

It supports:

- Laravel `10`, `11`, and `12`
- PHP `8.1+`
- Simple setup via Artisan installer
- Broadcasting through native Laravel `broadcast()`

---

## Installation

Install the package:

```bash
composer require ankurjha/comnestor
```

Run the installer command:

```bash
php artisan comnestor:install
```

The installer will:

1. Create `app/Services/ComnestorBroadcasting.php`
2. Ensure `config/broadcasting.php` exists
3. Add the `comnestor` connection in broadcasting config

---

## Environment Configuration

Add these values in your `.env`:

```env
BROADCAST_CONNECTION=comnestor

COMNESTOR_BASE_URL=https://comnestor.cloud
COMNESTOR_APP_KEY=app-key
COMNESTOR_APP_SECRET=secret
```

---

## Usage

### 1) Use Native Laravel Broadcasting

Create an event implementing `ShouldBroadcast`, then broadcast it:

```php
broadcast(new OrderCreated());
```

Your event should define:

- `broadcastOn()` for channels
- `broadcastAs()` for event name (optional)
- `broadcastWith()` for payload (optional)

### 2) Use Generated Helper Service

After running installer, use:

```php
use App\Services\ComnestorBroadcasting;

ComnestorBroadcasting::send(
    'order.created',
    'orders',
    ['id' => 10]
);
```

---

## How Signing Works

For each broadcast request, signature is generated using HMAC-SHA256 with:

- `app_key`
- `timestamp`
- comma-separated channels
- event name
- JSON payload

Your Comnestor backend should verify this signature before accepting the event.

---

## Troubleshooting

### Events are not reaching server

- Verify `COMNESTOR_BASE_URL` is correct and reachable
- Confirm `COMNESTOR_APP_KEY` and `COMNESTOR_APP_SECRET` match server values
- Check Laravel logs for `Comnestor broadcast failed`

### Driver not recognized

- Ensure package is installed successfully
- Run `php artisan optimize:clear`
- Re-run installer: `php artisan comnestor:install`

---

## Security Notes

- Always use `https` in production
- Keep app secret private
- Rotate keys periodically

---

## License

MIT License