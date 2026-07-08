<?php
// Script to create wa_settings table manually using PDO
// Run: php run_migration.php

$dotenv = __DIR__ . '/.env';
$env = [];
foreach (file($dotenv) as $line) {
    $line = trim($line);
    if (empty($line) || strpos($line, '#') === 0 || strpos($line, '=') === false) continue;
    [$key, $val] = explode('=', $line, 2);
    $env[trim($key)] = trim($val, "\"'");
}

$host     = $env['DB_HOST'] ?? '127.0.0.1';
$port     = $env['DB_PORT'] ?? '3306';
$dbname   = $env['DB_DATABASE'] ?? '';
$username = $env['DB_USERNAME'] ?? '';
$password = $env['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Check if table already exists
    $tables = $pdo->query("SHOW TABLES LIKE 'wa_settings'")->fetchAll();
    if (!empty($tables)) {
        echo "Table 'wa_settings' already exists.\n";
    } else {
        $pdo->exec("
            CREATE TABLE wa_settings (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                cabang_order TEXT NULL COMMENT 'JSON array of cabang IDs in blast priority order',
                delay_per_person INT NOT NULL DEFAULT 10 COMMENT 'Delay in seconds between each person',
                delay_per_cabang INT NOT NULL DEFAULT 0 COMMENT 'Additional delay in seconds between each branch',
                created_at TIMESTAMP NULL DEFAULT NULL,
                updated_at TIMESTAMP NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Insert default row
        $throttle = (int) ($env['WA_THROTTLE_SECONDS'] ?? 10);
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare("INSERT INTO wa_settings (cabang_order, delay_per_person, delay_per_cabang, created_at, updated_at) VALUES (NULL, :dp, 0, :ca, :ua)");
        $stmt->execute([':dp' => $throttle, ':ca' => $now, ':ua' => $now]);

        echo "Table 'wa_settings' created successfully.\n";
        echo "Inserted default row: delay_per_person={$throttle}, delay_per_cabang=0\n";
    }

    // Also insert to migrations table to track
    $existing = $pdo->query("SELECT * FROM migrations WHERE migration = '2026_07_08_080000_create_wa_settings_table'")->fetchAll();
    if (empty($existing)) {
        $batch = $pdo->query("SELECT MAX(batch) as b FROM migrations")->fetchColumn();
        $batch = ((int)$batch) + 1;
        $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES ('2026_07_08_080000_create_wa_settings_table', :b)");
        $stmt->execute([':b' => $batch]);
        echo "Migration record inserted (batch={$batch}).\n";
    }

    echo "Done!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
