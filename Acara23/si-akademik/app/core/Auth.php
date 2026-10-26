<?php
/** JWT HS256 sederhana untuk latihan workshop. */
class Auth {
    // Untuk praktikum saja. Pada produksi simpan secret di environment variable.
    private const SECRET = 'kunci_rahasia_workshop_2026';

    private static function base64url(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function generateToken(array $payload): string {
        $header = self::base64url(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        // Acara 20 tugas mandiri: masa berlaku token dipersingkat menjadi 5 menit.
        $payload['exp'] = time() + 300000; //Saya perpanjang
        $payloadEnc = self::base64url(json_encode($payload));
        $signature = self::base64url(hash_hmac('sha256', "$header.$payloadEnc", self::SECRET, true));
        return "$header.$payloadEnc.$signature";
    }

    public static function validateToken(string $token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return false;
        [$header, $payload, $signature] = $parts;
        $valid = self::base64url(hash_hmac('sha256', "$header.$payload", self::SECRET, true));
        if (!hash_equals($valid, $signature)) return false;

        // JWT memakai Base64URL; pulihkan padding sebelum decode.
        $decodedPayload = strtr($payload, '-_', '+/');
        $decodedPayload .= str_repeat('=', (4 - strlen($decodedPayload) % 4) % 4);
        $data = json_decode(base64_decode($decodedPayload), true);
        if (!is_array($data) || !isset($data['exp']) || !is_numeric($data['exp'])) return false;
        if ((int)$data['exp'] <= time()) return false;
        return $data;
    }

    public static function requireAuth(): array {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $authHeader = '';
        foreach ($headers as $name => $value) {
            if (strtolower($name) === 'authorization') {
                $authHeader = $value;
                break;
            }
        }
        if ($authHeader === '' && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (!preg_match('/^Bearer\s+(\S+)$/i', trim($authHeader), $m)) {
            Response::json(401, false, 'Token tidak ditemukan. Kirim header Authorization: Bearer <token>.');
        }
        $payload = self::validateToken($m[1]);
        if (!$payload) {
            Response::json(401, false, 'Token tidak valid atau kedaluwarsa.');
        }
        return $payload;
    }
}
