var data = function (_this) {
    return {
        'action' : 'check',
        'field' : _this.name,
        'value' : _this.get_value()
    }
};
var fields = {
    'login' : {
        'url' : 'action.php',
        'regex_check' : true,
        'regex' : /^[A-Za-z][A-Za-z0-9]{3,31}$/,
        'errors' : {
            'regex' : 'Логин слишком короткий или содержит недопустимые символы',
            'ajax' : 'Пользователь с тамким логином уже существует'
        },
        'form_check_func' : data
    },
    'group' : {
        'url' : 'action.php',
        'regex_check' : true,
        'regex' : /^[0-9]{5,6}\/[0-9]{1,5}$/,
        'errors' : {
            'regex' : 'Номер группы неполный или содержит недопустимые символы',
        },
        'form_check_func' : data
    },
    'email' : {
        'url' : 'action.php',
        'regex_check' : true,
        'regex' : /^(([^<>()\[\].,;:\s@"]+(\.[^<>()\[\].,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
        'errors' : {
            'regex' : 'Недопустимый e-mail',
            'ajax' : 'Пользователь с такой почой уже существует'
        },
        'form_check_func' : data
    },
    'password' : {
        'regex_check' : true,
        'regex' : /(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/,
        'errors' : {
            'regex' : 'Слабый или содержит недопустимые символы'
        }
    },
    'password_confirm' : {
        'regex_check' : true,
        'regex' : /(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/,
        'errors' : {
            'regex' : 'Слабый или содержит недопустимые символы'
        }
    }
};

var triggers = {
    'group' : {
        'class' : 'input.group_ajax_ignore'
    }
};
$(document).ready(function () {
    var controller = new FieldsController();
    var arr = {};
    $('.validate').each(function () {
        var field_name = $(this).attr('name');
        if (fields[field_name] !== undefined) {
            var field = new Field($(this), fields[field_name], controller);
            arr[field_name] = field;
            controller.add_field(field);
            field.on("validate", function () {
                var label = $(field.field).siblings("label");
                switch (field.state) {
                    case 1:
                    case 3: $(label).css("color", "var(--green)"); break;
                    case 2: $(label).css("color", "var(--red)"); break;
                }
            });
        } else {
            console.warn("Unregistered field for validation : \"" + field_name + "\" -> ignore");
        }
    });
    var pass = arr['password'];
    var pass_c = arr['password_confirm'];
    //TODO убрать быдло-код
    pass.on('validate', function () {
        if (pass.value_not_empty() && pass_c.value_not_empty()) {
            if (pass.get_value() === pass_c.get_value()) {
                pass.validate();
                pass_c.validate();
            } else {
                pass.invalidate();
                pass_c.invalidate();
                pass.set_error("Пароли не совпадают");
                pass_c.set_error("Пароли не совпадают");
            }
        }
    });
    pass_c.on('validate', function () {
        if (pass.value_not_empty() && pass_c.value_not_empty()) {
            if (pass.get_value() === pass_c.get_value()) {
                pass.validate();
                pass_c.validate();
            } else {
                pass.invalidate();
                pass_c.invalidate();
                pass.set_error("Пароли не совпадают");
                pass_c.set_error("Пароли не совпадают");
            }
        }
    });
    //
    var button = new AjaxButton($('.on_valid'), arr, {
        "url" : "action.php",
        "data_from_func" : function (elem) {
            var data = {};
            Object.keys(elem.fields).map(function (key) {
                data[key] = elem.fields[key].get_value();
            });
            data["submit_request"] = "";
            data["action"] = "register";
            return data;
        }
    });
    button.on("success", function (data) {
        var result = new Response(data, ["title", "name", "url", "default", "group"]);
        if (result.error_list.length !== 0) {

        } else {
            var form = $("#registerForm");
            var table = $('<table style="width: 100%; height: 100%"></table>');
            var tr = $("<tr></tr>");
            var td = $("<td></td>");
            var result_div = $('<div id="result"></div>');
            $(result_div).append($('<i class="ui icon check green" id="icon">'))
                .append($('<div id="success">Успешно</div>'))
                .append($('<div id="text"></div>').text(result['title']));
            if (result['name'] !== undefined && result['url'] !== undefined)
                $(result_div).append($('<div id="mail"></div>').append("Проверьте вашу почту: ", $('<a href="'+ result['url'] +'"></a>').text(result['name'])));
            $(result_div).append($('<a href="' + result['default'] + '" id="group"></a>')
                .append($('<i class="ui icon calendar alternate outline">'), "Расписание группы " + result['group']));
            $(table).append($(tr).append($(td).append(result_div)));
            $(form).append(table);
            $(form).animate({
                width: '100%',
                height: '100%'
            }, 500, function () {
                var timeout = 50;
                $(result_div).show(0).children().each(function () {
                    var elem = this;
                    setTimeout(function () {
                        $(elem).fadeIn(400);
                    }, timeout);
                    timeout += 25;
                })
            });
            $("form").fadeOut(400);
            $("#title").fadeOut(400);

        }
    });
    controller.set_triggers(triggers);
});

