<?php

declare(strict_types=1);

require __DIR__ . '/app.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $statement = $pdo->prepare('SELECT id, password_hash FROM users WHERE username = :username');
    $statement->execute([':username' => $username]);
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = (int)$user['id'];
        flash('登录成功。');
        redirectTo('index.php');
    }

    flash('用户名或密码不正确。', 'error');
}

renderHeader('登录');
?>
<section class="panel">
  <h1>登录</h1>
  <form method="post">
    <label>
      用户名
      <input name="username" autocomplete="username" required>
    </label>
    <label>
      密码
      <input name="password" type="password" autocomplete="current-password" required>
    </label>
    <button type="submit">登录</button>
  </form>
</section>
<?php renderFooter(); ?>
