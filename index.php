<?php

declare(strict_types=1);

require __DIR__ . '/app.php';

$postCount = (int)$pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$userCount = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$commentCount = (int)$pdo->query('SELECT COUNT(*) FROM comments')->fetchColumn();

$statement = $pdo->query(
    'SELECT p.id, p.title, p.content, p.created_at, u.display_name,
            (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count
     FROM posts p
     JOIN users u ON u.id = p.user_id
     ORDER BY p.id DESC
     LIMIT 8'
);
$posts = $statement->fetchAll();

renderHeader('首页');
?>
<section class="landing-hero">
  <div class="hero-copy">
    <p class="eyebrow">AI / 副业 / 私域 / 项目实战</p>
    <h1>把普通人的想法，推进成看得见的项目结果</h1>
    <p class="lead">触点圈层俱乐部是一个轻量社区：记录每天的行动，复盘真实问题，和同频的人一起把机会拆成下一步。</p>
    <div class="hero-actions">
      <a class="button" href="register.php">免费加入</a>
      <a class="button ghost" href="#feed">看最新内容</a>
    </div>
  </div>
  <div class="hero-metrics" aria-label="社区数据">
    <div>
      <strong><?= $userCount ?></strong>
      <span>成员</span>
    </div>
    <div>
      <strong><?= $postCount ?></strong>
      <span>帖子</span>
    </div>
    <div>
      <strong><?= $commentCount ?></strong>
      <span>评论</span>
    </div>
  </div>
</section>

<section class="intro-strip">
  <div>
    <span>01</span>
    <h2>记录行动</h2>
    <p>把灵感、尝试和结果写下来，不让经验只停留在聊天记录里。</p>
  </div>
  <div>
    <span>02</span>
    <h2>互相反馈</h2>
    <p>围绕 AI 工具、内容流量、私域运营、个人项目做具体交流。</p>
  </div>
  <div>
    <span>03</span>
    <h2>沉淀资产</h2>
    <p>长期积累的帖子可以变成案例库、课程资料或社群内容入口。</p>
  </div>
</section>

<section class="split-section">
  <div>
    <p class="eyebrow">适合谁</p>
    <h2>适合正在从 0 到 1 跑项目的人</h2>
    <p>不需要一开始就很复杂。你可以先记录今天做了什么、卡在哪里、下一步怎么改；当内容和用户验证起来后，再逐步加入会员、课程、活动和订单能力。</p>
  </div>
  <ul class="check-list">
    <li>想做自己的小项目，但不知道怎么拆步骤</li>
    <li>想沉淀案例、复盘和经验，形成长期内容资产</li>
    <li>想给用户一个能交流、提问、看资料的私域入口</li>
  </ul>
</section>

<section id="feed" class="feed-section">
  <div class="section-heading">
    <div>
      <p class="eyebrow">社区内容</p>
      <h2>最新帖子</h2>
    </div>
    <a class="button secondary" href="create.php">发布新帖</a>
  </div>

  <?php if (!$posts): ?>
    <div class="empty">还没有帖子，注册登录后发布第一条内容吧。</div>
  <?php else: ?>
    <ul class="post-list">
      <?php foreach ($posts as $post): ?>
        <li class="post-card">
          <h3 class="post-title"><a href="post.php?id=<?= (int)$post['id'] ?>"><?= h($post['title']) ?></a></h3>
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
</section>
<?php renderFooter(); ?>
