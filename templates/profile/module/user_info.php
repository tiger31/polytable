<div class="module" module="<?=$this->module_name;?>" group="<?=$this->chain;?>">
    <div class="box">
        <div class="ui card">
            <div class="image">
                <img src="data/image/256/<?=$this->user->getID();?>.png">
            </div>
            <div class="content">
                <i class="ui icon write" id="edit"></i>
                <a class="header">
                    <?=$this->user->getEscapedName();?>
                </a>
                <div class="meta">
                    <span class="date"><?=$this->user->getFullPost();?></span>
                </div>
                <div class="description<?=($this->user->number == null) ? " hidden" : "";?>" id="number">
                    <i class="ui icon call"></i>
                    <span class="value">Телефон: </span><span class="swap"><?=$this->user->number;?></span>
                    <input class="validate hidden" placeholder="+7(999)-999-99-99" name="number"/>
                </div>
                <div class="description<?=($this->user->email == null) ? " hidden" : "";?>" id="email">
                    <i class="ui icon mail"></i>
                    <span class="value">E-mail: </span><span class="swap"><?=$this->user->email;?></span>
                    <input class="validate hidden" placeholder="example@example.com" name="email"/>
                </div>
                <div class="description<?=($this->user->vk_link == null) ? " hidden" : "";?>" id="vk_link">
                    <i class="ui icon vk"></i>
                    <span class="value">VK: </span><span class="swap"><? if ($this->user->vk_link != null): ?><a href="<?=$this->user->vk_link;?>" target="_blank"><?=$this->user->vk_link;?></a><? endif;?></span>
                    <input class="validate hidden" placeholder="https://vk.com/id0" name="vk_link"/>
                </div>
            </div>
            <? if($this->user->verified): ?>
            <div class="ui bottom attached green disabled button" id="verify">
                <i class="ui icon checkmark"></i>
                Подтверждено
            </div>
            <? else: ?>
            <div class="ui bottom attached green button" id="verify">
                <i class="ui icon write"></i>
                Заполнить профиль
            </div>
            <? endif;?>
        </div>
    </div>
</div>
<script type="text/javascript">
    var fields = {
        'number': {
            'regex': /^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/,
            'regex_check': true,
            'empty_valid': true,
            'mask': {
                'pattern': '+7({{999}})-{{999}}-{{99}}-{{99}}',
                'persistent': false
            },
            'show_errors': false,
            'errors': {
                'regex': "Понятия не имею, что вы сделали, чтобы увидеть это сообщение"
            }
        },
        'vk_link': {
            'regex': /^https:\/\/vk.com\/[A-Za-z0-9_]{1,32}$/,
            'regex_check': true,
            'empty_valid': true,
            'show_errors': false,
            'errors': {
                'regex': "Недопустимая ссылка на профиль VK"
            }
        },
        'email': {
            'url': 'action.php',
            'regex_check' : true,
            'ajax_check' : true,
            'empty_valid': true,
            'regex': /^(([^<>()\[\].,;:\s@"]+(\.[^<>()\[\].,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
            'show_errors': false,
            'errors': {
                'regex': 'Недопустимый e-mail',
                'ajax': 'Пользователь с такой почой уже существует'
            },
            'form_check_func': function (_this) {
                return {
                    'action': 'check',
                    'field': _this.name,
                    'value': _this.get_value()
                }
            }
        }
    };
    var controller = new FieldsEdit(fields, $("#edit"), $("#verify"));

</script> 