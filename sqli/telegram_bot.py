#!/usr/bin/env python3
"""
Bot Telegram untuk Monitoring Sistem Keamanan SQLi
===================================================
Bot ini bersifat PRIVATE - hanya Owner yang bisa menggunakannya.
Fitur:
  - Notifikasi serangan SQLi secara real-time
  - /start    : Sambutan & daftar perintah
  - /status   : Cek apakah server Laravel & AI API berjalan
  - /sysinfo  : Informasi kesehatan VPS (CPU, RAM, Disk)
  - /logs     : Lihat log terbaru
  - /unblock  : Hapus blokir IP dari cache Laravel
  - /clear    : Hapus seluruh cache Laravel (untuk pengujian)
"""

import os
import subprocess
import psutil
import socket
import asyncio
import logging
from datetime import datetime
from dotenv import load_dotenv
from telegram import Update, Bot
from telegram.ext import Application, CommandHandler, ContextTypes, MessageHandler, filters

# ==================================================
# KONFIGURASI
# ==================================================
# Muat variabel dari .env Laravel
LARAVEL_PATH = os.getenv("APP_LARAVEL_PATH", "/home/prof/Documents/sembako-app-copy")
load_dotenv(os.path.join(LARAVEL_PATH, ".env"))

BOT_TOKEN = os.getenv("TELEGRAM_BOT_TOKEN")
OWNER_ID = int(os.getenv("TELEGRAM_OWNER_ID", "0"))
SQLI_LOG_PATH = os.path.join(os.path.dirname(__file__), "server.log")
LARAVEL_LOG_PATH = os.path.join(LARAVEL_PATH, "storage/logs/laravel.log")

logging.basicConfig(format='%(asctime)s - %(name)s - %(levelname)s - %(message)s', level=logging.INFO)
logger = logging.getLogger(__name__)


# ==================================================
# MIDDLEWARE KEAMANAN - HANYA OWNER
# ==================================================
def owner_only(func):
    """Decorator: Hanya Owner yang boleh menjalankan perintah ini."""
    async def wrapper(update: Update, context: ContextTypes.DEFAULT_TYPE):
        user_id = update.effective_user.id
        if user_id != OWNER_ID:
            logger.warning(f"Akses ditolak untuk user ID: {user_id}")
            await update.message.reply_text("⛔ Akses Ditolak. Bot ini bersifat private.")
            return
        return await func(update, context)
    return wrapper


# ==================================================
# HELPER FUNCTIONS
# ==================================================
def check_port(port: int) -> bool:
    """Cek apakah sebuah port sedang digunakan (service berjalan)."""
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        return s.connect_ex(('127.0.0.1', port)) == 0

def read_last_lines(filepath: str, n: int = 20) -> str:
    """Baca N baris terakhir dari sebuah file log."""
    try:
        with open(filepath, 'r', errors='replace') as f:
            lines = f.readlines()
        return "".join(lines[-n:]) if lines else "(Log kosong)"
    except FileNotFoundError:
        return f"(File tidak ditemukan: {filepath})"
    except Exception as e:
        return f"(Error membaca log: {e})"

def run_artisan(command: str) -> str:
    """Jalankan perintah php artisan dan kembalikan outputnya."""
    try:
        result = subprocess.run(
            ["php", "artisan"] + command.split(),
            capture_output=True, text=True, cwd=LARAVEL_PATH, timeout=15
        )
        return result.stdout.strip() or result.stderr.strip() or "(Tidak ada output)"
    except Exception as e:
        return f"Error: {e}"


def get_blocked_ips() -> list:
    """Ambil daftar IP yang sedang diblokir dari cache Laravel."""
    try:
        result = subprocess.run(
            ["php", "artisan", "sqli:blocked"],
            capture_output=True, text=True, cwd=LARAVEL_PATH, timeout=15
        )
        import json
        output = result.stdout.strip()
        # Ambil baris terakhir yang berisi JSON (artisan kadang ada output tambahan)
        for line in reversed(output.splitlines()):
            line = line.strip()
            if line.startswith('['):
                return json.loads(line)
        return []
    except Exception as e:
        logger.error(f"Error get_blocked_ips: {e}")
        return []


# ==================================================
# COMMAND HANDLERS
# ==================================================
@owner_only
async def cmd_start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handler untuk perintah /start."""
    username = update.effective_user.first_name
    msg = (
        f"👋 Halo *{username}*! Selamat datang di *SQLi Defense Bot*.\n\n"
        f"Bot ini memantau keamanan server secara real-time.\n\n"
        f"📋 *Daftar Perintah:*\n"
        f"🟢 `/status` — Cek status server (Laravel & AI API)\n"
        f"🖥️ `/sysinfo` — Info kesehatan VPS (CPU, RAM, Disk)\n"
        f"📜 `/logs` — Log terbaru (AI Server & Laravel)\n"
        f"🚫 `/blocked` — Daftar IP yang sedang diblokir\n"
        f"🔓 `/unblock <ip>` — Hapus blokir IP tertentu\n"
        f"🧹 `/clear` — Hapus seluruh cache Laravel\n"
    )
    await update.message.reply_text(msg, parse_mode='Markdown')


@owner_only
async def cmd_status(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handler untuk perintah /status — cek semua service."""
    await update.message.reply_text("🔍 Mengecek status server...")

    laravel_ok = check_port(8000)
    sqli_api_ok = check_port(8001)

    laravel_icon = "🟢" if laravel_ok else "🔴"
    sqli_icon = "🟢" if sqli_api_ok else "🔴"

    msg = (
        f"📡 *Status Server*\n\n"
        f"{laravel_icon} *Laravel App* (Port 8000): {'AKTIF' if laravel_ok else 'MATI'}\n"
        f"{sqli_icon} *AI SQLi API* (Port 8001): {'AKTIF' if sqli_api_ok else 'MATI'}\n\n"
        f"⏰ Diperbarui: `{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}`"
    )
    await update.message.reply_text(msg, parse_mode='Markdown')


@owner_only
async def cmd_sysinfo(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handler untuk perintah /sysinfo — info CPU, RAM, Disk VPS."""
    cpu = psutil.cpu_percent(interval=1)
    ram = psutil.virtual_memory()
    disk = psutil.disk_usage('/')
    uptime_seconds = (datetime.now() - datetime.fromtimestamp(psutil.boot_time())).total_seconds()
    hours, rem = divmod(int(uptime_seconds), 3600)
    minutes, _ = divmod(rem, 60)

    def bar(percent, length=10):
        filled = int(length * percent / 100)
        return "▓" * filled + "░" * (length - filled)

    msg = (
        f"🖥️ *Informasi Sistem VPS*\n\n"
        f"💻 *CPU* : `{cpu:.1f}%` {bar(cpu)}\n"
        f"🧠 *RAM* : `{ram.percent:.1f}%` {bar(ram.percent)}\n"
        f"      ↳ Dipakai: `{ram.used/1024**3:.1f}GB` / `{ram.total/1024**3:.1f}GB`\n"
        f"💾 *Disk*: `{disk.percent:.1f}%` {bar(disk.percent)}\n"
        f"      ↳ Dipakai: `{disk.used/1024**3:.1f}GB` / `{disk.total/1024**3:.1f}GB`\n"
        f"⏱️ *Uptime*: `{hours} jam {minutes} menit`\n\n"
        f"⏰ `{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}`"
    )
    await update.message.reply_text(msg, parse_mode='Markdown')


@owner_only
async def cmd_logs(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handler untuk perintah /logs — tampilkan log terbaru."""
    sqli_log = read_last_lines(SQLI_LOG_PATH, 15)
    msg = (
        f"📜 *Log AI Server (15 baris terakhir):*\n"
        f"```\n{sqli_log[-3000:]}\n```"  # Potong jika terlalu panjang
    )
    await update.message.reply_text(msg, parse_mode='Markdown')

    laravel_log = read_last_lines(LARAVEL_LOG_PATH, 10)
    msg2 = (
        f"📜 *Log Laravel (10 baris terakhir):*\n"
        f"```\n{laravel_log[-3000:]}\n```"
    )
    await update.message.reply_text(msg2, parse_mode='Markdown')


@owner_only
async def cmd_blocked(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handler untuk /blocked — tampilkan semua IP yang sedang diblokir."""
    blocked = get_blocked_ips()

    if not blocked:
        await update.message.reply_text("✅ Tidak ada IP yang sedang diblokir saat ini.")
        return

    lines = []
    for i, entry in enumerate(blocked, 1):
        ip = entry.get('ip', 'unknown')
        expires = entry.get('expires_at', 'unknown')
        lines.append(f"`{i}.` 🔴 `{ip}`\n    ↳ Blokir berakhir: `{expires}`")

    msg = (
        f"🚫 *Daftar IP yang Diblokir ({len(blocked)} IP)*\n\n"
        + "\n\n".join(lines)
        + "\n\n🔓 Untuk unblock: `/unblock <ip>`"
    )
    await update.message.reply_text(msg, parse_mode='Markdown')


@owner_only
async def cmd_unblock(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handler untuk /unblock <ip> — hapus blokir IP tertentu dari cache."""
    if not context.args:
        # Jika tidak ada argumen, tampilkan daftar IP yang diblokir sebagai bantuan
        blocked = get_blocked_ips()
        if not blocked:
            await update.message.reply_text(
                "⚠️ Tidak ada argumen.\n\n"
                "✅ Tidak ada IP yang sedang diblokir saat ini."
            )
        else:
            lines = [f"`{e['ip']}`" for e in blocked]
            msg = (
                "⚠️ *Format salah.* Gunakan: `/unblock <ip>`\n\n"
                f"📋 *IP yang sedang diblokir ({len(blocked)}) :*\n"
                + "\n".join(lines)
            )
            await update.message.reply_text(msg, parse_mode='Markdown')
        return

    ip = context.args[0]
    # Validasi format IP sederhana
    parts = ip.split('.')
    if len(parts) != 4 or not all(p.isdigit() and 0 <= int(p) <= 255 for p in parts):
        await update.message.reply_text(f"⚠️ Format IP tidak valid: `{ip}`", parse_mode='Markdown')
        return

    cache_key = f"sqli_blocked_ip_{ip}"
    output = run_artisan(f"cache:forget {cache_key}")

    # Tampilkan sisa IP yang masih diblokir
    remaining = get_blocked_ips()
    sisa_msg = f"📋 Sisa IP terblokir: *{len(remaining)}* IP" if remaining else "✅ Tidak ada IP yang tersisa."

    msg = (
        f"✅ *Blokir IP Dihapus*\n\n"
        f"🔓 IP `{ip}` berhasil di-unblock.\n"
        f"{sisa_msg}"
    )
    await update.message.reply_text(msg, parse_mode='Markdown')


@owner_only
async def cmd_clear(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Handler untuk /clear — hapus seluruh cache Laravel (untuk pengujian)."""
    await update.message.reply_text("🧹 Menghapus seluruh cache Laravel...")
    output = run_artisan("cache:clear")
    await update.message.reply_text(
        f"✅ *Cache Berhasil Dihapus*\n\n`{output}`",
        parse_mode='Markdown'
    )


async def cmd_unknown(update: Update, context: ContextTypes.DEFAULT_TYPE):
    """Abaikan semua perintah dari bukan Owner."""
    if update.effective_user.id != OWNER_ID:
        return  # Diam saja untuk orang asing


# ==================================================
# FUNGSI UTAMA
# ==================================================
async def send_notification(token: str, chat_id: int, message: str):
    """Kirim notifikasi ke Telegram (dipanggil oleh sistem eksternal)."""
    bot = Bot(token=token)
    await bot.send_message(chat_id=chat_id, text=message, parse_mode='Markdown')


def main():
    if not BOT_TOKEN or BOT_TOKEN == "ISI_TOKEN_BOT_ANDA_DISINI":
        print("❌ ERROR: TELEGRAM_BOT_TOKEN belum diisi di file .env!")
        return
    if OWNER_ID == 0:
        print("❌ ERROR: TELEGRAM_OWNER_ID belum diisi di file .env!")
        return

    print(f"🤖 Bot Telegram sedang berjalan...")
    print(f"👤 Owner ID: {OWNER_ID}")

    app = Application.builder().token(BOT_TOKEN).build()

    # Daftarkan semua command handler
    app.add_handler(CommandHandler("start", cmd_start))
    app.add_handler(CommandHandler("status", cmd_status))
    app.add_handler(CommandHandler("sysinfo", cmd_sysinfo))
    app.add_handler(CommandHandler("logs", cmd_logs))
    app.add_handler(CommandHandler("blocked", cmd_blocked))
    app.add_handler(CommandHandler("unblock", cmd_unblock))
    app.add_handler(CommandHandler("clear", cmd_clear))
    app.add_handler(MessageHandler(filters.ALL, cmd_unknown))

    app.run_polling(allowed_updates=Update.ALL_TYPES)


if __name__ == "__main__":
    main()
