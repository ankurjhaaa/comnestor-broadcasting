# Comnestor Broadcasting

Install

composer require ankurjha/comnestor


Run installer

php artisan comnestor:install


.env configuration

BROADCAST_DRIVER=comnestor

COMNESTOR_BASE_URL=https://server.com
COMNESTOR_APP_KEY=app-key
COMNESTOR_APP_SECRET=secret


Example event

broadcast(new OrderCreated());


Example helper

ComnestorBroadcasting::send(
    "order.created",
    "orders",
    ["id"=>10]
);