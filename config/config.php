<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'region' => env('LAZADA_REGION', 'MY'),
    'app_key' => env('LAZADA_APP_KEY'),
    'app_secret' => env('LAZADA_APP_SECRET'),
    'app_callback_url' => env('LAZADA_APP_CALLBACK_URL'),
    'seller_id' => env('LAZADA_SELLER_ID'),
    'sign_method' => env('LAZADA_SIGN_METHOD', 'sha256'),
    'authorization_url' => 'https://auth.lazada.com/oauth/authorize?response_type=code&force_auth=true&redirect_uri=:callback_url&client_id=:app_key',
    'auth_url' => 'https://auth.lazada.com/rest',
    'base_url' => [
        'MY' => 'https://api.lazada.com.my/rest',
        'VN' => 'https://api.lazada.vn/rest',
        'SG' => 'https://api.lazada.sg/rest',
        'PH' => 'https://api.lazada.com.ph/rest',
        'TH' => 'https://api.lazada.co.th/rest',
        'ID' => 'https://api.lazada.co.id/rest',
    ],
    'sandbox_mode' => env('LAZADA_SANDBOX_MODE', false),
    'log_message' => env('LAZADA_LOG_MESSAGE', false),
    'routes' => [
        'prefix' => 'lazada',
        'auth' => [
            'access_token' => 'POST /auth/token/create',
            'access_token_with_open_id' => '/auth/token/createWithOpenId',
            'refresh_token' => '/auth/token/refresh',
        ],
        'seller' => [
            'get' => '/seller/get',
            'pick_up_store_list' => '/rc/store/list/get',
        ],
        'order' => [
            'get' => '/order/get',
            'list' => '/orders/get',
            'items' => '/order/items/get',
            'document' => '/order/document/get',
        ],
        'finance' => [
            'payout_status' => '/finance/payout/status/get',
            'account_transactions' => '/finance/transaction/accountTransactions/query',
            'logistics_fee_detail' => '/lbs/slb/queryLogisticsFeeDetail',
            'transaction_detail' => '/finance/transaction/details/get',
        ],
        'product' => [
            'get' => '/products/get',
            'item' => '/product/item/get',
            'update_sellable_quantity' => 'POST /product/stock/sellable/update'
        ],
    ],
    'middleware' => ['api'],
];
