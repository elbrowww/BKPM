<?php

// Token CSRF: memastikan form POST benar-benar berasal dari halaman aplikasi ini.
class Csrf
{
    private const KEY = '_csrf';

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    // Hidden input untuk ditaruh di dalam <form method="POST">
    public static function field(): string
    {
        return '<input type="hidden" name="' . self::KEY . '" value="' . htmlspecialchars(self::token()) . '">';
    }

    public static function verify(): bool
    {
        $sent = $_POST[self::KEY] ?? '';

        return is_string($sent) && $sent !== '' && hash_equals(self::token(), $sent);
    }
}
