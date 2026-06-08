<?php
require __DIR__ . '/db.php';

$pdo   = get_pdo();
$today = date('Y-m-d');

// Récupère les messages pour la date du jour[web:121][web:132]
$stmt = $pdo->prepare("SELECT * FROM messages WHERE display_date = :today ORDER BY id ASC");
$stmt->execute([':today' => $today]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <title>Travaux Orange - Informations du jour</title>
  <meta name="color-scheme" content="light dark" />
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/user.css" />
</head>
<body class="tv-view">
  <header class="topbar">
    <div class="topbar-content">
      <div class="brand">
        <span class="brand-square"></span>
        <span class="brand-text">Travaux Orange</span>
      </div>
      <div class="topbar-right">
        <span class="topbar-date">
          Aujourd’hui :
          <span id="todayDisplay"><?= htmlspecialchars(date('d/m/Y')) ?></span>
        </span>
        <button
          class="theme-toggle"
          type="button"
          data-theme-toggle
          aria-label="Basculer le thème"
        >
          <span class="theme-toggle-icon" id="themeIcon">🌙</span>
          <span id="themeLabel">Mode sombre</span>
        </button>
      </div>
    </div>
  </header>

  <main class="main">
    <section class="hero card">
      <h1>Travaux réseau Orange</h1>
      <p class="text-muted">
        Cette page affiche les interventions planifiées pour la journée sur l’infrastructure Orange
        (coupures fibre, travaux de maintenance, bascules de liens, etc.).
      </p>
    </section>

    <section class="messages-section">
      <h2>Travaux prévus aujourd’hui</h2>
      <div class="messages-container card">
        <?php if (empty($messages)): ?>
          <p class="message-empty">
            Aucun travaux Orange impactant n’est planifié pour aujourd’hui.
          </p>
        <?php else: ?>
          <?php foreach ($messages as $m): ?>
            <article class="message-card">
              <div class="message-title-line">
                <div class="message-title">
                  <?= htmlspecialchars($m['title'] ?: 'Travaux Orange') ?>
                </div>
                <span class="badge badge-future">Information</span>
              </div>
              <div class="message-meta">
                Date d’intervention :
                <?= htmlspecialchars(date('d/m/Y', strtotime($m['display_date']))) ?>
              </div>
              <div class="message-text">
                <?= nl2br(htmlspecialchars($m['body'])) ?>
              </div>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <footer class="footer">
    Page interne – informations indicatives sur les travaux Orange.
  </footer>

  <script src="js/theme.js"></script>
</body>
</html>