moment.locale("ru");
function me(data) {
    AjaxModule.apply(this, [me.config]);
    this.data = data;
    this.forms = {};
    this.on("ready", function () {
        const _this = this;
        for(let form of Object.keys(me.AjaxFieldsForms)) {
            const fields = me.AjaxFieldsForms[form]['fields'];
            let load_data = {};
            let obj_fields = {};
            //Setting up form
            this.forms[form] = new FieldsController();
            for (let field of fields) {
                obj_fields[field] = new Field($(this.html[field]), {
                    url : "action.php",
                    regex : me.AjaxFieldsRegex[field],
                    empty_valid : me.AjaxFieldsAllowEmpty.includes(field),
                    ajax_check : me.AjaxFieldsAjax.includes(field),
                    form_check_func : me.AjaxFieldsFunc,
                    errors : (me.AjaxFieldsErrorMessages[field]) ? me.AjaxFieldsErrorMessages[field]['errors'] : {},
                    mask : me.AjaxFieldsFromatters[field]
                });
                if (me.AjaxFieldsErrorMessages[field])
                    obj_fields[field].on("validate ajax_received", function () {
                        $(_this.html[me.AjaxFieldsErrorMessages[field]['html']]).text(obj_fields[field].error || "")
                    });
                this.forms[form].add_field(obj_fields[field]);
                if (this.data[field])
                    load_data[field] = data[field];
            }
            if (me.AjaxFieldsForms[form]['password_field']) {
                const confirm_field = obj_fields[me.AjaxFieldsForms[form]['confirm_field']];
                confirm_field.controller = this.forms[form];
                const password_field = new PasswordField($(this.html[me.AjaxFieldsForms[form]['password_field']]), {
                    valid_strength: 2
                });
                obj_fields[me.AjaxFieldsForms[form]['password_field']] = password_field;
                password_field.set_confirm(confirm_field);
                this.forms[form].add_field(password_field);
                password_field.on("validate", function () {
                    if (!this.value_not_empty()) {
                        $(_this.html['password_text']).text("");
                        $(_this.html['password_color']).css("display", "none");
                        return;
                    }
                    switch (this.current_strength) {
                        case 0:
                            $(_this.html['password_text']).text("Пароль содержит недопустимые символы").css("color", "var(--red)");
                            $(_this.html['password_color']).css("display", "none");
                            break;
                        case 1:
                            $(_this.html['password_text']).text("Слабый").css("color", "var(--red)");
                            $(_this.html['password_color']).css({
                                display : "block",
                                width: "calc((100% - 178px) / 3)",
                                "background-color": "var(--red)"
                            });
                            break;
                        case 2:
                            $(_this.html['password_text']).text("Средний").css("color", "var(--orange)");
                            $(_this.html['password_color']).css({
                                display : "block",
                                width: "calc((100% - 178px) / 2)",
                                "background-color": "var(--orange)"
                            });
                            break;
                        case 3: {
                            $(_this.html['password_text']).text("Сильный").css("color", "var(--green)");
                            $(_this.html['password_color']).css({
                                display : "block",
                                width: "calc(100% - 178px)",
                                "background-color": "var(--green)"
                            });
                            break;
                        }
                    }
                })
            }
            //Setting up button to send data on server
            this.forms[form].button = new AjaxButton($(this.html[me.AjaxFieldsForms[form]['button']]), obj_fields, {
                url : "action.php",
                method : "POST",
                data :  me.AjaxFieldsForms[form]['button_data']
            }, this.forms[form]);
            this.forms[form].button.response_block = this.html[me.AjaxFieldsForms[form]['form_response']];
            if (me.AjaxFieldsForms[form]['success']) {
                this.forms[form].button.on("success", me.AjaxFieldsForms[form]['success']);

            }
            this.forms[form].button.valid_check = false;
            this.forms[form].button.on("activate disable", function () {
                if (this.disabled)
                    $(this.button).addClass("disabled");
                else
                    $(this.button).removeClass("disabled");
            });
            this.forms[form].button.disable();
            //Lock form if needed, Lock means, same values as loaded would be invalid
            if (me.AjaxFieldsForms[form]['load'])
                this.forms[form].load(load_data);
            if (me.AjaxFieldsForms[form]['lock'])
                this.forms[form].lock();
        }
        console.log(this);
    });
}
me.config = {
    name : "me",
    load_node : "#modules",
    templates : {
        main : "me",
        templates : [
            {
                name : "me",
                path : "templates/profile/me.handlebars",
                type : "template"
            }
        ]
    },
    events : {

    }
};

/* ---------- Fields settings for module ---------- */
me.AjaxFieldsForms = {
    "user-data-edit" : {
        fields : ["email", "number"],
        button : "user-data",
        form_response : "user-data-response",
        button_data : function (button) {
            return {
                action : "user",
                type : "change",
                number : button.fields["number"].get_value()
            }
        },
        success: function (response) {
            if (response['response']) {
                $(this.response_block).removeClass("error").text("Сохранено");
                this.controller.lock();
                this.disable();
            } else {
                $(this.response_block).addClass("error").text("Ошибка");
            }
        },
        load : true,
        lock : true
    },
    "user-change-password" : {
        fields : ["password", "confirm_password"],
        password_field : "new_password",
        confirm_field : "confirm_password",
        button : "user-password",
        form_response : "user-password-response",
        button_data : function (button) {
            return {
                action : "user",
                type : "password",
                password : button.fields["password"].get_value(),
                new_password : button.fields["new_password"].get_value(),
            }
        },
        success: function (response) {
            if (response['response']) {
                $(this.response_block).removeClass("error").text("Сохранено");
                this.controller.clear();
            } else {
                $(this.response_block).addClass("error").text("Ошибка");
            }
        }
    }
};
me.AjaxFieldsFunc = function (_this) {
    return {
        'action' : 'check',
        'field' : _this.name,
        'value' : _this.get_value()
    }
};
me.AjaxFieldsRegex = {
    "email" : /^(([^<>()\[\].,;:\s@"]+(\.[^<>()\[\].,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
    "number" : /^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/
};
me.AjaxFieldsAjax = ["email"];
me.AjaxFieldsAllowEmpty = ["number"];
me.AjaxFieldsErrorMessages = {
    "email" : {
        html : "email_error",
        errors : {
            'regex': 'Неверный адрес электронной почты',
            'ajax': 'Пользователь с такой почтой уже существует'
        }
    },
};
me.AjaxFieldsFromatters = {
    "number" : {
        'pattern': '+7({{999}})-{{999}}-{{99}}-{{99}}',
        'persistent': false
    }
};
/* ---------- End of fields settings ---------- */

me.prototype = Object.create(AjaxModule.prototype);
me.prototype.constructor = me;
me.prototype.template_data = function () {
    return Object.assign(this.template_object(), {
        id : this.data["id"],
        login : this.data["login"],
        title : this.data["title"],
        group : this.data["group"],
        email : this.data["email"],
        vk_linked: (this.data['vk']),
        vk : (this.data['vk']) ? `https://vk.com/id${this.data['vk']}` : "Не привязан",
        number : this.data['number'],
        changed : moment(this.data['password_changed']).fromNow()
    });
};