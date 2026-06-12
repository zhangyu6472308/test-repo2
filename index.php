<?php

declare(strict_types=1);

require __DIR__ . '/app.php';

$statement = $pdo->query(
    'SELECT p.id, p.title, p.content, p.created_at, u.display_name,
            (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count
     FROM posts p
     JOIN users u ON u.id = p.user_id
     ORDER BY p.id DESC
     LIMIT 50'
);
$posts = $statement->fetchAll();

renderHeader('首页');
?>
<section class="hero">
  <h1>一个属于自己的内容圈子</h1>
  <p>用户可以注册、发帖、评论；管理员可以处理不合适的内容。先做小而完整的一版，后面再加付费、分类、图片和小程序。</p>
</section>

<?php if (!$posts): ?>
  <div class="empty">还没有帖子，登录后发布第一条内容吧。</div>
<?php else: ?>
  <ul class="post-list">
    <?php foreach ($posts as $post): ?>
      <li class="post-card">
        <h2 class="post-title"><a href="post.php?id=<?= (int)$post['id'] ?>"><?= h($post['title']) ?></a></h2>
        <div class="meta">
          <span><?= h($post['display_name']) ?></span>
          <span><?= h($post['created_at']) ?></span>
          <span><?= (int)$post['comment_count'] ?> 条评论</span>
        </div>
        <p class="content"><?= h(mb_substr($post['content'], 0, 180, 'UTF-8')) ?><?= mb_strlen($post['content'], 'UTF-8') > 180 ? '...' : '' ?></p>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php renderFooter(); ?>
