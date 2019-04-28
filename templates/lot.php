<?php require_once 'functions.php'; ?>
    <nav class="nav">
      <ul class="nav__list container">
        <? foreach ($categories as $category) : ?>
        <li class="nav__item">
          <a href="all-lots.html"><?=$category['name']; ?></a>
        </li>
        <? endforeach; ?>
      </ul>
    </nav>
    <section class="lot-item container">
      <h2><?=$lot['name']; ?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="<?=$lot['image']; ?>" width="730" height="548" alt="Сноуборд">
          </div>
          <p class="lot-item__category">Категория: <span><?=$lot['category']; ?></span></p>
          <p class="lot-item__description"><?=$lot['about']; ?></p>
        </div>
        <div class="lot-item__right">
          <div class="lot-item__state">
            <?php $remainingTime = getRemainingTime($lot['end_at']); ?>
            <?php $timerClass = $remainingTime['remaining_hours'] <= 1 ? 'timer--finishing' : ''; ?>
            <div class="lot-item__timer timer <?=$timerClass; ?>">
                <?=$remainingTime['remaining_time']; ?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?=getFormattedPrice($lot['current_cost']); ?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?=getFormattedPrice($lot['current_cost'] + $lot['rate_step']); ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>