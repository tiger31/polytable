function Field(field, statement, controller) {
    this.emitter = new Emitter("changed validate ajax_ready ajax_received", this);
    //Default filed info
    this.field = field;
    this.name = $(field).attr("name");
    this.controller = controller;
    //Regex for current field
    this.regex_check = (statement['regex_check'] !== undefined) ? statement['regex_check'] : false;
    this.ajax_check = (statement['ajax_check'] !== undefined) ? statement['ajax_check'] : $(field).hasClass("ajax");

    this.regex = statement['regex'];
    //Default class set
    this.default_class = $(field).attr("class").split(' ');
    //Ajax
    this.url = statement['url'];
    this.ajax_ignore_statement = statement['ajax_ignore'];
    this.ajax_passed = true;
    this.ajax_timer = undefined;
    //Field state needs for group checks
    this.state = 1;
    //Error message if field value is "invalid"
    this.show_errors = (statement['show_errors'] !== undefined) ? statement['show_errors'] : true;
    this.empty_valid = (statement['empty_valid'] !== undefined) ? statement['empty_valid'] : false;
    this.error_list = statement['errors'];
    this.error = undefined;
    this.error_tag = $('<div></div>').addClass('errorMessage');

    this.form_check_func = statement['form_check_func'];

    this.log_level = (statement['log_level'] !== undefined) ? statement['log_level'] : 0;

    if (statement['errors_block'] !== undefined) {
        if(!$("*").is(statement['errors_block'])) {
            $(this.field).append(this.error_tag);
            if (this.log_level > 1)
                console.warn("Error log field (" + statement['errors_block'] + ") set, but it doesn't exist");
        } else {
           $(statement['errors_block']).append(this.error_tag);
        }
    } else {
        $(this.field).parent().append(this.error_tag);
    }

    if (statement['mask'] !== undefined) $(this.field).formatter(statement['mask']);
    this.on_change();
    var _this = this;
    $(this.field).on("change insert keyup", function () {
        _this.on_change();
    });
}
Field.prototype = {
    constructor: Field,
    get_value:function () {
        return $(this.field).val();
    },
    setState:function (state) {
        this.state = state;
    },
    value_not_empty:function () {
        var value = this.get_value();
        return (value !== undefined && value !== null && value !== "");
    },
    preg_match:function () {
        var result = this.regex.test(this.get_value());

        if (!result) this.set_error(this.error_list['regex']);
        return result;
    },
    valid:function () {
        return (this.state === 3 && (!this.ajax_check || this.ajax_passed)) || (this.state === 1 && this.empty_valid);
    },
    validate:function () {
        $(this.field).removeClass("invalid");
        $(this.field).addClass("valid");
        $(this.error_tag).hide(0);
        this.error = undefined;
        this.setState(3);
    },
    invalidate:function () {
        $(this.field).addClass("invalid");
        $(this.field).removeClass("valid");
        this.setState(2)
    },
    clear:function () {
        $(this.field).attr("class", this.default_class.join(' '));
        $(this.error_tag).hide(0);
        this.error = undefined;
        this.setState(1);
    },
    set_error:function (message) {
        this.error = message;
        if (this.error !== undefined && this.show_errors){
            $(this.error_tag).text(message);
            $(this.error_tag).show(0);
        }
    },
    set_error_block:function (func) {
        func(this.error_tag, this.field);
    },
    ajax:function (_this) {
        _this.emitter.emit("ajax_ready");
        $.getJSON({
                url: _this.url,
                data: _this.form_check_request()
            },
            function ( data, status ) {
                if (data['error'] === undefined) {
                    if (data['response'] === true) {
                        _this.ajax_passed = true;
                        _this.validate();
                    } else {
                        _this.ajax_passed = false;
                        _this.error = _this.set_error(_this.error_list['ajax']);
                        _this.invalidate();
                    }
                } else {
                    _this.set_error(_this.error_list['ajax_success_failed']);
                    if (this.log_level > 1)
                        console.warn(data['error']);
                }
                if (status !== "success") {
                    _this.set_error(_this.error_list['ajax_success_failed']);
                    if (this.log_level > 1)
                        console.warn(status);
                }
                _this.emitter.emit("ajax_received", data, status);
            });
    },
    ajax_ignore:function () {
        return (this.ajax_ignore_statement !== undefined && this.ajax_ignore_statement());
    },
    on_change:function () {
        this.emitter.emit("changed", this.get_value());
        if (this.value_not_empty()) {
            if ((!this.regex_check || this.preg_match()))
                this.validate();
            else
                this.invalidate();
            //Ajax part
            if (((this.ajax_check && this.valid()) || !this.ajax_passed) && !this.ajax_ignore()){
                if(this.ajax_timer !== undefined)
                    clearTimeout(this.ajax_timer);
                this.ajax_timer = setTimeout(this.ajax, 500, this);
            }
        } else {
            console.log("cleared");
            this.clear();
        }
        if (this.error !== undefined && this.log_level > 2) console.log(this.error);
        this.emitter.emit("validate");
    },
    form_check_request: function () {
        return $.param(this.form_check_func(this));
    },
    on: function (event, func) {
        this.emitter.on(event, func);
    }
};
function FieldsController() {
    this.submit = $('.on_valid');
    this.fields = [];
    this.callback = this.on_valid;
}
FieldsController.prototype = {
    constructor: FieldsController,
    add_field: function (field) {
        var _this = this;
        this.fields.push(field);
        field.on("validate", function () {
            _this.on_valid();
        })
    },
    on_valid:function () {
        var _this = this;
        var valid = true;
        this.fields.forEach(function (element) {
            if (!element.valid()) {
                valid = false;
                $(_this.submit).prop("disabled", !valid);
                return valid;
            }
        });
        $(this.submit).prop("disabled", !valid);
    },
    set_triggers:function (triggers) {
        this.fields.forEach(function (element) {
            if (triggers[element.name] !== undefined) {
                if ($('*').is(triggers[element.name]['class'])) {
                    $(triggers[element.name]['class']).on("change", function () {
                        element.on_change();
                    });
                } else
                    if (this.log_level > 1)
                        console.warn("Trigger for field " + element.name + " exists, but there is no element " + triggers[element.name]['class']);
            }
        })
    }
};
function AjaxButton (button, fields, statement, controller) {
    this.emitter = new Emitter("success sent disable activate", this);
    this.controller = (controller !== undefined && controller !== null) ? controller : null;
    this.button = button;
    this.fields = fields;
    this.url = statement['url'];
    this.data_from_func = statement['data_from_func'];
    this.callback = statement['callback'];
    this.disabled = false;
    this.valid_check = true;

    var _this = this;

    $(this.button).on("click", function () {
        if ((!_this.valid_check ||_this.valid()) && !_this.disabled) {
            _this.send(_this);
        }
    })
}
AjaxButton.prototype =  {
    constructor: AjaxButton,
    send:function (_this) {
        this.emitter.emit("sent");
        $.getJSON({
            url: _this.url,
            data: _this.get_data(),
            success: function (data) {
                _this.emitter.emit("success", data);
            }
        })
    },
    valid:function () {
        var valid = true;
        for (var key in this.fields) {
            if (this.fields.hasOwnProperty(key))
                if (!this.fields[key].valid()) {
                    valid = false;
                    break;
                }
        }
        return valid;
    },
    get_data:function () {
        var _this = this;
        return $.param(this.data_from_func(_this));
    },
    disable:function () {
        this.disabled = true;
        this.emitter.emit("disable");
    },
    activate:function () {
        this.disabled = false;
        this.emitter.emit("activate");
    },
    on: function (event, func) {
        this.emitter.on(event, func);
    }
};
function Emitter(events, consumer) {
    this.events = {}; //Wut?
    this.consumer = consumer;
    this.register_events(events);
}
Emitter.prototype = {
    constructor: Emitter,
    str_to_events: function (str) {
        return str.split(/\s+/);
    },
    register_events: function (events) {
        var _this = this;
        this.str_to_events(events).forEach(function (element) {
            _this.events[element] = [];
        });
    },
    on: function (events, func) {
        var _this = this;
        this.str_to_events(events).forEach(function (element) {
            if (_this.events[element] !== undefined && _this.events[element] !== null) {
                _this.events[element].push(func);
            } else {
                throw new Error("Event " + element + " not supported");
            }
        });
    },
    addEventListener: this.on,
    off: function (events) {
        var _this = this;
        this.str_to_events(events).forEach(function (element) {
            if (_this.events[element] !== undefined && _this.events[element] !== null) {
                _this.events[element] = [];
            } else {
                throw new Error("Event " + element + " not supported");
            }
        });
    },
    removeEventListener: this.off,
    //While emitting, all the others args except event are passed to an event
    emit: function (event) {
        var _this = this;
        var args = Array.apply(null, arguments);
        args = args.slice(1, arguments.length);
        if (this.events[event] !== undefined && this.events[event] !== null) {
            this.events[event].forEach(function (func) {
                func.apply(_this.consumer, args);
            });
        } else {
            throw new Error("Event " + event + " not supported");
        }
    },
    trigger: this.emit
};
function Response(data, fields) {
    this.data = data;
    this.result = false;
    this.error_list = [];
    var _this = this;
    if (data['errors'] !== undefined) {
        data['errors'].forEach(function (element) {
            _this.error_list.push(element);
        })
    } else if (data['response'] !== undefined) {
        this.result = data['response'];;
        fields.forEach(function (field) {
            var value = data[field];
            if (value !== undefined) {
                _this[field] = value;
            }
        });
    }
    return this;
}
