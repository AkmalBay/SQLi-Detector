import csv
import random
import os
import string

def generate_synthetic_data(filename_normal='data/raw/synthetic_normal.csv', filename_sqli='data/raw/synthetic_sqli.csv', count=100000):
    names = ["Budi Santoso", "Siti Aminah", "Agus Setiawan", "Dewi Lestari", "Eko Prasetyo", "Ani Wijaya", "Fajar Ramadhan", "Lestari Putri", "Andi Pratama", "Rina Kartika", "Muhammad Rizky", "Nur Hidayah", "Ahmad Fauzi", "Dian Sari", "Wahyu Pratama"]
    domains = ["gmail.com", "yahoo.com", "outlook.com", "sembako.id", "toko.com", "brayan.shop", "hotmail.com"]
    products = ["Beras Cianjur 5kg", "Minyak Goreng SunCo 2L", "Gula Pasir 1kg", "Telur Ayam Ras 1kg", "Garam Dapur", "Kopi Kapal Api", "Teh Celup Sariwangi", "Indomie Goreng", "Susu Kental Manis", "Sabun Cuci Piring", "Mie Sedaap Goreng", "Royco Ayam 80g", "Tepung Terigu Segitiga Biru 1kg", "Kecap Manis ABC 600ml", "Sambal Indofood 275ml"]
    actions = ["login", "search", "checkout", "profile", "dashboard", "products", "orders", "settings", "inventory", "report"]
    passwords = ["password123", "qwerty", "admin123", "123456", "rahasia", "p@ssword", "sembako2024"]
    
    # SKU patterns (pola kode produk yang realistis)
    sku_prefixes = ["BRS", "MYK", "GLA", "TLR", "KPI", "TEH", "MIE", "SBN", "SKU", "PRD", "INV", "STK", "ITEM", "GRS"]
    
    # Indonesian addresses
    streets = ["Jl. Merdeka", "Jl. Sudirman", "Jl. Ahmad Yani", "Jl. Gatot Subroto", "Jl. Diponegoro", "Jl. Imam Bonjol", "Jl. Kartini"]
    cities = ["Jakarta", "Surabaya", "Bandung", "Semarang", "Yogyakarta", "Malang", "Solo", "Medan", "Wonosobo", "Purwokerto"]
    
    # Unit names
    unit_names = ["Pcs", "Dus", "Karton", "Lusin", "Pack", "Box", "Renceng", "Sachet", "Botol", "Kaleng", "Kg", "Liter"]
    
    os.makedirs(os.path.dirname(filename_normal), exist_ok=True)
    
    # 1. Generate Realistic NORMAL Data
    with open(filename_normal, 'w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(['Sentence', 'Label'])
        
        items_per_category = count // 14  # Bagi rata ke semua kategori
        
        # Pure numbers
        for _ in range(items_per_category): writer.writerow([str(random.randint(1, 100000)), 0])
        
        # Email addresses
        for _ in range(items_per_category):
            name = random.choice(names).lower().replace(" ", ".")
            writer.writerow([f"{name}{random.randint(1, 99)}@{random.choice(domains)}", 0])
        
        # Product names
        for _ in range(items_per_category): writer.writerow([random.choice(products), 0])
        
        # Action words
        for _ in range(items_per_category): writer.writerow([random.choice(actions), 0])
        
        # Passwords
        for _ in range(items_per_category): writer.writerow([random.choice(passwords) + str(random.randint(1, 99)), 0])
        
        # Random alphanumeric strings
        chars = "abcdefghijklmnopqrstuvwxyz0123456789"
        for _ in range(items_per_category): writer.writerow(["".join(random.choice(chars) for _ in range(12)), 0])
        
        # === NEW: SKU CODES (penyebab utama false positive!) ===
        for _ in range(items_per_category):
            prefix = random.choice(sku_prefixes)
            sep = random.choice(["-", "/", ".", ""])
            num1 = f"{random.randint(0, 999):03d}"
            num2 = f"{random.randint(1, 99):02d}" if random.random() > 0.3 else ""
            extra_sep = random.choice(["-", "/"]) if num2 else ""
            sku = f"{prefix}{sep}{num1}{extra_sep}{num2}"
            writer.writerow([sku, 0])
        
        # === NEW: Numeric codes with dashes (001-10, 12-345, etc.) ===
        for _ in range(items_per_category):
            patterns = [
                f"{random.randint(0,999):03d}-{random.randint(1,99):02d}",
                f"{random.randint(0,999):03d}-{random.randint(1,999):03d}",
                f"{random.randint(1,99)}-{random.randint(1,999)}",
                f"{random.randint(1,9999)}/{random.randint(1,99)}",
                f"P{random.randint(1,999):03d}-{random.randint(1,12):02d}",
                f"NO.{random.randint(1,9999)}",
                f"REF-{random.randint(10000,99999)}",
            ]
            writer.writerow([random.choice(patterns), 0])
        
        # === NEW: Indonesian phone numbers ===
        for _ in range(items_per_category // 2):
            phone = f"08{random.randint(10,99)}{random.randint(1000000,9999999)}"
            writer.writerow([phone, 0])
            writer.writerow([f"+62{phone[1:]}", 0])
        
        # === NEW: Prices and currency values ===
        for _ in range(items_per_category):
            price = random.randint(500, 500000)
            formats = [str(price), f"{price:,}", f"Rp {price:,}", f"Rp{price}", f"{price}.00"]
            writer.writerow([random.choice(formats), 0])
        
        # === NEW: Indonesian addresses ===
        for _ in range(items_per_category // 2):
            addr = f"{random.choice(streets)} No. {random.randint(1, 200)}, {random.choice(cities)}"
            writer.writerow([addr, 0])
            writer.writerow([f"RT {random.randint(1,20):02d}/RW {random.randint(1,15):02d}, {random.choice(cities)}", 0])
        
        # === NEW: Dates and timestamps ===
        for _ in range(items_per_category):
            day = random.randint(1, 28)
            month = random.randint(1, 12)
            year = random.randint(2020, 2026)
            formats = [
                f"{day:02d}-{month:02d}-{year}",
                f"{year}-{month:02d}-{day:02d}",
                f"{day:02d}/{month:02d}/{year}",
                f"{day} Januari {year}" if month == 1 else f"{day} Mei {year}",
            ]
            writer.writerow([random.choice(formats), 0])
        
        # === NEW: Unit/quantity descriptions ===
        for _ in range(items_per_category):
            qty = random.randint(1, 500)
            unit = random.choice(unit_names)
            formats = [
                f"{qty} {unit}",
                f"{qty}{unit.lower()}",
                f"Isi {qty} {unit}",
                f"{qty}x {random.choice(products)}",
            ]
            writer.writerow([random.choice(formats), 0])

    # 2. Generate Realistic SQLI Data (to counter the "prose" bias)
    sqli_keywords = ["OR", "AND", "SELECT", "UNION", "DROP", "INSERT", "UPDATE", "DELETE"]
    sqli_templates = [
        "' {kw} '{word}'='{word}'",
        "\" {kw} \"{word}\"=\"{word}\"",
        "' {kw} 1=1 --",
        "admin' {kw} '{word}'='{word}'",
        "' {kw} 'a'='a' --",
        "'); {kw} ALL SELECT NULL,NULL--",
        "' UNION SELECT username,password FROM users--",
        "1; DROP TABLE users--",
        "' OR ''='",
        "admin'--",
        "1' AND (SELECT * FROM users)--",
        "'; EXEC xp_cmdshell('dir')--",
        "' UNION SELECT NULL,table_name FROM information_schema.tables--",
        "1 AND SLEEP(5)--",
        "' OR BENCHMARK(10000000,SHA1('test'))--",
    ]
    words = ["something", "anything", "test", "user", "pass", "data", "admin"]
    
    with open(filename_sqli, 'w', newline='', encoding='utf-8') as f:
        writer = csv.writer(f)
        writer.writerow(['Sentence', 'Label'])
        for _ in range(10000):
            tmpl = random.choice(sqli_templates)
            writer.writerow([tmpl.format(kw=random.choice(sqli_keywords), word=random.choice(words)), 1])

    print(f"[SUCCESS] Generated {count} normal and 10000 sqli synthetic rows.")

if __name__ == "__main__":
    generate_synthetic_data()

