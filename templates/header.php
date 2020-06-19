<a href="https://<?=$_SERVER['HTTP_HOST'];?>"><img class="logo" src="assets/images/logo3.png" /></a>
<div id="login" <?=((\User\User::$user !== null) ? 'class="logged"' : "")?>>
    <?php if (\User\User::$user == null): ?>
        <i class="ui icon user circular" style="margin: 0;"></i>
    <?php else: ?>
        <img src="data/image/32/<?=\User\User::$user['id']?>.png" />
        <div id="logged"><?=\User\User::$user['login'];?></div>
    <?php endif; ?>
</div>
<?php if (\User\User::$user == null): ?>
    <div id="loginForm">
        <div id="loginTitle">Войти</div>
        <form action="auth.php?m=pass" method="post">
            <input type="text" title="login" name="login" placeholder="Логин" required/>
            <input type="password" title="password" name="password" placeholder="Пароль" required/>
            <input id="remember" type="checkbox" title="Remember me" name="remember">
            <label for="remember">Запомнить меня</label>
            <button type="submit">Войти</button>
            <div id="register"><a href="register.php">Регистрация</a></div>
        </form>
        <div id="auths" style="margin-top: 5px; color: var(--darkGray);    text-align: center;    font-size: 12px; text-decoration: none">
            <a href="http://oauth.vk.com/authorize?client_id=6463991&redirect_uri=https://polytable.ru/auth.php?m=vk&display=popup&response_type=code&v=5.74" style="text-decoration: none">
                <i class="ui icon circular inverted vk blue" style="height: 1em !important;"></i></a>
            <a href="https://accounts.google.com/o/oauth2/auth?client_id=513360273760-c6cpf9oqjikep1qeaegvvtod79i138fi.apps.googleusercontent.com&redirect_uri=https://polytable.ru/auth.php?m=google&response_type=code&scope=https://www.googleapis.com/auth/userinfo.email" style="text-decoration: none">
                <i class="ui icon circular google red" style="height: 1em !important;"></i>
            </a>
            <a href="https://oauth.yandex.ru/authorize?response_type=code&client_id=8f519b6836c247d5af94d5b7f55edb24&display=popup" style="text-decoration: none">
                <i class="ui icon circular yandex red" style="height: 1em !important;"></i>
            </a>
            <a href="https://connect.mail.ru/oauth/authorize?client_id=759857&response_type=code&redirect_uri=https://polytable.ru/auth.php?m=mail" style="text-decoration: none">
                <i class="ui icon circular at orange" style="height: 1em !important;"></i>
            </a>
        </div>
    </div>
<?php else: ?>
    <div id="user">
        <a href="groups.php?id=<?=\User\User::$user['group'];?>">Группа</a>
        <a href="profile.php">Профиль</a>
        <a href="logout.php">Выйти</a>
    </div>
<?php endif; ?>
