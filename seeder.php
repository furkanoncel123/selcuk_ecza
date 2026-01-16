<?php
// SUPREME SEEDER (MySQL Version)
// Run this once to populate your MySQL database.

// --- CONFIG ---
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $env = parse_ini_file($envPath);
} else {
    $env = []; // Fallback or die?
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '8889';
$user = $env['DB_USERNAME'] ?? 'root';
$pass = $env['DB_PASSWORD'] ?? 'root';
$dbname = $env['DB_DATABASE'] ?? 'selcuk_ecza';
$socket = $env['DB_SOCKET'] ?? '';

try {
    // 1. Connect to Server
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    if (!empty($socket)) {
        $dsn = "mysql:host=$host;unix_socket=$socket;charset=utf8mb4";
    }
    
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "🔌 Connected to MySQL Server at $host...\n";

    // 2. Create Database
    $pdo->exec("DROP DATABASE IF EXISTS $dbname");
    $pdo->exec("CREATE DATABASE $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✨ Database '$dbname' created...\n";

    // 3. Connect to Database (Use same connection, just USE)
    $pdo->exec("USE $dbname");

    // 4. Create Tables
    $sql = "
    CREATE TABLE eczaneler (
        id INT AUTO_INCREMENT PRIMARY KEY,
        isim VARCHAR(255) NOT NULL,
        sehir VARCHAR(100)
    );

    CREATE TABLE ilaclar (
        id INT AUTO_INCREMENT PRIMARY KEY,
        isim VARCHAR(255) NOT NULL,
        kategori VARCHAR(100)
    );

    CREATE TABLE satis_verileri (
        id INT AUTO_INCREMENT PRIMARY KEY,
        eczane_id INT,
        ilac_id INT,
        tarih DATE,
        ay INT,
        yil INT,
        miktar INT,
        FOREIGN KEY (eczane_id) REFERENCES eczaneler(id),
        FOREIGN KEY (ilac_id) REFERENCES ilaclar(id)
    );

    CREATE TABLE musteriler (
        id INT AUTO_INCREMENT PRIMARY KEY,
        adi VARCHAR(255) NOT NULL,
        soyadi VARCHAR(255) NOT NULL,
        hesap_no VARCHAR(50),
        kullanici_adi VARCHAR(50) UNIQUE NOT NULL,
        sifre VARCHAR(255) NOT NULL
    );
    ";
    $pdo->exec($sql);
    echo "📋 Tables created (including musteriler)...\n";

    // 5. Seed Data
    $eczaneler = ['Buca Eczanesi', 'Şirinyer Eczanesi', 'Dokuz Eylül Eczanesi', 'Hasanağa Eczanesi', 'Yıldız Eczanesi', 'Çamlık Eczanesi', 'Tınaztepe Eczanesi'];
    foreach ($eczaneler as $e) {
        $stmt = $pdo->prepare("INSERT INTO eczaneler (isim, sehir) VALUES (?, 'İzmir')");
        $stmt->execute([$e]);
    }

    $ilaclar = [
        ['Augmentin 1000mg', 'Antibiyotik '], ['Apranax Fort', 'Ağrı Kesici'], ['Majezik', 'Ağrı Kesici'],
        ['Coraspin', 'Kalp Damar'], ['Supradyn', 'Vitamin'], ['Redoxon', 'Vitamin'],
        ['Benexol', 'Vitamin'], ['Dolorex', 'Ağrı Kesici'], ['Tylolhot', 'Soğuk Algınlığı'],
        ['Theraflu', 'Soğuk Algınlığı'], ['Nurofen', 'Soğuk Algınlığı'], ['Aerius', 'Alerji'],
        ['Zyrtec', 'Alerji'], ['Klamoks', 'Antibiyotik'], ['Largopen', 'Antibiyotik'],
        ['Parol', 'Ağrı Kesici'], ['Arveles', 'Ağrı Kesici'], ['Cialis', 'Üroloji'],
        ['Viagra', 'Üroloji'], ['Muscoflex', 'Kas Gevşetici'], ['Dikloron', 'Kas Gevşetici'],
        ['Nexium', 'Mide'], ['Gaviscon', 'Mide'], ['Rennie', 'Mide'],
        ['Lansor', 'Mide'], ['Prozac', 'Psikiyatri'], ['Cipralex', 'Psikiyatri'],
        ['Xanax', 'Psikiyatri'], ['Ventolin', 'Solunum'], ['Symbicort', 'Solunum'],
        ['Bioderma Sensibio', 'Dermokozmetik'], ['La Roche Posay Effaclar', 'Dermokozmetik'],
        ['Cerave Temizleyici', 'Dermokozmetik'], ['Vichy Mineral 89', 'Dermokozmetik'],
        ['Bepanthol', 'Cilt Bakım'], ['Madecassol', 'Cilt Bakım'], ['Fito Krem', 'Cilt Bakım'],
        ['Biteral', 'Antibiyotik'], ['Flagyl', 'Antibiyotik'], ['Linex', 'Probiyotik']
    ];
    foreach ($ilaclar as $i) {
        $stmt = $pdo->prepare("INSERT INTO ilaclar (isim, kategori) VALUES (?, ?)");
        $stmt->execute([$i[0], $i[1]]);
    }

    echo "🌱 Pharmacies and Drugs seeded...\n";

    // Generate Sales Data (Last 12 Months)
    $stmt = $pdo->prepare("INSERT INTO satis_verileri (eczane_id, ilac_id, tarih, ay, yil, miktar) VALUES (?, ?, ?, ?, ?, ?)");
    
    // Get IDs
    $eIds = $pdo->query("SELECT id FROM eczaneler")->fetchAll(PDO::FETCH_COLUMN);
    $iIds = $pdo->query("SELECT id FROM ilaclar")->fetchAll(PDO::FETCH_COLUMN);

    $total = 0;
    foreach ($eIds as $eid) {
        for ($m = 1; $m <= 12; $m++) {
            // Seasonal Logic for realistic data simulation
            $seasonFactor = 1.0;
            if (in_array($m, [12, 1, 2])) $seasonFactor = 1.3; // Winter boost
            
            // Random days in month
            for ($d = 1; $d <= 28; $d += rand(1, 3)) {
                $date = sprintf("2024-%02d-%02d", $m, $d);
                // Daily entries
                for ($k = 0; $k < rand(3, 15); $k++) {
                    $iid = $iIds[array_rand($iIds)];
                    $qty = rand(1, 5) * $seasonFactor;
                    $stmt->execute([$eid, $iid, $date, $m, 2024, (int)$qty]);
                    $total++;
                }
            }
        }
    }

    echo "🚀 Sales Data Generated ($total rows)...\n";
    echo "✅ DONE! Database '$dbname' is ready.\n";

} catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "💡 Hint: Make sure MySQL is running and user/pass in this file are correct.\n";
}
