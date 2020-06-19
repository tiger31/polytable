<?php

namespace Interaction\Mail\TemplateMailer\Mail;

use Interaction\Mail\TemplateMailer\Mail;
use Configuration\Validation\ConfirmHash;


class ConfirmMail extends Mail {



    private $link = "https://polytable.ru/confirm.php";
    private $hash;

    public function __construct($template_file, array $data = array()) {
        $this->hash = new ConfirmHash($data['login'], $data['target'], 21600, (isset($data['store']) ? $data['store'] : null));
        $this->hash->set();
        $data['template']['confirm_link'] = $this->link . "?" . urldecode(http_build_query(array("login" => $data['login'], "hash" => $this->hash->hash)));
        parent::__construct($template_file, $data);
    }

}