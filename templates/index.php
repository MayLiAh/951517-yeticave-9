<section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <?php foreach ($categories as $category) : ?>
            <li class="promo__item promo__item--<?=$category['symbol_code']; ?>">
                <a class="promo__link" href="lots-by-category.php?id=<?=$category['id']; ?>"><?=$category['name']; ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <?php foreach ($lots as $lot) : ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$lot['image']; ?>" width="350" height="260" alt="">
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
                        <div class="lot-item__timer timer <?=$timerClass; ?>">
                            <?=$remainingTime['remaining_time']; ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
        <?php if (count($pages) > 1) : ?>
      <ul class="pagination-list">
        <?php 
        $hrefBack = '';
        if (isset($_GET['page']) && $_GET['page'] > 1) {
            $pageBack = $_GET['page'] - 1;
            $hrefBack = "href='index.php?page=$pageBack'";
        } ?>
        <li class="pagination-item pagination-item-prev"><a <?=$hrefBack; ?>>Назад</a></li>
        <?php foreach ($pages as $page => $href) : ?>
        <li class="pagination-item pagination-item-active"><a <?=$href; ?>><?=$page; ?></a></li>
        <?php endforeach; ?>
        <?php 
        $hrefForward = '';
        if (isset($_GET['page']) && array_key_exists($_GET['page'] + 1, $pages)) {
            $pageForward = $_GET['page'] + 1;
            $hrefForward = "href='index.php?page=$pageForward'";
        } elseif (!isset($_GET['page'])) {
            $hrefForward = "href='index.php?page=2'";
        } ?>
        <li class="pagination-item pagination-item-next"><a <?=$hrefForward; ?>>Вперед</a></li>
      </ul>
        <?php endif; ?>
