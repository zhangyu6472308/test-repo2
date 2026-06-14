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
    <p class="eyebrow">触点圈层俱乐部 · AI / 副业 / 私域 / 项目实战</p>
    <h1>把想法推进成真实结果的个人成长圈子</h1>
    <p class="lead">这里不是信息堆积站，而是一个适合普通人记录项目、交流经验、复盘踩坑、找到同路人的内容社区。</p>
    <div class="hero-actions">
      <a class="button" href="register.php">加入社区</a>
      <a class="button secondary" href="#feed">浏览内容</a>
      <a class="button secondary" href="member.php">会员权益</a>
    </div>
  </div>
  <div class="hero-board" aria-label="社群数据">
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

<section class="section-grid">
  <article class="feature-block">
    <span>01</span>
    <h2>项目实战</h2>
    <p>把每天看到的机会拆成可执行的小任务，记录过程、结果和复盘。</p>
  </article>
  <article class="feature-block">
    <span>02</span>
    <h2>同频交流</h2>
    <p>成员可以发帖和评论，围绕 AI 工具、内容流量、私域运营互相反馈。</p>
  </article>
  <article class="feature-block">
    <span>03</span>
    <h2>个人沉淀</h2>
    <p>每一条帖子都是你的案例库，以后可以整理成文章、课程或社群资料。</p>
  </article>
</section>

<section class="split-section">
  <div>
    <p class="eyebrow">适合谁</p>
    <h2>适合正在从 0 到 1 跑项目的人</h2>
    <p>你可以先从发帖记录开始：今天学了什么、做了什么、遇到什么问题、下一步怎么改。社区功能先保持简单，后面再加会员、课程、活动报名和小程序。</p>
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
