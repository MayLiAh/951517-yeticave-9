<nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $category) : ?>
        <li class="nav__item">
          <a href="lots-by-category.php?id=<?=$category['id']; ?>"><?=$category['name']; ?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <div class="container">
      <section class="lots">
        <h2>Все лоты в категории «<span><?=$categoryName; ?></span>»</h2>
        <?php if (!empty($lots)) : ?>
        <ul class="lots__list">
            <?php foreach ($lots as $lot) : ?>
          <li class="lots__item lot">
            <div class="lot__image">
              <img src="<?=$lot['image']; ?>" width="350" height="260" alt="<?=$lot['name']; ?>">
            </div>
            <div class="lot__info">
              <span class="lot__category"><?=$lot['category']; ?></span>
              <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$lot['id']; ?>"><?=$lot['name']; ?></a></h3>
              <div class="lot__state">
                <div class="lot__rate">
                  <span class="lot__amount"><?=$lot['cost_type']; ?></span>
                  <span class="lot__cost"><?=getFormattedPrice($lot['current_cost']); ?></span>
                  </div>
                    <?php $remainingTime = getRemainingTime($lot['end_at']); ?>
                    <?php $timerClass = $remainingTime['remaining_hours'] <= 1 ? 'timer--finishing' : ''; ?>
                <div class="lot__timer timer <?=$timerClass; ?>">
                    <?=$remainingTime['remaining_time']; ?>
                </div>
              </div>
            </div>
          </li>
            <?php endforeach; ?>
        </ul>
        <?php else : ?>
        <span>В этой категории нет активных лотов</span>
        <?php endif; ?>
      </section>
        <?php if (count($lots) > 9) : ?>
      <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
        <li class="pagination-item pagination-item-active"><a>1</a></li>
        <li class="pagination-item"><a href="#">2</a></li>
        <li class="pagination-item"><a href="#">3</a></li>
        <li class="pagination-item"><a href="#">4</a></li>
        <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
      </ul>
        <?php endif; ?>
    </div>