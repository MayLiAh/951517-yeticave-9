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
            <?php if (isset($_SESSION['user_name'])) : ?>
            <form class="lot-item__form" action="https://echo.htmlacademy.ru" method="post" autocomplete="off">
              <p class="lot-item__form-item form__item form__item--invalid">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" placeholder="12 000">
                <span class="form__error">Введите наименование лота</span>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>