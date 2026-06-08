<?php
require __DIR__ . '/db.php';

$pdo = get_pdo();
$errors = [];
$info = '';

// Traitement formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action      = $_POST['action'] ?? 'create';
    $displayDate = $_POST['display_date'] ?? '';
    $title       = trim($_POST['title'] ?? '');
    $body        = trim($_POST['body'] ?? '');
    $id          = isset($_POST['id']) ? (int) $_POST['id'] : null;

    if ($action !== 'delete') {
        if (!$displayDate) {
            $errors[] = "La date d'affichage est obligatoire.";
        }
        if ($body === '') {
            $errors[] = "La description des travaux est obligatoire.";
        }
    }

    if (empty($errors)) {
        if ($action === 'create') {
            $stmt = $pdo->prepare("
                INSERT INTO messages (display_date, title, body)
                VALUES (:display_date, :title, :body)
            ");
            $stmt->execute([
                ':display_date' => $displayDate,
                ':title'        => $title,
                ':body'         => $body,
            ]);
            $info = "Message créé.";
        } elseif ($action === 'update' && $id) {
            $stmt = $pdo->prepare("
                UPDATE messages
                SET display_date = :display_date, title = :title, body = :body
                WHERE id = :id
            ");
            $stmt->execute([
                ':display_date' => $displayDate,
                ':title'        => $title,
                ':body'         => $body,
                ':id'           => $id,
            ]);
            $info = "Message mis à jour.";
        } elseif ($action === 'delete' && $id) {
            $stmt = $pdo->prepare("DELETE FROM messages WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $info = "Message supprimé.";
        }
    }
}

// Liste des messages pour affichage
$stmt = $pdo->query("SELECT * FROM messages ORDER BY display_date ASC, id ASC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <title>Admin Travaux Orange</title>
  <meta name="color-scheme" content="light dark" />
  <link rel="stylesheet" href="css/base.css" />
  <link rel="stylesheet" href="css/admin.css" />
</head>
<body>
  <header class="admin-header">
    <div class="admin-header-content">
      <div>
        <div class="brand">
          <span class="brand-square"></span>
          <span class="brand-text">Travaux Orange – Admin</span>
        </div>
        <p class="admin-subtitle">
          Gestion des messages affichés d'intervention Orange.
        </p>
      </div>
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
  </header>

  <main class="admin-main">
    <section class="admin-panel card">
      <h2>Créer / modifier un message</h2>
      <p class="text-muted">
        Un message est affiché uniquement le jour indiqué (de 00:00 à 23:59).
      </p>

      <?php if (!empty($errors)): ?>
        <ul style="color:red;">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      <?php elseif ($info): ?>
        <p style="color:green;"><?= htmlspecialchars($info) ?></p>
      <?php endif; ?>

      <form id="messageForm" method="post">
        <input type="hidden" id="id" name="id" value="" />
        <input type="hidden" id="action" name="action" value="create" />

        <div class="form-row">
          <label for="display_date">Date d'affichage</label>
          <input
            type="date"
            id="display_date"
            name="display_date"
            required
            value="<?= htmlspecialchars($today) ?>"
          />
        </div>

        <div class="form-row">
          <label for="title">Titre (optionnel)</label>
          <input
            type="text"
            id="title"
            name="title"
            placeholder="Ex : Travaux fibre – Bâtiment A"
          />
        </div>

        <div class="form-row">
          <label for="body">Description des travaux</label>
          <textarea
            id="body"
            name="body"
            required
            placeholder="Ex : Intervention Orange sur le lien fibre entre 08h00 et 10h00. Risque de coupure momentanée."
          ></textarea>
        </div>

        <div class="form-actions">
          <button type="submit" id="saveBtn">Enregistrer</button>
          <button
            type="button"
            class="secondary"
            id="cancelEditBtn"
            style="display: none;"
          >
            Annuler la modification
          </button>
        </div>
      </form>
    </section>

    <section class="admin-panel card">
      <h2>Messages planifiés</h2>
      <p class="text-muted">
        Statut :
        <span class="badge badge-today">Aujourd’hui</span>
        <span class="badge badge-future">À venir</span>
        <span class="badge badge-past">Passé</span>
      </p>
      <div id="allMessages" class="message-list">
        <?php if (empty($messages)): ?>
          <p>Aucun message planifié.</p>
        <?php else: ?>
          <?php foreach ($messages as $m): ?>
            <?php
              $cmp = $m['display_date'] < $today ? -1 : ($m['display_date'] > $today ? 1 : 0);
              if ($cmp === 0) {
                  $badgeClass = 'badge badge-today';
                  $badgeText  = "Aujourd'hui";
              } elseif ($cmp < 0) {
                  $badgeClass = 'badge badge-past';
                  $badgeText  = 'Passé';
              } else {
                  $badgeClass = 'badge badge-future';
                  $badgeText  = 'À venir';
              }
            ?>
            <div class="message-item">
              <div class="message-header">
                <div class="message-title">
                  <?= htmlspecialchars($m['title'] ?: 'Travaux Orange') ?>
                </div>
                <div>
                  <span class="message-date">
                    <?= htmlspecialchars(date('d/m/Y', strtotime($m['display_date']))) ?>
                  </span>
                  <span class="<?= $badgeClass ?>"><?= $badgeText ?></span>
                </div>
              </div>
              <div class="message-text">
                <?= nl2br(htmlspecialchars($m['body'])) ?>
              </div>
              <div class="message-actions">
                <button
                  type="button"
                  onclick='editMessage(<?= json_encode($m, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'
                >
                  Modifier
                </button>
                <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer ce message ?');">
                  <input type="hidden" name="id" value="<?= (int)$m['id'] ?>" />
                  <input type="hidden" name="action" value="delete" />
                  <button type="submit" class="secondary">Supprimer</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <script src="js/theme.js"></script>
  <script>
    function editMessage(message) {
      document.getElementById('id').value = message.id;
      document.getElementById('display_date').value = message.display_date;
      document.getElementById('title').value = message.title || '';
      document.getElementById('body').value = message.body;

      document.getElementById('action').value = 'update';
      document.getElementById('saveBtn').textContent = 'Mettre à jour';
      document.getElementById('cancelEditBtn').style.display = 'inline-block';
    }

    document.getElementById('cancelEditBtn').addEventListener('click', () => {
      document.getElementById('id').value = '';
      document.getElementById('action').value = 'create';
      document.getElementById('display_date').value = '<?= $today ?>';
      document.getElementById('title').value = '';
      document.getElementById('body').value = '';
      document.getElementById('saveBtn').textContent = 'Enregistrer';
      document.getElementById('cancelEditBtn').style.display = 'none';
    });
  </script>
</body>
</html>