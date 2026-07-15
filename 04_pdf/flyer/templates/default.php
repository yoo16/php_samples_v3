<div class="header">
    <div class="title"><?= $title ?></div>
    <div>期間限定：<?= $dateString ?></div>
</div>
<div class="content">
    <h2><?= $headline ?></h2>
    <p><?= nl2br($message) ?></p>
    <div class="price">¥<?= number_format($price) ?> 〜</div>
    <div class="item-image">
        <img src="<?= $image ?>" alt="">
    </div>
</div>