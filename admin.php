<?php

declare(strict_types=1);

require __DIR__ . '/app.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'login') {
        $savedPassword = (string)($config['admin_password'] ?? '');
        $inputPassword = (string)($_POST['admin_password'] ?? '');

        if ($savedPassword !== '' && $savedPassword !== 'change_this_admin_password' && hash_equals($savedPassword, $inputPassword)) {
            $_SESSION['is_admin'] = true;
            flash('管理员登录成功。');
            redirectTo('admin.php');
        }

        flash('管理密码不正确，或 api/config.php 还没有设置 admin_password。', 'error');
    }

    if ($action === 'logout') {
        unset($_SESSION['is_admin']);
        flash('已退出管理模式。');
        redirectTo('admin.php');
    }

    if ($action === 'delete_post' && isAdmin()) {
        $postId = (int)($_POST['post_id'] ?? 0);
        $statement = $pdo->prepare('DELETE FROM posts WHERE id = :id');
        $statement->execute([':id' => $postId]);
        flash('帖子已删除。');
        redirectTo('admin.php');
    }

    if ($action === 'delete_comment' && isAdmin()) {
        $commentId = (int)($_POST['comment_id'] ?? 0);
        $statement = $pdo->prepare('DELETE FROM comments WHERE id = :id');
        $statement->execute([':id' => $commentId]);
        flash('评论已删除。');
        redirectTo('admin.php');
    }
}

$posts = [];
$comments = [];
if (isAdmin()) {
    $posts = $pdo->query(
        'SELECT p.id, p.title, p.created_at, u.display_name
         FROM posts p
         JOIN users u ON u.id = p.user_id
         ORDER BY p.id DESC
         LIMIT 100'
    )->fetchAll();

    $comments = $pdo->query(
        'SELECT c.id, c.content, c.created_at, p.title, u.display_name
         FROM comments c
         JOIN posts p ON p.id = c.post_id
         JOIN users u ON u.id = c.user_id
         ORDER BY c.id DESC
         LIMIT 100'
    )->fetchAll();
}

renderHeader('后台管理');
?>
<section class="panel">
  <h1>后台管理</h1>
  <?php if (!isAdmin()): ?>
    <form method="post">
      <input type="hidden" name="action" value="login">
      <label>
        管理密码
        <input name="admin_password" type="password" required>
      </label>
      <button type="submit">进入管理</button>
    </form>
  <?php else: ?>
    <form method="post" class="inline-actions">
      <input type="hidden" name="action" value="logout">
      <button type="submit">退出管理模式</button>
    </form>

    <h2>帖子管理</h2>
    <?php if (!$posts): ?>
      <p class="empty">暂无帖子。</p>
    <?php else: ?>
      <ul class="post-list">
        <?php foreach ($posts as $post): ?>
          <li class="post-card">
            <h3 class="post-title"><?= h($post['title']) ?></h3>
            <div class="meta">
              <span><?= h($post['display_name']) ?></span>
              <span><?= h($post['created_at']) ?></span>
            </div>
            <form method="post" class="inline-actions">
              <input type="hidden" name="action" value="delete_post">
              <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
              <button class="danger" type="submit" onclick="return confirm('确定删除这个帖子吗？相关评论也会删除。')">删除帖子</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <h2>评论管理</h2>
    <?php if (!$comments): ?>
      <p class="empty">暂无评论。</p>
    <?php else: ?>
      <ul class="comment-list">
        <?php foreach ($comments as $comment): ?>
          <li class="comment-card">
            <div class="meta">
              <span><?= h($comment['display_name']) ?></span>
              <span><?= h($comment['created_at']) ?></span>
              <span>帖子：<?= h($comment['title']) ?></span>
            </div>
            <p class="content"><?= h($comment['content']) ?></p>
            <form method="post" class="inline-actions">
              <input type="hidden" name="action" value="delete_comment">
              <input type="hidden" name="comment_id" value="<?= (int)$comment['id'] ?>">
              <button class="danger" type="submit" onclick="return confirm('确定删除这条评论吗？')">删除评论</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  <?php endif; ?>
</section>
<?php renderFooter(); ?>
