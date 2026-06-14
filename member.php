<?php

declare(strict_types=1);

require __DIR__ . '/app.php';

renderHeader('会员');
?>
<section class="member-hero">
  <div class="hero-copy">
    <p class="eyebrow">会员权益</p>
    <h1>先把社区体验跑顺，再开放更完整的会员能力</h1>
    <p class="lead">当前版本重点是让成员能注册、发帖、评论和沉淀内容。后续可以平滑扩展为课程资料、活动报名、会员专属栏目和支付订单。</p>
    <div class="hero-actions">
      <a class="button" href="register.php">免费加入</a>
      <a class="button ghost" href="index.php#feed">看最新内容</a>
    </div>
  </div>
</section>

<section class="pricing-grid">
  <article class="plan-card">
    <p class="eyebrow">当前版本</p>
    <h2>社区体验</h2>
    <strong class="price">免费</strong>
    <p class="plan-note">先把交流和内容沉淀跑起来。</p>
    <ul class="check-list">
      <li>注册账号</li>
      <li>发布帖子</li>
      <li>参与评论</li>
      <li>浏览公开内容</li>
    </ul>
  </article>
  <article class="plan-card highlighted">
    <p class="eyebrow">后续可加</p>
    <h2>会员圈子</h2>
    <strong class="price">待开放</strong>
    <p class="plan-note">等内容和用户验证后再上线。</p>
    <ul class="check-list">
      <li>会员专属内容</li>
      <li>课程资料栏目</li>
      <li>活动报名</li>
      <li>微信支付/订单</li>
    </ul>
  </article>
</section>
<?php renderFooter(); ?>
