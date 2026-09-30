<?php

return [
    'secret' => env('STRIPE_SECRET'),
    'currency' => env('CASHIER_CURRENCY', 'usd'),
];