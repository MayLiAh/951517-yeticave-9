<nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $category) : ?>
        <li class="nav__item">
          <a href="lots-by-category.php?id=<?=$category['id']; ?>"><?=$category['name']; ?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <section class="rates container">
      <h2>Мои ставки</h2>
      <table class="rates__list">
        <?php if (!empty($rates)) : ?>
        <?php foreach ($rates as $rate) : ?>
        <tr class="rates__item <?=$rate['rate_class']; ?>">
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?=$rate['lot_img']; ?>" width="54" height="40" alt="<?=$rate['lot_name']; ?>">
            </div>
            <h3 class="rates__title"><a href="lot.php?id=<?=$rate['lot_id']; ?>"><?=$rate['lot_name']; ?></a></h3>
            <p><?=$rate['user_contacts']; ?></p>
          </td>
          <td class="rates__category">
            <?=$rate['category_name']; ?>
          </td>
          <td class="rates__timer">
            <div class="timer <?=$rate['timer_class']; ?>"><?=$rate['timer_status']; ?></div>
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