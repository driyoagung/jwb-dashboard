<?php

return [
    'features' => [
        'customers' => (bool) env('POS_CUSTOMERS_ENABLED', false),
        'credit' => (bool) env('POS_CREDIT_ENABLED', false),
    ],
];
