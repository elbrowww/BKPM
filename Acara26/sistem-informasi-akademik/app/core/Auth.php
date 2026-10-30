<?php
class Auth {
    private static function b64e(string $s): string {
        return rtrim(strtr(base64_encode($s), '+/', '-_'), '=');
    }
    private static function b64d(string $s): string {
        return base64_decode(strtr($s, '-_', '+/'));
    }

    /** Buat token bertanda tangan HMAC-SHA256 (format mirip JWT). */
    public static function generateToken(array $payload): string {
        $cfg = require __DIR__ . '/../config/config.php';
        $payload['exp'] = time() + $cfg['token_ttl'];
        $head = self::b64e(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $body = self::b64e(json_encode($payload));
        $sig  = self::b64e(hash_hmac('sha256', "$head.$body", $cfg['secret'], true));
        return "$head.$body.$sig";
    }

    private static function bearerToken(): ?string {
        $h = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? (function_exists('getallheaders') ? (getallheaders()['Authorization'] ?? null) : null);
        if ($h && preg_match('/^Bearer\s+(.+)$/i', $h, $m)) return $m[1];
        return null;
    }

    /** Wajib login. Mengembalikan payload token, atau 401. */
    public static function requireAuth(): array {
        $token = self::bearerToken();
        if (!$token) {
            Response::json(401, false, 'Token tidak ditemukan, silakan login');
        }
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            Response::json(401, false, 'Token tidak valid');
        }
        $cfg = require __DIR__ . '/../config/config.php';
        $expected = self::b64e(hash_hmac('sha256', "$parts[0].$parts[1]", $cfg['secret'], true));
        if (!hash_equals($expected, $parts[2])) {
            Response::json(401, false, 'Token tidak valid');
        }
        $payload = json_decode(self::b64d($parts[1]), true);
        if (!is_array($payload) || ($payload['exp'] ?? 0) < time()) {
            Response::json(401, false, 'Token kedaluwarsa, silakan login ulang');
        }
        return $payload;
    }

    // ===== Acara 25, Langkah 1 =====
    public static function requireAdmin(): array {
        $payload = self::requireAuth();
        if (($payload['role'] ?? '') !== 'admin') {
            Response::json(403, false, 'Akses ditolak, hanya admin yang diizinkan');
        }
        return $payload;
    }
}
