<img class="logo" src="assets/images/logo3.png" />
<div id="login" <?=(isset($_SESSION["user"]) and $_SESSION['user'] !== null) ? 'class="logged"' : ""?>>
    <?php if (!isset($_SESSION["user"]) or $_SESSION['user'] === null): ?>
        <img src="assets/images/login2.png"/>
    <?php else: ?>
        <img src="data/image/32/<?=$_SESSION['user']->getID()?>.png" />
        <div id="logged"><?=$_SESSION['user']->login;?></div>
    <?php endif; ?>
</div>
<?php if (!isset($_SESSION["user"]) or $_SESSION['user'] === null): ?>
    <div id="loginForm">
        <div id="loginTitle">Войти</div>
        <form action="login.php" method="post">
            <input type="text" title="login" name="login" placeholder="Логин" required/>
            <input type="password" title="password" name="password" placeholder="Пароль" required/>
            <button type="submit">Войти</button>
            <div id="register">Староста? <a href="register.php">Оставьте заявку группы</a></div>
        </form>
    </div>
<?php else: ?>
    <div id="user">
        <a href="groups.php?id=<?=$_SESSION['user']->group;?>">Группа</a>
        <a href="profile.php">Профиль</a>
        <a href="logout.php">Выйти</a>
    </div>
<?php endif; ?>
