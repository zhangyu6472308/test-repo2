<?php

declare(strict_types=1);

require __DIR__ . '/app.php';

$postId = (int)($_GET['id'] ?? 0);
$statement = $pdo->prepare(
    'SELECT p.id, p.title, p.content, p.created_at, u.display_name
     FROM posts p
     JOIN users u ON u.id = p.user_id
     WHERE p.id = :id'
);
$statement->execute([':id' => $postId]);
$post = $statement->fetch();

if (!$post) {
    flash('帖子不存在。', 'error');
    redirectTo('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = requireLogin();
    $content = trim((string)($_POST['content'] ?? ''));

    if ($content === '' || mb_strlen($content, 'UTF-8') > 1000) {
        flash('评论不能为空，且不能超过 1000 个字。', 'error');
    } else {
        $statement = $pdo->prepare(
            'INSERT INTO comments (post_id, user_id, content)
             VALUES (:post_id, :user_id, :content)'
        );
        $statement->execute([
            ':post_id' => $postId,
            ':user_id' => (int)$user['id'],
            ':content' => $content,
        ]);
        flash('评论发布成功。');
        redirectTo('post.php?id=' . $postId);
    }
}

$statement = $pdo->prepare(
    'SELECT c.id, c.content, c.created_at, u.display_name
     FROM comments c
     JOIN users u ON u.id = c.user_id
     WHERE c.post_id = :post_id
     ORDER BY c.id ASC'
);
$statement->execute([':post_id' => $postId]);
$comments = $statement->fetchAll();

renderHeader($post['title']);
?>
<article class="post-card">
  <h1 class="post-title"><?= h($post['title']) ?></h1>
  <div class="meta">
    <span><?= h($post['display_name']) ?></span>
    <span><?= h($post['created_at']) ?></span>
  </div>
  <p class="content"><?= h($post['content']) ?></p>
</article>

<section class="panel" style="margin-top: 16px;">
  <h2>评论</h2>
  <?php if (!$comments): ?>
    <p class="empty">还没有评论。</p>
  <?php else: ?>
    <ul class="comment-list">
      <?php foreach ($comments as $comment): ?>
        <li class="comment-card">
          <div class="meta">
            <span><?= h($comment['display_name']) ?></span>
            <span><?= h($comment['created_at']) ?></span>
          </div>
          <p class="content"><?= h($comment['content']) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if (currentUser()): ?>
    <form method="post" style="margin-top: 18px;">
      <label>
        写评论
        <textarea name="content" maxlength="1000" required></textarea>
      </label>
      <button type="submit">提交评论</button>
    </form>
  <?php else: ?>
    <p><a href="login.php">登录后发表评论</a></p>
  <?php endif; ?>
</section>
<?php renderFooter(); ?>
