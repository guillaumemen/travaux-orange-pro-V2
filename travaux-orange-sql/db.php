<?php
// db.php : connexion à SQLite + création de la table messages si besoin

function get_pdo(): PDO {
    $dbDir = __DIR__ . '/data';
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0775, true);
    }

    $dbPath = $dbDir . '/travaux.db';
    $dsn = 'sqlite:' . $dbPath; // Connexion PDO SQLite[web:132]

    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}

function init_db(): void {
    $pdo = get_pdo();

    $sql = "
        CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            display_date TEXT NOT NULL,   -- YYYY-MM-DD
            title        TEXT,
            body         TEXT NOT NULL,
            created_at   TEXT NOT NULL DEFAULT (CURRENT_TIMESTAMP)
        );
    ";
    // Création table via PDO + SQLite[web:125][web:134]
    $pdo->exec($sql);
}

init_db();