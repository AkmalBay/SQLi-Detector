<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Content Security Policy (CSP)
        // Mengizinkan source internal, Google Fonts, dan CDN yang umum digunakan
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://unpkg.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.tailwindcss.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; " .
               "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self'; " .
               "frame-ancestors 'self';";
        
        $response->headers->set('Content-Security-Policy', $csp);

        // 2. Clickjacking Protection
        // Mencegah situs ditampilkan di dalam iframe dari domain lain
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 3. MIME Type Confusion (Sniffing)
        // Memaksa browser untuk mengikuti MIME type yang dikirim server
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 4. HTTP Strict Transport Security (HSTS)
        // Memaksa koneksi HTTPS selama 1 tahun
        if ($request->isSecure() || env('APP_ENV') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // 5. XSS Protection
        // Mengaktifkan filter XSS di browser lama
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 6. Referrer Policy
        // Mengontrol informasi referrer yang dikirim saat navigasi
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 7. Permissions Policy
        // Membatasi akses ke fitur browser seperti kamera, mikrofon, dll.
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // 8. Fix Inconsistent Redirection (Wapiti Alert)
        // Menghapus body HTML dari respon redirect 3xx agar tidak membingungkan client
        if ($response->isRedirection()) {
            $response->setContent('');
        }

        // 9. Force HttpOnly on XSRF-TOKEN (Wapiti Alert)
        // Catatan: Jika aplikasi Anda menggunakan AJAX yang membaca cookie ini (seperti Axios default), 
        // Anda mungkin perlu mematikan ini. Tapi untuk keamanan maksimal, kita set HttpOnly.
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === 'XSRF-TOKEN') {
                $response->headers->setCookie(
                    new \Symfony\Component\HttpFoundation\Cookie(
                        $cookie->getName(),
                        $cookie->getValue(),
                        $cookie->getExpiresTime(),
                        $cookie->getPath(),
                        $cookie->getDomain(),
                        $cookie->isSecure(),
                        true, // httpOnly = true
                        $cookie->isRaw(),
                        $cookie->getSameSite()
                    )
                );
            }
        }

        return $response;
    }
}
