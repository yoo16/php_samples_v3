<div class="card">
    <div class="header">
        <h1 class="title"><?= $title ?></h1>
    </div>
    
    <div class="image-wrapper">
        <img src="<?= $image ?>" alt="Item Image">
    </div>

    <div class="content">
        <h2 class="headline"><?= $headline ?></h2>
        <p class="description"><?= nl2br($message) ?></p>
        <div class="date"><?= $dateString ?></div>
        <div class="price-tag">
            <span class="currency">Ticket</span>
            <span class="amount">¥<?= number_format($price) ?></span>
            <span class="suffix">(税込)</span>
        </div>
    </div>
</div>