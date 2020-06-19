<div id="title">
    <?php if ($is_head): ?>
        Ваша заявка приянта, в скором времени она будет рассмотрена
    <?php else: ?>
        На указанный вами e-mail было отправлено письмо с подтверждением
    <?php endif;?>
</div>

<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/modules/Connect.php";
$mysql->set_active(QUERY_EMAIL_SELECT);
$email = substr($data["email"],strpos($data["email"],"@") + 1);
$data = $mysql->exec(QUERY_EMAIL_SELECT, RETURN_FALSE_ON_EMPTY, array("url" => $email));
?>
<?php if(!$data): ?>
    <a href="<?=$default_redirect?>"><button>Вернуться на главную страницу</button></a>
<?php else: ?>
    <a href="<?=$data["url"]?>"><button>Перейти на <?=$data["name"]?></button></a>
<?php endif; ?>