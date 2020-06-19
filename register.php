<?php header("pragma","no-cache"); ?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="theme-color" content="#105d3b">
        <title>Регистрация группы</title>
        <link rel="icon" type="image/png" href="assets/favicon-16x16.png" sizes="16x16">
        <link rel="icon" type="image/png" href="assets/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="assets/favicon-96x96.png" sizes="96x96">
        <link rel="stylesheet" type="text/css" href="css/icon.css">
        <link rel="stylesheet" type="text/css" href="css/register.css?1337">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700&amp;subset=cyrillic" rel="stylesheet">
        <!-- Yandex.Metrika counter --> <script type="text/javascript" > (function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter48396962 = new Ya.Metrika({ id:48396962, clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://mc.yandex.ru/metrika/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks"); </script> <noscript><div><img src="https://mc.yandex.ru/watch/48396962" style="position:absolute; left:-9999px;" alt="" /></div></noscript> <!-- /Yandex.Metrika counter -->
        <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
        <script type="text/javascript" src="js/lib/jquery.formatter.min.js"></script>
        <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js?1337"></script>
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
                                <div class="errorMessage"></div>
                            </div>
                            <div>
                                <label for="group">Группа</label>
                                <input id="group" type="text" title="Group" name="group" placeholder="Группа" class="validate" maxlength="12" required/>
                                <div class="errorMessage"></div>
                            </div>
                            <div>
                                <label for="email">E-mail</label>
                                <input id="email" type="email" title="E-mail" name="email" placeholder="Электронная почта" class="validate ajax" maxlength="64" autocomplete="email" required/>
                                <div class="errorMessage"></div>
                            </div>
                            <div>
                                <label for="pass">Пароль</label>
                                <input id="pass" type="password" title="Password" name="password" placeholder="Введите пароль"
                                       class="validate" autocomplete="new-password"
                                       required/>
                                <div class="errorMessage"></div>
                            </div>
                            <div>
                                <label for="pass_c">Подтверждение пароля</label>
                                <input id="pass_c" type="password" title="Confirm Password" name="password_confirm"
                                       placeholder="Подтвердите пароль" autocomplete="new-password"
                                       class="validate" required/>
                                <div class="errorMessage"></div>
                            </div>
                        </div>
                        <button type="button" class="on_valid" name="submit_request">Отправить</button>
                    </form>
                </div>
                <div class="field_errors"></div>
            </td>
        </tr>
    </table>

    </body>
</html>
