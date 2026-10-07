<?php

// Logging: detail error (teknis) disimpan ke storage/logs/app.log.
// Pengguna TIDAK pernah melihat isi log ini; mereka hanya menerima pesan yang aman.
class Logger
{
    public static function error(string $message, ?Throwable $e = null, array $context = []): void
    {
        self::write('ERROR', $message, $e, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, null, $context);
    }

    public static function path(): string
    {
        return ROOT_PATH . '/storage/logs/app.log';
    }

    private static function write(string $level, string $message, ?Throwable $e, array $context): void
    {
        $line = sprintf('[%s] %s: %s', date('Y-m-d H:i:s'), $level, $message);

        if ($e !== null) {
            $line .= sprintf(' | %s: %s (%s:%d)', get_class($e), $e->getMessage(), basename($e->getFile()), $e->getLine());
        }

        if ($context) {
            $line .= ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $dir = dirname(self::path());
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        // Jika log sendiri gagal ditulis, jangan sampai aplikasi ikut error
        @file_put_contents(self::path(), $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
