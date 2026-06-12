<?php

declare(strict_types=1);

require __DIR__ . '/app.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $displayName = trim((string)($_POST['display_name'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
        flash('用户名只能用字母、数字、下划线，长度 3-30 位。', 'error');
    } elseif ($displayName === '' || mb_strlen($displayName, 'UTF-8') > 50) {
        flash('昵称不能为空，且不能超过 50 个字。', 'error');
    } elseif (strlen($password) < 6) {
        flash('密码至少 6 位。', 'error');
    } else {
        try {
            $statement = $pdo->prepare(
                'INSERT INTO users (username, password_hash, display_name)
                 VALUES (:username, :password_hash, :display_name)'
            );
            $statement->execute([
                ':username' => $username,
                ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ':display_name' => $displayName,
            ]);
            flash('注册成功，请登录。');
            redirectTo('login.php');
        } catch (PDOException $exception) {
            flash('这个用户名已经被使用。', 'error');
        }
    }
}

renderHeader('注册');
?>
<section class="panel">
  <h1>注册账号</h1>
  <form method="post">
    <label>
      用户名
      <input name="username" autocomplete="username" placeholder="例如 zhangsan" required>
    </label>
    <label>
      昵称
      <input name="display_name" placeholder="显示给其他用户看的名字" required>
    </label>
    <label>
      密码
      <input name="password" type="password" autocomplete="new-password" required>
    </label>
    <button type="submit">注册</button>
  </form>
</section>
<?php renderFooter(); ?>
