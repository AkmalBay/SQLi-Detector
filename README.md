# AI SQL Injection Defense System (Laravel + Python AI)

Sistem pertahanan web berbasis kecerdasan buatan (AI) yang dirancang untuk mendeteksi dan mencegah serangan **SQL Injection (SQLi)** secara real-time. Proyek ini mengintegrasikan framework **Laravel** dengan model **Machine Learning (Naive Bayes)** untuk menganalisis setiap input yang masuk ke aplikasi.

## 🚀 Fitur Utama
- **AI-Powered Detection**: Menggunakan algoritma Naive Bayes dengan analisis karakter n-grams untuk akurasi tinggi.
- **Global Middleware**: Melindungi seluruh rute aplikasi Laravel secara otomatis.
- **Automated IP Blocking**: Memblokir alamat IP penyerang secara otomatis selama 24 jam jika terdeteksi aktivitas mencurigakan.
- **Real-time API**: Deteksi dilakukan melalui API Python (FastAPI) yang sangat cepat.
- **Fail-Open System**: Jika API AI tidak tersedia, aplikasi tetap berjalan normal untuk menjaga ketersediaan layanan.

---

## 📁 Struktur Kode

Proyek ini terbagi menjadi dua bagian utama: aplikasi web (Laravel) dan mesin AI (Python).

### 1. Web Application (Laravel)
- `app/Http/Middleware/SQLiDefense.php`: Middleware inti yang mencegat request, mengirimkan data ke AI, dan menangani pemblokiran IP.
- `routes/`: Seluruh route aplikasi yang kini dilindungi oleh middleware global.
- `.env`: Konfigurasi aplikasi termasuk pengaturan database.

### 2. AI Engine (Python) - Folder `/sqli`
- `sqli_server_api.py`: Server API (FastAPI) yang melayani permintaan prediksi dari Laravel.
- `train_model.py`: Script untuk melatih model menggunakan dataset terbaru.
- `src/ml/sqli_detector.py`: Logika inti pemrosesan data, ekstraksi fitur (TF-IDF), dan algoritma Naive Bayes.
- `data/raw/sqli.csv`: Dataset yang digunakan untuk melatih model (berisi ribuan contoh query aman dan SQLi).
- `models/sqli_model.pkl`: Model yang sudah dilatih dan siap digunakan.

---

## 🛠️ Cara Menjalankan

### 1. Menjalankan AI Server (Python)
Pastikan Python 3.x sudah terinstall.
```bash
# Masuk ke folder sqli
cd sqli

# (Opsional) Buat virtual environment
python3 -m venv venv
source venv/bin/activate

# Install dependensi
pip install -r requirements.txt

# Latih model (jika belum ada file models/sqli_model.pkl)
python3 train_model.py

# Jalankan server API
python3 sqli_server_api.py
```
*API akan berjalan di `http://127.0.0.1:8001`*

### 2. Menjalankan Aplikasi Laravel
```bash
# Di root direktori
composer install
npm install
php artisan migrate
php artisan serve
```
*Aplikasi akan berjalan di `http://127.0.0.1:8000`*

---

## 🧠 Inti dari SQLi Detector (How it Works)

Sistem ini tidak bekerja menggunakan filter kata kunci (regex) tradisional yang mudah dilewati penyerang. Berikut adalah inti cara kerjanya:

### 1. Representasi Data (TF-IDF & N-Grams)
Alih-alih melihat kata secara utuh, model ini memecah teks menjadi potongan-potongan karakter kecil (**N-Grams**, rentang 1-4 karakter). Contoh: `OR 1=1` akan dipecah menjadi `['O', 'R', ' ', '1', '=', 'OR', 'R ', ' 1', '1=']` dst.
Ini memungkinkan model mengenali struktur "berbahaya" meskipun penyerang mencoba melakukan obfuscation (penyamaran).

### 2. Algoritma Naive Bayes
Model menggunakan probabilitas statistik untuk menentukan apakah sebuah teks adalah SQLi atau normal:
- Ia menghitung seberapa sering pola karakter tertentu muncul di dataset serangan vs dataset normal.
- Saat input baru masuk, ia menghitung probabilitas totalnya. Jika probabilitas "Serangan" > "Normal" dengan kepercayaan (confidence) tinggi, maka dianggap SQLi.

### 3. Alur Deteksi
1. **Request Interception**: Laravel menangkap input (GET/POST/PUT).
2. **Flattening**: Semua input digabung menjadi satu string panjang.
3. **AI Analysis**: String dikirim ke API Python.
4. **Action**: 
   - Jika **is_sqli = true** (Confidence >= 80%), maka:
     - IP Penyerang dicatat di **Cache**.
     - Akses ditolak (**403 Forbidden**).
   - Jika aman, request diteruskan ke Controller.

### 4. IP Blocking
Pemblokiran dilakukan menggunakan sistem Cache Laravel. Jika IP diblokir, mereka bahkan tidak akan bisa mengakses halaman login selama 24 jam ke depan, sehingga mengurangi beban kerja server dari serangan brute force atau scanning berkelanjutan.

---

## 📊 Dataset
Dataset yang digunakan berasal dari gabungan berbagai sumber SQLi payloads dan query normal untuk memastikan tingkat *false positive* yang rendah.
