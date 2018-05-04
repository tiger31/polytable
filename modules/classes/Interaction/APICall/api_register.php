<?php
namespace Interaction\APICall;

use Interaction\Mail\TemplateMailer\Mailer;
use Interaction\Mail\TemplateMailer\Mail\ConfirmMail;
use Interaction\APICall;
use User\User;

class api_register extends APICall {
    function __construct($config) {
        $this->name = "register";
        $this->fields = json_decode('[{"name":"login","isset":true,"regex":true},{"name":"group","isset":true,"regex":true},{"name":"email","isset":true,"regex":true},{"name":"password","isset":true,"regex":true},{"name":"password_confirm","isset":true,"regex":true},{"name":"submit_request","isset":true}]', true);
        $this->bit_mask = 0;
        $this->user_needed = false;
        $this->force_csrf_check = false;
        $this->method = "POST";
        $this->input = $_POST;

        parent::__construct($config);
    }

    function handle() {
        global $mysql, $default_redirect;
        $valid = false;
        $login_free = $mysql->login_free($this->input['login']);
        if (!$login_free) {
            $this->response
                ->error(400, array("info" => "Login is taken", "affected_row" => "login", "state" => "taken"))
                ->response();
        }
        if (!($mysql->email_free($this->input['email']) || (User::$user && User::$user->email == $this->input['email'])))
           $this->response
                ->error(400, array("info" => "Email is taken", "affected_row" => "email", "state" => "taken"))
                ->response();
        $data = array(
            "login" => $this->input["login"],
            "email" => $this->input["email"],
            "group" => $this->input["group"],
            "password_hash" => password_hash($this->input['password'], PASSWORD_BCRYPT),
        );
        if ($mysql->exec(QUERY_USER_INSERT, RETURN_IGNORE, $data)) {
            $template = $_SERVER["DOCUMENT_ROOT"] . "/templates/mail.html";
            $mailer = new Mailer();
            $valid = $mailer->send(new ConfirmMail($template, array(
                "subject" => "Подтверждение регистрации",
                "to" => $data["email"],
                "login" => $data["login"],
                "target" => "ACCOUNT",
                "template" => array(
                    "host" => "polytable.ru",
                    "login" => $data['login']
                )
            )));
        } else {
            $valid = false;
        }
        if ($valid) {
            $arr = array();
            $arr['title'] = "На указанный вами e-mail было отправлено письмо с подтверждением";
            $arr['group'] = $data['group'];
            $mysql->set_active(QUERY_EMAIL_SELECT);
            $email = substr($data["email"],strpos($data["email"],"@") + 1);
            $data = $mysql->exec(QUERY_EMAIL_SELECT, RETURN_FALSE_ON_EMPTY, array("url" => $email));
            if ($data) {
                $arr['url'] = $data['url'];
                $arr['name'] = $data['name'];
            }
            $arr['default'] = "/groups.php?id=" . $this->input["group"];
            $this->response->response(true, $arr);
        } else {
            $this->response->response(false);
        }
    }
}