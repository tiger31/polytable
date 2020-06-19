$.ajaxSetup({
    beforeSend: function (xhr) {
        const cookie = document.cookie.match(/X-CSRF-TOKEN=([\w]+)/);
        xhr.setRequestHeader("x-csrf-token", (cookie) ? cookie[1] : null);
    },
    dataType: "json"
});
function Field(field, config) {
    this.emitter = new Emitter("changed validate ajax_ready ajax_received invalid", this);
    //Default filed info
    this.field = field;
    this.name = $(field).attr("name");
    this.last_value = undefined;
    //Regex for current field
    this.regex = config['regex'] || null;
    this.ajax_check = (config['ajax_check'] !== undefined) ? config['ajax_check'] : $(field).hasClass("ajax");
    this.regex_check = !!(this.regex);
    //Default class set
    this.default_class = ($(field).attr("class") || "").split(' ');
    //Ajax
    this.url = config['url'];
    this.ajax_ignore_statement = config['ajax_ignore'];
    this.ajax_passed = true;
    this.ajax_timer = undefined;
    //Field state needs for group checks
    this.state = 1;
    //Error message if field value is "invalid"
    this.empty_valid = (config['empty_valid'] !== undefined) ? config['empty_valid'] : false;
    this.error_list = config['errors'] || {};

    this.error = undefined;

    this.form_check_func = config['form_check_func'];

    this.log_level = (config['log_level'] !== undefined) ? config['log_level'] : 0;

    if (config['mask'] !== undefined) $(this.field).formatter(config['mask']);

    this.on_change();

    const _this = this;

    $(this.field).on("change insert keyup", function () {
        if (_this.get_value() !== _this.last_value)
            _this.on_change();
        _this.last_value = _this.get_value();
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
        const value = this.get_value();
        return (value !== undefined && value !== null && value !== "");
    },
    preg_match:function () {
        const result = this.regex.test(this.get_value());

        if (!result) this.set_error(this.error_list['regex']);
        return result;
    },
    valid:function () {
        return (this.state === 3 && (!this.ajax_check || this.ajax_passed)) || (this.state === 1 && this.empty_valid);
    },
    validate:function () {
        $(this.field).removeClass("invalid");
        $(this.field).addClass("valid");
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
        this.error = undefined;
        this.setState(1);
    },
    set_error:function (message) {
        this.error = message;
        this.emitter.emit("invalid", message);
    },
    ajax: function (_this, value) {
        _this.emitter.emit("ajax_ready");
        if (!_this.value_not_empty()) return;
        $.getJSON({
                url: _this.url,
                data: _this.form_check_request()
            },
            function ( data, status ) {
                if (data['error'] === undefined) {
                    if (value === _this.get_value())
                        if (data['response'] === true) {
                            _this.ajax_passed = true;
                            _this.validate();
                        } else {
                            _this.ajax_passed = false;
                            _this.set_error(_this.error_list['ajax']);
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
        this.clear();
        if (this.value_not_empty()) {

            const valid = (!this.regex_check || this.preg_match());

            //Ajax part
            if (((this.ajax_check && valid) || !this.ajax_passed) && !this.ajax_ignore()){
                if(this.ajax_timer !== undefined)
                    clearTimeout(this.ajax_timer);
                this.ajax_timer = setTimeout(this.ajax, 500, this, this.get_value());
            } else if (!this.ajax_check || !valid){
                (valid) ? this.validate() : this.invalidate();
            }
        } else {
            this.clear();
        }
        if (this.error !== undefined && this.log_level > 2) console.log(this.error);
        this.emitter.emit("validate");
    },
    form_check_request: function () {
        return $.param(this.form_check_func(this));
    },
    on: function (events, func) {
        this.emitter.on(events, func);
    }
};
function FieldsController() {
    this.emitter = new Emitter("valid");
    this.button = undefined;
    this.locked = false;
    this.changed = false;
    this.fields = [];
    this.stored = {};
}
FieldsController.prototype = {
    constructor: FieldsController,
    add_field: function (field) {
        const _this = this;
        this.fields.push(field);
        field.on("validate ajax_received", function () {
            _this.on_valid();
        })
    },
    on_valid: function () {
        const _this = this;
        let valid = true;
        let changed = false;
        this.fields.forEach(function (element) {
            if (!element.valid()) {

                valid = false;
            } else if (_this.locked) {
                if (element.get_value() !== _this.stored[element.name]) {
                    changed = true;
                }
            }
        });
        this.changed = changed;
        if (valid && (!this.locked || (this.locked && this.changed)))
            this.button.activate();
        else
            this.button.disable();
    },
    load: function (data) {
        for (let field of this.fields) {
            if (data[field.name]) {
                $(field.field).val(data[field.name]);
                field.on_change();
            }
        }
    },
    store: function () {
        for (let field of this.fields) {
            this.stored[field.name] = field.get_value();
        }
    },
    lock: function () {
        this.store();
        this.locked = true;
        this.changed = false;
    },
    clear: function () {
        for (let field of this.fields) {
            $(field.field).val("");
            field.on_change();
        }
    }
};
function AjaxButton (button, fields, statement, controller) {
    this.emitter = new Emitter("success error sent disable activate", this);
    this.controller = (controller !== undefined && controller !== null) ? controller : null;
    this.button = button;
    this.fields = fields;
    this.url = statement['url'];
    this.data = statement['data'];
    this.method = statement['method'] || "GET";
    this.disabled = false;
    this.valid_check = true;

    const _this = this;

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
        $.ajax({
            url: _this.url,
            method: _this.method,
            data: _this.get_data(),
            success: function (data) {
                _this.emitter.emit("success", data);
            },
            error: function () {
                _this.emitter.emit("error", ...arguments);
            }
        })
    },
    valid: function () {
        let valid = true;
        for (let key in this.fields) {
            if (this.fields.hasOwnProperty(key))
                if (!this.fields[key].valid()) {
                    valid = false;
                    break;
                }
        }
        return valid;
    },
    get_data:function () {
        let _this = this;
        return $.param(this.data(_this));
    },
    disable: function () {
        this.disabled = true;
        this.emitter.emit("disable");
    },
    activate: function () {
        this.disabled = false;
        this.emitter.emit("activate");
    },
    on: function (event, func) {
        this.emitter.on(event, func);
    }
};

//---------- Emitter ----------//

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
        const _this = this;
        this.str_to_events(events).forEach(function (element) {
            _this.events[element] = [];
        });
    },
    on: function (events, func) {
        const _this = this;
        this.str_to_events(events).forEach(function (element) {
            if (_this.events[element] !== undefined && _this.events[element] !== null) {
                _this.events[element].push(func);
            } else {
                throw new Error("Event " + element + " not supported");
            }
        });
    },
    addEventListener: this.on,
    off: function (events, func) {
        const _this = this;
        this.str_to_events(events).forEach(function (element) {
            if (_this.events[element] !== undefined && _this.events[element] !== null) {
                const index = _this.events[element].indexOf(func);
                _this.events[element].splice(index, 1);
            } else {
                throw new Error("Event " + element + " not supported");
            }
        });
    },
    removeEventListener: this.off,
    //While emitting, all the others args except event are passed to an event
    emit: function (event) {
        const _this = this;
        let args = Array.apply(null, arguments);
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

//---------- Handlebars for iterator ----------//
if (Handlebars !== undefined)
    Handlebars.registerHelper('times', function(n, block) {
        let content = '';
        for(let i = 0; i < n; ++i)
            content += block.fn(this);
        return content;
    });
//---------- Linker ----------//

function Linker(module) {
    this.emitter = new Emitter("linked", this);
    if (!(module instanceof AjaxModule))
        throw new LinkerError("module is not instance of AjaxModule");
    this.module = module;
    if (this.module.submodule) {
        this.parent = this.module.parent.linker;
    }
    this.html_roots = {};
    this.link_roots = {};
    this.links = {};

    this.out_roots = [];
    this.html = {};
}
Linker.create_index = function (length) {
        let text = "";
        let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (let i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
};
Linker.helper = Handlebars.registerHelper("linker", function (l, link, options) {
    let root = options.hash.r || false;
    let root_for = options.hash['r_for'] || "__main__";
    if (!(l instanceof Linker)) {
        throw new LinkerError("linker is not instance of Linker");
    }
    const index = Linker.create_index(8);
    const attr = 'linker=' + index;
    if (root) {
        if (l.parent) {
            l.parent.out_roots.push({
                index: index,
                linker: l,
                for : root_for,
                name : link
            });
        }
    } else
        l.set_link(link, index);
    return attr;
});
Linker.prototype = {
    set_link : function (object, link) {
        if (this.links[object] !== undefined) {
            if (Array.isArray(this.links[object]))
                this.links[object].push(link);
            else
                this.links[object] = [this.links[object], link];
        } else {
            this.links[object] = link;
        }
    },
    link: function () {
        if (this.parent && this.parent.out_roots.length > 0)
            this.parent.uplink();
        //
        if (Object.keys(this.html_roots).length === 0 || !this.html_roots['__main__'])
            this.html_roots['__main__'] = $(this.module.get_node());
        //
        for (let object of Object.keys(this.links)) {
            if (this.links.hasOwnProperty(object)) {
                const link = this.links[object];
                const root = (this.html_roots[object] === undefined) ? $(this.html_roots['__main__']) : $(this.html_roots[object]);
                if (!Array.isArray(link)) {
                    this.html[object] = $(root).find(`*[linker="${link}"]`).removeAttr("linker");
                }
                else {
                    this.html[object] = [];
                    for (let i = 0; i < link.length; i++)
                        this.html[object].push($(root).find(`*[linker="${link[i]}"]`).removeAttr("linker"));
                }
            }
            delete this.links[object];
        }
        for (let root of this.out_roots) {
            let html = $(this.html_roots['__main__']).find(`*[linker="${root.index}"]`).removeAttr("linker");
            root.linker.html_roots[root.for] = html;
            root.linker.link_roots[root.name] = html;
        }
        this.out_roots = [];
        this.html = Object.assign(this.html, this.link_roots);
        this.emitter.emit("linked");
    },
    uplink : function () {
        if (this.parent && this.parent.out_roots.length > 0)
            this.parent.uplink();
        for (let root of this.out_roots) {
            let html = $(this.html_roots['__main__']).find(`*[linker="${root.index}"]`).removeAttr("linker");
            root.linker.html_roots[root.for] = html;
            root.linker.link_roots[root.name] = html;
        }
        this.out_roots = [];
    },
    clear: function () {
        this.link_roots = {};
        this.html = {};
    },
    on : function (events, func) {
        this.emitter.on(events, func);
    }
};
//---------- LinkerError ----------//
function LinkerError(property) {
    Error.call(this, property);
    this.name = "LinkerError";
    this.message = property;
    this.stack = (new Error()).stack;
}

LinkerError.prototype = Object.create(Error.prototype);
LinkerError.prototype.constructor = LinkerError;

//---------- AjaxModule ----------//

function AjaxModule(config, parent) {
    let _this = this;
    this.name = config['name'];
    this.emitter = new Emitter("templated linked expanded ready", this);
    //Submodules control
    this.ready = false; //Means itself
    this.linked = false;
    this.submodule = config['submodule'] || false;
    if (this.submodule) {
        if (!(parent instanceof AjaxModule))
            throw new Error("parent is not instance of AjaxModule");
        else {
            this.parent = parent;
            this.parent.register_submodule(this);
            this.parent.on("linked", function () {
                _this.emitter.emit("templated");
            });
            this.parent.on("expanded", function () {
                if (!_this.linked)
                    _this.emitter.emit("templated");
            })
        }
    } else {
        this.node = config['node'];
    }
    //
    //Adding custom event
    if (config['customEvents'] !== undefined)
        this.emitter.register_events(config['customEvents']);
    this.loader = new TemplatesLoader(config['templates']);
    this.setter = new EventSetter(config['events'] || {}, this);
    this.linker = new Linker(this);

    this.tracker = [];
    this.html = {};

    this.on("templated", function () {
        this.linked = false;
        this.linker.link();
    });
    this.linker.on("linked", function () {
        _this.html = _this.linker.html;
        _this.linked = true;
        _this.emitter.emit("linked");
        if (_this.ready_check() && _this.tracker.length === 0) {
            _this.ready = true;
            _this.emitter.emit("ready");
        }
        this.clear();
    });
}
AjaxModule.prototype = {
    constructor: AjaxModule,
    template_object: function () {
        return {
            _l : this.linker
        }
    },
    template_data: function () {
        throw new Error("method template_data is not implemented for AjaxModule: " + this.name);
    },
    template: function (add=false) {
        if (this.loader.loaded) {
            this.ready = false;
            const template = this.loader.main.compilable(this.template_data());
            if (!add)
                $(this.node).html(template);
            else
                $(this.node).append(template);
            this.emitter.emit("templated");
        } else {
            const _this = this;
            this.loader.on("loaded", function () {
                _this.ready = false;
                const template = _this.loader.main.compilable(_this.template_data());
                if (!add)
                    $(_this.node).html(template);
                else
                    $(_this.node).append(template);
                _this.emitter.emit("templated");
            })
        }
    },
    retemplate: function (replace) {
        this.ready = false;
        let object = this.html[replace];
        let replacement = this.loader.main.compilable(this.template_data());
        $(object).replaceWith(replacement);
        this.emitter.emit("templated");
    },
    expand: function (...templates) {
        for (let template of templates) {
            this.ready = false;
            let object = this.html[template.node];
            let addition = TemplatesLoader.templates[template.template].compilable(template.data);
            $(object).append(addition);
        }
        this.emitter.emit("expanded");
    },
    get_node: function () {
        if (!this.submodule)
            return this.node;
        else
            return this.parent.get_node();
    },
    on : function (events, func) {
        this.emitter.on(events, func);
    },
    off : function (events, func) {
        this.emitter.off(events, func);
    },
    register_submodule : function (module) {
        const _this = this;
        if (!(module instanceof AjaxModule)) {
            throw new Error("submodule is not instance of AjaxModule");
        }
        this.tracker.push(module);
        module.on("ready", function () {
            if (_this.ready_check() && !_this.ready) {
                _this.ready = true;
                _this.emitter.emit("ready");
            }
        });
    },
    ready_check : function () {
        return this.linked && (this.tracker.length === 0 || this.tracker.every(m => m.ready));
    }
};

//---------- TemplatesLoader ----------//

function TemplatesLoader(templates) {
    const _this = this;
    this.loaded = false;
    this.emitter = new Emitter("loaded");
    this.templates = templates['templates'];
    for (let template of this.templates) {
            const t = template;
            if (!TemplatesLoader.templates[t.name]) {
                $.get(t.path, function (data) {
                    switch (t.type) {
                        case "template":
                            TemplatesLoader.templates[t.name] = {
                                compilable: Handlebars.compile(data),
                                type: t.type
                            };
                            break;
                        case "partial":
                            Handlebars.registerPartial(t.name, data);
                            TemplatesLoader.templates[t.name] = {
                                compilable: Handlebars.compile(data),
                                type: t.type
                            };
                            break;
                    }
                    if (templates['main'] === t.name) {
                        _this.main = TemplatesLoader.templates[t.name];
                    }
                    if (!_this.loaded && _this.check())
                        _this.emitter.emit("loaded");
                }, 'text');
            } else {
                if (templates['main'] === t.name) {
                    _this.main = TemplatesLoader.templates[t.name];
                }
                if (!_this.loaded && _this.check())
                    _this.emitter.emit("loaded");
            }
    }
}
TemplatesLoader.prototype = {
    constructor: TemplatesLoader,
    check: function () {
        if (!this.loaded) {
            for (let template of this.templates) {
                if (!TemplatesLoader.templates[template.name])
                    return false;
            }
            this.loaded = true;
        }
        return this.loaded;
    },
    on: function (events, func) {
        this.emitter.on(events, func);
    }
};
TemplatesLoader.templates = {};

//---------- EventSetter ----------//

function EventSetter(events, module) {
    if (!(module instanceof AjaxModule))
        throw new Error("module is not instance of AjaxModule");
    this.module = module;
    this.events = events;
}
EventSetter.prototype = {
    constructor: EventSetter,
    set: function (events) {
        const _this = this;
        const list = events.split(/\s+/);
        for (let i = 0; i < list.length; i++) {
            (function () {
                const event = _this.events[list[i]];
                let consumers = [];
                let objects = (event.type === "html") ? _this.module.html[event.object] : _this.module[event.object];
                if (Array.isArray(objects))
                    consumers.push(...objects);
                else
                    consumers = [objects];
                for (let object of consumers) {
                    const consumer = (event.type === "html") ? $(object) : object;
                    if (!consumer.on) {
                        console.warn("Trying to set listener on object without handler\nObject: " + consumer);
                    } else {
                        consumer.on(event.event, function () {
                            let args = Array.apply(null, arguments);
                            args = args.slice(1, arguments.length);
                            args.unshift(object);
                            event.handler.apply(_this.module, args);
                        });
                    }
                }
            })()
        }
    }
};

//---------- ResponseHandler ----------//

function Response(data, fields) {
    this.data = data;
    this.result = false;
    this.error_list = [];
    const _this = this;
    if (data['errors'] !== undefined) {
        data['errors'].forEach(function (element) {
            _this.error_list.push(element);
        })
    } else if (data['response'] !== undefined) {
        this.result = data['response'];
        fields.forEach(function (field) {
            const value = data[field];
            if (value !== undefined) {
                _this[field] = value;
            }
        });
    }
    return this;
}
