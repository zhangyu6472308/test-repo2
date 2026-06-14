<?php

declare(strict_types=1);

session_start();

require __DIR__ . '/api/db.php';

$config = getAppConfig();
$pdo = getDatabaseConnection();
ensureCommunityTables($pdo);

function siteName(): string
{
    global $config;
    return (string)($config['site_name'] ?? '我的个人圈子');
}

function h(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function currentUser(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    global $pdo;
    $statement = $pdo->prepare('SELECT id, username, display_name FROM users WHERE id = :id');
    $statement->execute([':id' => (int)$_SESSION['user_id']]);
    $user = $statement->fetch();

    return $user ?: null;
}

function requireLogin(): array
{
    $user = currentUser();
    if (!$user) {
        header('Location: login.php');
        exit;
    }

    return $user;
}

function isAdmin(): bool
{
    return !empty($_SESSION['is_admin']);
}

function flash(?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function redirectTo(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function renderHeader(string $title): void
{
    $user = currentUser();
    $flash = flash();
    ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($title) ?> - <?= h(siteName()) ?></title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="index.php"><?= h(siteName()) ?></a>
    <nav class="nav">
      <a href="index.php">首页</a>
      <a href="index.php#feed">内容</a>
      <a href="member.php">会员</a>
      <?php if ($user): ?>
        <a href="create.php">发帖</a>
        <span><?= h($user['display_name']) ?></span>
        <a href="logout.php">退出</a>
      <?php else: ?>
        <a href="login.php">登录</a>
        <a href="register.php">注册</a>
      <?php endif; ?>
      <a href="admin.php">管理</a>
    </nav>
  </header>
  <main class="page">
    <?php if ($flash): ?>
      <div class="flash <?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
    <?php endif; ?>
    <?php
}

function renderFooter(): void
{
    ?>
  </main>
</body>
</html>
    <?php
}
