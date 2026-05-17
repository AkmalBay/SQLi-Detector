<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SQLiDefense
{
    /**
     * Kirim notifikasi ke Telegram saat serangan terdeteksi.
     */
    private function sendTelegramAlert(string $ip, string $payload, float $confidence): void
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $ownerId = env('TELEGRAM_OWNER_ID');

        if (empty($token) || empty($ownerId) || $token === 'ISI_TOKEN_BOT_ANDA_DISINI') {
            return; // Bot belum dikonfigurasi, lewati
        }

        $time = now()->setTimezone('Asia/Jakarta')->format('d-m-Y H:i:s');
        $message = "🚨 *SERANGAN SQL INJECTION TERDETEKSI!*\n\n"
            . "🕒 Waktu: `{$time} WIB`\n"
            . "🌐 IP Penyerang: `{$ip}`\n"
            . "💣 Payload: `" . substr($payload, 0, 200) . "`\n"
            . "🤖 Kepercayaan AI: `{$confidence}%`\n\n"
            . "✅ IP telah diblokir selama 24 jam.\n"
            . "🔓 Untuk unblock: /unblock {$ip}";

        try {
            Http::timeout(3)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id'    => $ownerId,
                'text'       => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Exception $e) {
            Log::error("[SQLi PREVENTION] Gagal kirim Telegram: " . $e->getMessage());
        }
    }

    public function handle(Request $request, Closure $next)
    {
        $ipAddress = $request->ip();
        $cacheKey = 'sqli_blocked_ip_' . $ipAddress;

        // 1. Cek apakah IP sudah diblokir
        if (Cache::has($cacheKey)) {
            Log::warning("[SQLi PREVENTION] IP $ipAddress mencoba akses saat masa blokir.");
            abort(403, '⛔ AKSES DITOLAK: IP Anda telah diblokir karena aktivitas mencurigakan.');
        }

        // 2. Filter & Bersihkan Input (Cek Values, Keys, dan Route Parameters)
        $inputs = $request->all();
        $routeParams = $request->route() ? $request->route()->parameters() : [];
        $allInputs = array_merge($inputs, $routeParams);
        
        $flattenedInputs = \Illuminate\Support\Arr::dot($allInputs); // Menggunakan dot untuk meratakan tanpa menghilangkan keys secara penuh
        $queriesToCheck = [];

        foreach ($flattenedInputs as $key => $value) {
            // Cek Key
            if (!empty($key) && is_string($key) && strlen($key) > 1) {
                $queriesToCheck[] = $key;
            }
            // Cek Value
            if (!empty($value) && is_string($value) && strlen($value) > 1) {
                $queriesToCheck[] = $value;
            }
        }

        // Jika tidak ada input untuk dicek, lanjutkan
        if (empty($queriesToCheck)) {
            return $next($request);
        }

        try {
            // 3. Kirim BATCH ke API Python (Sekali jalan untuk semua input)
            $response = Http::timeout(2)->post('http://127.0.0.1:8001/predict/batch', [
                'queries' => $queriesToCheck
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $isSqli = isset($result['is_sqli']) ? (bool) $result['is_sqli'] : false;

                // 4. Jika terdeteksi SQL Injection
                if ($isSqli === true) {
                    $confidence = floatval($result['confidence'] ?? 0);
                    $payload = $result['malicious_query'] ?? 'unknown';

                    // Blokir IP di cache
                    Cache::put($cacheKey, true, now()->addMinutes(1440));

                    // Log lokal
                    Log::warning("[SQLi PREVENTION] Serangan Terdeteksi & IP Diblokir!", [
                        'ip'             => $ipAddress,
                        'confidence'     => $confidence,
                        'payload_sample' => $payload,
                    ]);

                    // Kirim notifikasi Telegram (non-blocking)
                    $this->sendTelegramAlert($ipAddress, $payload, $confidence);

                    // LANGSUNG 403 Tanpa Jeda
                    abort(403, "⛔ SQLI BLOCKED! Sistem AI mendeteksi serangan SQL Injection. IP Anda diblokir sementara.");
                }
            }
        } catch (\Exception $e) {
            // Jika API mati/error, log & biarkan lewat (Fail-Open)
            Log::error("[SQLi PREVENTION] API Error: " . $e->getMessage());
        }

        // Jika semua input aman, lanjutkan request ke Controller
        return $next($request);
    }
}