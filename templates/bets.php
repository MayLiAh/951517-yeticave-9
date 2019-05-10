<?php require_once __DIR__ . '/../functions.php'; ?>
<nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $category) : ?>
        <li class="nav__item">
          <a href="all-lots.html"><?=$category['name']; ?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <section class="rates container">
      <h2>Мои ставки</h2>
      <table class="rates__list">
        <?php if (!empty($rates)) : ?>
        <?php foreach ($rates as $rate) : ?>
        <tr class="rates__item">
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?=$rate['lot_img']; ?>" width="54" height="40" alt="<?=$rate['lot_name']; ?>">
            </div>
            <h3 class="rates__title"><a href="lot.php?id=<?=$rate['lot_id']; ?>"><?=$rate['lot_name']; ?></a></h3>
          </td>
          <td class="rates__category">
            <?=$rate['category_name']; ?>
          </td>
            <?php $remainingTime = getRemainingTime($rate['lot_end']); ?>
            <?php $timerClass = $remainingTime['remaining_hours'] <= 1 ? 'timer--finishing' : ''; ?>
          <td class="rates__timer">
            <div class="timer <?=$timerClass; ?>"><?=$remainingTime['remaining_time']; ?></div>
          </td>
          <td class="rates__price">
            <?=getFormattedPrice($rate['cost']); ?>
          </td>
          <td class="rates__time">
            <?=getElapsedTime($rate['rate_time']); ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif ?>
      </table>
    </section>