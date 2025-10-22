<?php
// Simple PDO-based page showing items from 'items' table
$host = getenv('DB_HOST') ?: 'db';
$db   = getenv('DB_NAME') ?: 'appdb';
$user = getenv('DB_USER') ?: 'appuser';
$pass = getenv('DB_PASS') ?: 'apppass';
$dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo "DB connection failed: " . htmlspecialchars($e->getMessage());
    exit;
}

$stmt = $pdo->query('SELECT id, name FROM items LIMIT 100');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>PHP MySQL Demo</title></head>
<body>
<h1>Items</h1>
<?php if (!$rows): ?>
  <p>No items — add one using SQL or seeder.</p>
<?php else: ?>
  <ul>
    <?php foreach ($rows as $r): ?>
      <li><?=htmlspecialchars($r['id'])?>: <?=htmlspecialchars($r['name'])?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
</body>
</html>
