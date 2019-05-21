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
    <form class="form container <?=$formClass; ?>" enctype="multipart/form-data" action="sing-up.php" method="post" autocomplete="off">
      <h2>Регистрация нового аккаунта</h2>
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
      <div class="form__item <?=$passClass; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">
        <span class="form__error"><?=$passError; ?></span>
      </div>
        <?php
            $nameClass = isset($errors['name']) ? 'form__item--invalid' : '';
            $nameError = isset($errors['name']) ? $errors['name'] : '';
        ?>
      <div class="form__item <?=$nameClass; ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя" value="<?=$newUserName; ?>">
        <span class="form__error"><?=$nameError; ?></span>
      </div>
        <?php
            $messageClass = isset($errors['message']) ? 'form__item--invalid' : '';
            $messageError = isset($errors['message']) ? $errors['message'] : '';
        ?>
      <div class="form__item <?=$messageClass; ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?=$message; ?></textarea>
        <span class="form__error"><?=$messageError; ?></span>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button" name="submit">Зарегистрироваться</button>
      <a class="text-link" href="login.php">Уже есть аккаунт</a>
    </form>