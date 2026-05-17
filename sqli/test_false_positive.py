"""
Test script untuk memverifikasi bahwa false positive sudah diperbaiki.
Menguji data bisnis yang seharusnya AMAN + payload SQLi yang seharusnya TERBLOKIR.
"""
from src.ml.sqli_detector import SQLIDetector

detector = SQLIDetector(model_path='models/sqli_model.pkl')
detector.load_model()

print("=" * 70)
print("TEST FALSE POSITIVE FIX - Verifikasi Audit SQLi Detector")
print("=" * 70)

# === Data yang HARUS LOLOS (Normal/Aman) ===
safe_inputs = [
    # SKU codes (penyebab masalah awal!)
    "001-10",
    "BRS-001",
    "MYK-002/05",
    "SKU-123-45",
    "ITEM.001",
    "PRD-999",
    "001-10-25",
    
    # Product names
    "Beras Cianjur 5kg",
    "Minyak Goreng SunCo 2L",
    "Tepung Terigu Segitiga Biru 1kg",
    "Indomie Goreng Spesial",
    "Kecap Manis ABC 600ml",
    
    # Numbers/prices
    "25000",
    "12500",
    "Rp 150,000",
    "100",
    "50.00",
    
    # Names
    "Budi Santoso",
    "Siti Aminah",
    "Toko Sembako Makmur",
    
    # Addresses
    "Jl. Merdeka No. 45, Jakarta",
    "RT 05/RW 03, Semarang",
    
    # Dates
    "15-05-2026",
    "2026-01-15",
    
    # Phone numbers
    "081234567890",
    "+6281234567890",
    
    # Email
    "admin@sembako.id",
    "user@gmail.com",
    
    # Units
    "40 Dus",
    "Isi 24 Pack",
    "10x Indomie Goreng",
    
    # Misc form data
    "Catatan: stok perlu dicek ulang",
    "Supplier baru dari Bandung",
    "Pembayaran via transfer BCA",
]

# === Data yang HARUS DIBLOKIR (SQL Injection) ===
attack_inputs = [
    "' OR '1'='1'",
    "admin' OR 'a'='a'--",
    "' UNION SELECT username,password FROM users--",
    "1; DROP TABLE products--",
    "'; DELETE FROM users--",
    "' OR 1=1 --",
    "admin'--",
    "' UNION ALL SELECT NULL,NULL--",
    "1' AND (SELECT * FROM information_schema.tables)--",
    "'; EXEC xp_cmdshell('dir')--",
]

# === Run Tests ===
print("\n🟢 TEST 1: Data AMAN (harus lolos / prediction=0)")
print("-" * 70)
safe_pass = 0
safe_fail = 0
for inp in safe_inputs:
    prediction, proba = detector.predict(inp)
    status = "✅ PASS" if prediction == 0 else "❌ FAIL (FALSE POSITIVE!)"
    if prediction == 0:
        safe_pass += 1
    else:
        safe_fail += 1
        conf = float(proba[1] * 100)
        print(f"  {status} | Input: '{inp}' | Confidence SQLi: {conf:.1f}%")

print(f"\n  Hasil: {safe_pass}/{len(safe_inputs)} lolos, {safe_fail}/{len(safe_inputs)} false positive")

print(f"\n🔴 TEST 2: Payload SQLI (harus terblokir / prediction=1)")
print("-" * 70)
attack_pass = 0
attack_fail = 0
for inp in attack_inputs:
    prediction, proba = detector.predict(inp)
    status = "✅ BLOCKED" if prediction == 1 else "❌ MISS (FALSE NEGATIVE!)"
    if prediction == 1:
        attack_pass += 1
    else:
        attack_fail += 1
        print(f"  {status} | Input: '{inp}'")

print(f"\n  Hasil: {attack_pass}/{len(attack_inputs)} terblokir, {attack_fail}/{len(attack_inputs)} lolos")

# === Summary ===
print("\n" + "=" * 70)
total_tests = len(safe_inputs) + len(attack_inputs)
total_pass = safe_pass + attack_pass
print(f"📊 TOTAL: {total_pass}/{total_tests} test PASSED")
if safe_fail == 0 and attack_fail == 0:
    print("🎉 SEMUA TEST BERHASIL! Model sudah aman untuk data bisnis.")
else:
    if safe_fail > 0:
        print(f"⚠️  Ada {safe_fail} false positive yang harus diperbaiki!")
    if attack_fail > 0:
        print(f"⚠️  Ada {attack_fail} serangan yang tidak terdeteksi!")
print("=" * 70)
