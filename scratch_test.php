<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=bprcianjur", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Find all internal regulations and their jenis_surat
    $stmt = $pdo->query("SELECT id, name, kategori, jenis_surat, created_at, updated_at FROM peraturans WHERE kategori = 'internal' ORDER BY id DESC LIMIT 50");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
