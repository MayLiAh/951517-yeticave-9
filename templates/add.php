<nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $category) : ?>
        <li class="nav__item">
          <a href="all-lots.html"><?=$category['name']; ?></a>
        </li>
<?php endforeach; ?>
      </ul>
    </nav>
    <?php $formClass = empty($errors) ? '' : 'form--invalid'; ?>
    <form class="form form--add-lot container <?=$formClass; ?>" enctype="multipart/form-data" action="add.php" method="post"> <!-- form--invalid -->
      <h2>Добавление лота</h2>
      <div class="form__container-two">
        <?php 
            $nameClass = isset($errors['lot-name']) ? 'form__item--invalid' : '';
            $nameError = isset($errors['lot-name']) ? $errors['lot-name'] : '';
        ?>
        <div class="form__item <?=$nameClass; ?>">
          <label for="lot-name">Наименование <sup>*</sup></label>
          <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?=$lotName; ?>">
          <span class="form__error"><?=$nameError; ?></span>
        </div>
        <?php 
            $categoryClass = isset($errors['category']) ? 'form__item--invalid' : '';
            $categoryError = isset($errors['category']) ? $errors['category'] : '';
        ?>
        <div class="form__item <?=$categoryClass; ?>">
          <label for="category">Категория <sup>*</sup></label>
          <select id="category" name="category">
            <option value="0">Выберите категорию</option>
            <?php foreach ($categories as $category) : ?>
            <option value="<?=$category['id']; ?>" <?php if ($category['id'] === $categoryId) { echo 'selected'; } ?>><?=$category['name']; ?></option>
            <?php endforeach; ?>
          </select>
          <span class="form__error"><?=$categoryError; ?></span>
        </div>
      </div>
        <?php 
            $messageClass = isset($errors['message']) ? 'form__item--invalid' : '';
            $messageError = isset($errors['message']) ? $errors['message'] : '';
        ?>
      <div class="form__item form__item--wide <?=$messageClass; ?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?=$message; ?></textarea>
        <span class="form__error"><?=$messageError; ?></span>
      </div>
        <?php 
            $imgClass = isset($errors['lot-img']) ? 'form__item--invalid' : '';
            $imgError = isset($errors['lot-img']) ? $errors['lot-img'] : '';
        ?>
      <div class="form__item form__item--file <?=$imgClass; ?>">
        <label>Изображение <sup>*</sup></label>
        <div class="form__input-file">
          <input class="visually-hidden" type="file" id="lot-img" name="lot-img">
          <label for="lot-img">
            Добавить
          </label>
          <span class="form__error"><?=$imgError; ?></span>
        </div>
      </div>
      <div class="form__container-three">
        <?php 
            $rateClass = isset($errors['lot-rate']) ? 'form__item--invalid' : '';
            $rateError = isset($errors['lot-rate']) ? $errors['lot-rate'] : '';
        ?>
        <div class="form__item form__item--small <?=$rateClass; ?>">
          <label for="lot-rate">Начальная цена <sup>*</sup></label>
          <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?=$cost; ?>">
          <span class="form__error"><?=$rateError; ?></span>
        </div>
        <?php 
            $stepClass = isset($errors['lot-step']) ? 'form__item--invalid' : '';
            $stepError = isset($errors['lot-step']) ? $errors['lot-step'] : '';
        ?>
        <div class="form__item form__item--small <?=$stepClass; ?>">
          <label for="lot-step">Шаг ставки <sup>*</sup></label>
          <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?=$step; ?>">
          <span class="form__error"><?=$stepError; ?></span>
        </div>
        <?php 
            $dateClass = isset($errors['lot-date']) ? 'form__item--invalid' : '';
            $dateError = isset($errors['lot-date']) ? $errors['lot-date'] : '';
        ?>
        <div class="form__item <?=$dateClass; ?>">
          <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
          <input class="form__input-date" id="lot-date" type="text" name="lot-date" placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?=$date; ?>">
          <span class="form__error"><?=$dateError; ?></span>
        </div>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button" name='submit'>Добавить лот</button>
    </form>
    