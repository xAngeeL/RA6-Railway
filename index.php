<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

$name = "Ángel Beltrán Molina";

$info = [
  "MYSQLHOST" => getenv("MYSQLHOST") ?: "",
  "MYSQLPORT" => getenv("MYSQLPORT") ?: "",
  "MYSQLDATABASE" => getenv("MYSQLDATABASE") ?: "",
  "MYSQLUSER" => getenv("MYSQLUSER") ?: "",
];

$dbStatus = "No conectado (faltan variables de entorno).";
$tables = [];

if ($info["MYSQLHOST"] && $info["MYSQLPORT"] && $info["MYSQLDATABASE"] && $info["MYSQLUSER"] && getenv("MYSQLPASSWORD")) {
  try {
    $dsn = "mysql:host={$info['MYSQLHOST']};port={$info['MYSQLPORT']};dbname={$info['MYSQLDATABASE']};charset=utf8mb4";
    $pdo = new PDO($dsn, $info["MYSQLUSER"], getenv("MYSQLPASSWORD"), [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_TIMEOUT => 5,
    ]);

    $dbStatus = "✅ Conectado a MySQL (Railway)";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
  } catch (Throwable $e) {
    $dbStatus = "❌ Error MySQL: " . $e->getMessage();
  }
}

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>RA6 Railway - PHP</title>
  <style>
    body{font-family:system-ui,Segoe UI,Arial;margin:40px;background:#0b1020;color:#e7eaf3}
    .card{max-width:760px;padding:24px;border:1px solid #223; border-radius:14px;background:#111a33}
    h1{margin:0 0 10px}
    code{background:#0b1020;padding:2px 6px;border-radius:8px}
    ul{margin:8px 0 0}
    .muted{opacity:.8}
  </style>
</head>
<body>
  <div class="card">
    <h1><?php echo htmlspecialchars($name); ?></h1>
    <p class="muted">Despliegue en Railway con PHP + MySQL.</p>

    <h3>Estado BD</h3>
    <p><?php echo htmlspecialchars($dbStatus); ?></p>

    <h3>Variables detectadas</h3>
    <ul>
      <li>MYSQLHOST: <code><?php echo htmlspecialchars($info["MYSQLHOST"]); ?></code></li>
      <li>MYSQLPORT: <code><?php echo htmlspecialchars($info["MYSQLPORT"]); ?></code></li>
      <li>MYSQLDATABASE: <code><?php echo htmlspecialchars($info["MYSQLDATABASE"]); ?></code></li>
      <li>MYSQLUSER: <code><?php echo htmlspecialchars($info["MYSQLUSER"]); ?></code></li>
    </ul>

    <h3>Tablas</h3>
    <?php if ($tables): ?>
      <ul>
        <?php foreach ($tables as $t): ?>
          <li><code><?php echo htmlspecialchars((string)$t); ?></code></li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="muted">No hay tablas (o no se pudo listar).</p>
    <?php endif; ?>
  </div>
</body>
</html>
