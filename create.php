<?php

declare(strict_types=1);

require __DIR__ . '/app.php';

$user = requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim((string)($_POST['title'] ?? ''));
    $content = trim((string)($_POST['content'] ?? ''));

    if ($title === '' || mb_strlen($title, 'UTF-8') > 120) {
        flash('标题不能为空，且不能超过 120 个字。', 'error');
    } elseif ($content === '' || mb_strlen($content, 'UTF-8') > 5000) {
        flash('内容不能为空，且不能超过 5000 个字。', 'error');
    } else {
        $statement = $pdo->prepare(
            'INSERT INTO posts (user_id, title, content)
             VALUES (:user_id, :title, :content)'
        );
        $statement->execute([
            ':user_id' => (int)$user['id'],
            ':title' => $title,
            ':content' => $content,
        ]);
        flash('帖子发布成功。');
        redirectTo('post.php?id=' . (int)$pdo->lastInsertId());
    }
}

renderHeader('发帖');
?>
<section class="panel">
  <h1>发布新帖子</h1>
  <form method="post">
    <label>
      标题
      <input name="title" maxlength="120" placeholder="写一个清楚的标题" required>
    </label>
    <label>
      内容
      <textarea name="content" maxlength="5000" placeholder="分享你的想法、项目进展或问题" required></textarea>
    </label>
    <button type="submit">发布</button>
  </form>
</section>
<?php renderFooter(); ?>
