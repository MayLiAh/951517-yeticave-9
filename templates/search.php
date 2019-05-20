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
        <h2>Результаты поиска по запросу «<span><?=$search; ?></span>»</h2>
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
        <span>По вашему запросу ничего не найдено</span>
        <?php endif; ?>
      </section>
        <?php if (count($pages) > 1) : ?>
      <ul class="pagination-list">
        <?php 
        $hrefBack = '';
        if (isset($_GET['page']) && $_GET['page'] > 1 && isset($_GET['search'])) {
            $searching = $_GET['search'];
            $pageBack = $_GET['page'] - 1;
            $hrefBack = "href='search.php?search=$searching&page=$pageBack'";
        } ?>
        <li class="pagination-item pagination-item-prev"><a <?=$hrefBack; ?>>Назад</a></li>
        <?php foreach ($pages as $page => $href) : ?>
        <li class="pagination-item pagination-item-active"><a <?=$href; ?>><?=$page; ?></a></li>
        <?php endforeach; ?>
        <?php 
        $hrefForward = '';
        if (isset($_GET['page']) && array_key_exists($_GET['page'] + 1, $pages) && isset($_GET['search'])) {
            $searching = $_GET['search'];
            $pageForward = $_GET['page'] + 1;
            $hrefForward = "href='search.php?search=$searching&page=$pageForward'";
        } elseif (!isset($_GET['page'])  && isset($_GET['search'])) {
            $searching = $_GET['search'];
            $hrefForward = "href='search.php?search=$searching&page=2'";
        } ?>
        <li class="pagination-item pagination-item-next"><a <?=$hrefForward; ?>>Вперед</a></li>
      </ul>
        <?php endif; ?>
    </div>