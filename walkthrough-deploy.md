# Panduan Deploy ke VPS

## 🚀 Panduan Deploy ke VPS

**Info VPS:** `ssh byme30@20.2.20.28`

### Persiapan Lokal (Lakukan di Komputer Anda)

**Langkah 1 — Pastikan model AI sudah terlatih:**
```bash
cd ~/Documents/sembako-app-copy/sqli
source venv/bin/activate
python3 train_model.py
```

**Langkah 2 — Build asset frontend:**
```bash
cd ~/Documents/sembako-app-copy
npm run build
```

---

### Deploy ke VPS (SSH)

**Langkah 3 — Login ke VPS & Siapkan direktori:**
```bash
ssh byme30@20.2.20.28
sudo mkdir -p /var/www/sembako
sudo chown -R byme30:www-data /var/www/sembako
```

**Langkah 4 — Upload project dari komputer lokal:**
> Jalankan ini di terminal **komputer lokal** Anda (bukan di dalam SSH):
```bash
# Upload project Laravel (tanpa vendor dan node_modules)
rsync -avz --progress \
  --exclude='vendor/' \
  --exclude='node_modules/' \
  --exclude='sqli/venv/' \
  --exclude='sqli/__pycache__/' \
  --exclude='sqli/data/raw/synthetic_*.csv' \
  --exclude='.git/' \
  --exclude='storage/logs/*.log' \
  ~/Documents/sembako-app-copy/ \
  byme30@20.2.20.28:/var/www/sembako/
```

**Langkah 5 — Masuk VPS & Install dependensi:**
```bash
ssh byme30@20.2.20.28

# Install dependensi Laravel
cd /var/www/sembako
composer install --no-dev --optimize-autoloader

# Install dependensi Python
cd sqli
python3 -m venv venv
venv/bin/pip install -r requirements.txt
cd ..
```

**Langkah 6 — Konfigurasi .env untuk Production:**
```bash
cp .env .env.backup
nano .env
```
Ubah nilai-nilai berikut:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://20.2.20.28   # atau domain Anda

# Sesuaikan database dengan VPS Anda
DB_HOST=127.0.0.1
DB_DATABASE=sembako_db
DB_USERNAME=sembako_user
DB_PASSWORD=password_kuat_baru

# Isi dengan token & ID Telegram Anda
TELEGRAM_BOT_TOKEN=token_dari_botfather
TELEGRAM_OWNER_ID=chat_id_anda
APP_LARAVEL_PATH=/var/www/sembako
```

**Langkah 7 — Inisialisasi Laravel:**
```bash
cd /var/www/sembako

# Optimasi untuk production
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permission storage
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**Langkah 8 — Konfigurasi Supervisor (agar bot & AI auto-restart):**
```bash
sudo apt install supervisor -y
sudo nano /etc/supervisor/conf.d/sembako.conf
```
Isi dengan:
```ini
[program:sembako-sqli-api]
command=/var/www/sembako/sqli/venv/bin/python3 /var/www/sembako/sqli/sqli_server_api.py
directory=/var/www/sembako/sqli
autostart=true
autorestart=true
stderr_logfile=/var/log/sembako-sqli.err.log
stdout_logfile=/var/log/sembako-sqli.out.log

[program:sembako-telegram-bot]
command=/var/www/sembako/sqli/venv/bin/python3 /var/www/sembako/sqli/telegram_bot.py
directory=/var/www/sembako/sqli
autostart=true
autorestart=true
stderr_logfile=/var/log/sembako-bot.err.log
stdout_logfile=/var/log/sembako-bot.out.log
```
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
sudo supervisorctl status
```

**Langkah 9 — Konfigurasi Nginx:**
```bash
sudo nano /etc/nginx/sites-available/sembako
```
```nginx
server {
    listen 80;
    server_name 20.2.20.28;  # ganti dengan domain jika ada

    root /var/www/sembako/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```
```bash
sudo ln -s /etc/nginx/sites-available/sembako /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

**Langkah 10 — Konfigurasi Laravel Queue Worker:**
```bash
sudo nano /etc/supervisor/conf.d/sembako-queue.conf
```
```ini
[program:sembako-queue]
command=php /var/www/sembako/artisan queue:work --sleep=3 --tries=3
directory=/var/www/sembako
autostart=true
autorestart=true
stderr_logfile=/var/log/sembako-queue.err.log
stdout_logfile=/var/log/sembako-queue.out.log
```
```bash
sudo supervisorctl reread && sudo supervisorctl update
```

---

## 🔐 Checklist Keamanan Final

- [ ] `APP_DEBUG=false` di .env production
- [ ] Password database diganti yang kuat
- [ ] Firewall: buka port 80, 443, 22 — tutup port 8000 & 8001 dari luar
- [ ] Pastikan `TELEGRAM_BOT_TOKEN` dan `TELEGRAM_OWNER_ID` sudah diisi
- [ ] Jalankan `php artisan config:cache` setelah edit .env

### Tutup Port AI dari Publik (PENTING!)
Port 8001 (AI API) hanya boleh diakses dari localhost, bukan dari internet:
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw deny 8001/tcp
sudo ufw deny 8000/tcp
sudo ufw enable
```
