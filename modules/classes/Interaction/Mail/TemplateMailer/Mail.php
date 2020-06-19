<?php

namespace Interaction\Mail\TemplateMailer;


class Mail {

    public $template = null;
    public $subject;
    public $to;

    public function __construct($template_file, $data = array()) {
        $template = file_get_contents($template_file);
        $this->subject = $data['subject'];
        $this->to = $data['to'];
        if (isset($data['template']))
            foreach ($data['template'] as $key => $value) {
                $template = str_replace("{{{$key}}}", $value, $template);
            }
        $this->template = $template;
    }

}