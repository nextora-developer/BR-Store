<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HitPay Payment Gateway 
    |--------------------------------------------------------------------------
    */

    'hitpay' => [
        'url'          => env('HITPAY_API_URL'),
        'api_key'      => env('HITPAY_API_KEY'),
        'salt'         => env('HITPAY_SALT'),
        'webhook_salt' => env('HITPAY_WEBHOOK_SALT'),
        'currency' => env('HITPAY_CURRENCY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Revenue Monster Payment Gateway 
    |--------------------------------------------------------------------------
    */

    'rm' => [
        'api_base'     => env('RM_API_BASE'),
        'store_id'     => env('RM_STORE_ID'),
        'api_key'      => env('RM_API_KEY'),
        'private_key'  => env('RM_PRIVATE_KEY'),
        'public_key'   => env('RM_PUBLIC_KEY'),
        'return_url'   => env('RM_RETURN_URL'),
        'webhook_url'  => env('RM_WEBHOOK_URL'),
    ],



];
