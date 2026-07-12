<?php

/**
 * Minimal WordPress function stubs so Updater can be unit-tested
 * without a full WordPress bootstrap.
 */

if (! function_exists('add_filter')) {
    function add_filter(...$args): true
    {
        return true;
    }
}

if (! function_exists('add_action')) {
    function add_action(...$args): true
    {
        return true;
    }
}

if (! function_exists('get_plugin_data')) {
    function get_plugin_data(string $plugin_file, bool $markup = true, bool $translate = true): array
    {
        return [
            'Name' => 'Test Plugin',
            'Version' => '1.0.0',
        ];
    }
}

if (! function_exists('get_transient')) {
    function get_transient(string $transient): mixed
    {
        return false;
    }
}

if (! function_exists('set_transient')) {
    function set_transient(string $transient, mixed $value, int $expiration = 0): bool
    {
        return true;
    }
}

if (! function_exists('delete_transient')) {
    function delete_transient(string $transient): bool
    {
        return true;
    }
}

if (! function_exists('is_wp_error')) {
    function is_wp_error(mixed $thing): bool
    {
        return false;
    }
}

if (! function_exists('wp_remote_retrieve_response_code')) {
    function wp_remote_retrieve_response_code(mixed $response): int|string
    {
        return 200;
    }
}

if (! function_exists('wp_remote_retrieve_body')) {
    function wp_remote_retrieve_body(mixed $response): string
    {
        return '';
    }
}

if (! defined('MINUTE_IN_SECONDS')) {
    define('MINUTE_IN_SECONDS', 60);
}

if (! defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}

require __DIR__.'/../vendor/autoload.php';
