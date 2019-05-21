    <nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $category) : ?>
        <li class="nav__item">
          <a href="lots-by-category.php?id=<?=$category['id']; ?>"><?=$category['name']; ?></a>
        </li>
        <?php endforeach; ?>
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
            <?php if (isset($_SESSION['user_name']) && $showRate) : ?>
                <?php $formClass = empty($errors) ? '' : 'form--invalid'; ?>
            <form class="lot-item__form <?=$formClass; ?>" action="lot.php?id=<?=$lotId; ?>" method="post" autocomplete="off">
                <?php
                $costClass = isset($errors['cost']) ? 'form__item--invalid' : '';
                $costError = isset($errors['cost']) ? $errors['cost'] : '';
                ?>
              <p class="lot-item__form-item form__item <?=$costClass; ?>">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" placeholder="<?=$minRate; ?>" value="<?=$cost; ?>">
                <span class="form__error"><?=$costError; ?></span>
                <span><?=$success; ?></span>
              </p>
              <button type="submit" class="button" name="submit">Сделать ставку</button>
            </form>
            <?php endif; ?>
          </div>
          <div class="history">
            <h3>История ставок (<span><?=$ratesCount; ?></span>)</h3>
            <?php if (!empty($rates)) : ?>
            <table class="history__list">
                <?php foreach ($rates as $rate) : ?>
              <tr class="history__item">
                <td class="history__name"><?=$rate['user_name']; ?></td>
                <td class="history__price"><?=getFormattedPrice($rate['cost']); ?></td>
                <td class="history__time"><?=getElapsedTime($rate['rate_time']); ?></td>
              </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>