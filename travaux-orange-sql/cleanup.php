<?php
// cleanup.php : supprimer les messages dont la date est strictement passée

require __DIR__ . '/db.php';

$pdo = get_pdo();
$today = date('Y-m-d');

// Suppression des messages dont display_date < aujourd'hui
$stmt = $pdo->prepare("DELETE FROM messages WHERE display_date < :today");
$stmt->execute([':today' => $today]);
$deleted = $stmt->rowCount();

echo "Supprimé $deleted message(s) dont la date est passée au " . date('d/m/Y H:i:s') . PHP_EOL;