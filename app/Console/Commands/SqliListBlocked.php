<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SqliListBlocked extends Command
{
    /**
     * Nama perintah Artisan.
     */
    protected $signature = 'sqli:blocked';

    /**
     * Deskripsi perintah.
     */
    protected $description = 'Tampilkan semua IP yang saat ini sedang diblokir oleh sistem SQLi Defense';

    /**
     * Jalankan perintah.
     */
    public function handle()
    {
        // Cari semua entri cache dengan key yang diawali 'sqli_blocked_ip_'
        $blockedEntries = DB::table('cache')
            ->where('key', 'like', '%sqli_blocked_ip_%')
            ->where('expiration', '>', now()->timestamp)
            ->get();

        if ($blockedEntries->isEmpty()) {
            $this->line('[]'); // Output JSON array kosong agar mudah diparsing bot
            return 0;
        }

        $results = [];
        foreach ($blockedEntries as $entry) {
            // Ambil hanya IP dari key (format: prefix_sqli_blocked_ip_xxx.xxx.xxx.xxx)
            $ip = preg_replace('/^.*sqli_blocked_ip_/', '', $entry->key);
            $expiresAt = date('d-m-Y H:i:s', $entry->expiration);
            $results[] = ['ip' => $ip, 'expires_at' => $expiresAt];
        }

        // Output JSON agar mudah diparsing oleh bot Python
        $this->line(json_encode($results));
        return 0;
    }
}
