<html>
    <head>
        <meta charset="utf-8">
        <title>Регистрация группы</title>
        <link rel="stylesheet" type="text/css" href="css/semantic.css">
        <link rel="stylesheet" type="text/css" href="css/register.css">
        <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
        <script type="text/javascript" src="js/lib/jquery.formatter.min.js"></script>
        <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js"></script>
        <script type="text/javascript" src="js/register.js"></script>
    </head>
    <body>
    <table style="width: 100%; height: 100%;">
        <tr>
            <td>
                <div id="registerForm">
                    <div id="title">Регистрация</div>
                    <form>
                        <div>
                            <div>
                                <label for="login">Логин</label>
                                <input id="login" type="text" title="Login" name="login" placeholder="Логин" class="validate ajax" maxlength="32" required/>
                            </div>
                            <div>
                                <label for="group">Группа</label>
                                <input id="group" type="text" title="Group" name="group" placeholder="Группа" class="validate" maxlength="12" required/>
                            </div>
                            <div>
                                <label for="email">E-mail</label>
                                <input id="email" type="email" title="E-mail" name="email" placeholder="Электронная почта" class="validate ajax" maxlength="64" autocomplete="email" required/>
                            </div>
                            <div>
                                <label for="pass">Пароль</label>
                                <input id="pass" type="password" title="Password" name="password" placeholder="Введите пароль"
                                       class="validate"
                                       required/>
                            </div>
                            <div>
                                <label for="pass_c">Подтверждение пароля</label>
                                <input id="pass_c" type="password" title="Confirm Password" name="password_confirm"
                                       placeholder="Подтвердите пароль"
                                       class="validate" required/>
                            </div>
                        </div>
                        <button type="button" class="on_valid" name="submit_request" disabled>Отправить</button>
                    </form>
                </div>
                <div class="field_errors"></div>
            </td>
        </tr>
    </table>

    </body>
</html>
