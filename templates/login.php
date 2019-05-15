<nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $category) : ?>
        <li class="nav__item">
          <a href="lots-by-category.php?id=<?=$category['id']; ?>"><?=$category['name']; ?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <?php $formClass = empty($errors) ? '' : 'form--invalid'; ?>
    <form class="form container <?=$formClass; ?>" enctype="multipart/form-data" action="login.php" method="post"> <!-- form--invalid -->
      <h2>Вход</h2>
        <?php 
            $emailClass = isset($errors['email']) ? 'form__item--invalid' : '';
            $emailError = isset($errors['email']) ? $errors['email'] : '';
        ?>
      <div class="form__item <?=$emailClass; ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=$email; ?>">
        <span class="form__error"><?=$emailError; ?></span>
      </div>
        <?php 
            $passClass = isset($errors['password']) ? 'form__item--invalid' : '';
            $passError = isset($errors['password']) ? $errors['password'] : '';
        ?>
      <div class="form__item form__item--last <?=$passClass; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">
        <span class="form__error"><?=$passError; ?></span>
      </div>
      <button type="submit" class="button" name="submit">Войти</button>
    </form>