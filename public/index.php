<?php
declare(strict_types=1);

function pdo(): PDO {
  // 1) Railway suele darte DATABASE_URL (interna)
  $url = getenv('DATABASE_URL') ?: getenv('DATABASE_URL_PUBLIC_URL');

  // 2) Si no hay URL, usamos variables sueltas (PGHOST, etc.)
  if (!$url) {
    $host = getenv('PGHOST') ?: '127.0.0.1';
    $port = getenv('PGPORT') ?: '5432';
    $db   = getenv('PGDATABASE') ?: 'railway';
    $user = getenv('PGUSER') ?: 'postgres';
    $pass = getenv('PGPASSWORD') ?: '';

    $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
    return new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
  }

  // Parse postgresql://user:pass@host:port/dbname
  $parts = parse_url($url);
  if (!$parts) throw new RuntimeException("DATABASE_URL inválida");

  $host = $parts['host'] ?? 'localhost';
  $port = (string)($parts['port'] ?? 5432);
  $user = $parts['user'] ?? '';
  $pass = $parts['pass'] ?? '';
  $db   = ltrim($parts['path'] ?? '', '/');

  // Si es PUBLIC_URL, a veces necesita SSL
  $query = [];
  if (!empty($parts['query'])) parse_str($parts['query'], $query);
  $sslmode = $query['sslmode'] ?? null;

  // Heurística: si usas el host público *.proxy.rlwy.net => sslmode=require
  $needsSsl = str_contains($host, '.proxy.rlwy.net');

  $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
  if ($sslmode) {
    $dsn .= ";sslmode={$sslmode}";
  } elseif ($needsSsl) {
    $dsn .= ";sslmode=require";
  }

  return new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);
}

$db = pdo();

// Crea tabla si no existe (Postgres)
$db->exec("
  CREATE TABLE IF NOT EXISTS tasks (
    id SERIAL PRIMARY KEY,
    title TEXT NOT NULL,
    done BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
  );
");

$action = $_POST['action'] ?? $_GET['action'] ?? null;

if ($action === 'add') {
  $title = trim((string)($_POST['title'] ?? ''));
  if ($title !== '') {
    $st = $db->prepare('INSERT INTO tasks(title) VALUES(:t)');
    $st->execute([':t' => $title]);
  }
  header('Location: /'); exit;
}

if ($action === 'toggle') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id > 0) {
    $st = $db->prepare('UPDATE tasks SET done = NOT done WHERE id = :id');
    $st->execute([':id' => $id]);
  }
  header('Location: /'); exit;
}

if ($action === 'delete') {
  $id = (int)($_POST['id'] ?? 0);
  if ($id > 0) {
    $st = $db->prepare('DELETE FROM tasks WHERE id = :id');
    $st->execute([':id' => $id]);
  }
  header('Location: /'); exit;
}

$tasks = $db->query('SELECT id, title, done, created_at FROM tasks ORDER BY id DESC')
            ->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Railway PHP + Postgres</title>
  <style>
    body{font-family:system-ui,Arial;margin:24px;max-width:820px}
    .row{display:flex;gap:8px;align-items:center;justify-content:space-between;padding:10px 12px;border:1px solid #ddd;border-radius:10px;margin:8px 0}
    .muted{color:#666;font-size:12px}
    .done{text-decoration:line-through;color:#777}
    form{margin:0}
    button{cursor:pointer}
  </style>
</head>
<body>
  <h1>Tasks</h1>

  <form method="post" action="/?action=add" style="display:flex;gap:8px;margin:14px 0;">
    <input name="title" placeholder="Nueva task..." style="flex:1;padding:10px;border:1px solid #ccc;border-radius:10px" required>
    <button type="submit" style="padding:10px 14px">Añadir</button>
  </form>

  <?php foreach ($tasks as $t): ?>
    <div class="row">
      <div>
        <div class="<?= $t['done'] ? 'done' : '' ?>"><?= htmlspecialchars($t['title']) ?></div>
        <div class="muted">#<?= (int)$t['id'] ?> · <?= htmlspecialchars((string)$t['created_at']) ?></div>
      </div>

      <div style="display:flex;gap:8px">
        <form method="post" action="/?action=toggle">
          <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
          <button type="submit"><?= $t['done'] ? 'Desmarcar' : 'Hecho' ?></button>
        </form>
        <form method="post" action="/?action=delete" onsubmit="return confirm('¿Borrar?')">
          <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
          <button type="submit">Borrar</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</body>
</html>
