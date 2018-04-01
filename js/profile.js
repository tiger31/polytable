$(document).ready(function () {
    $(".module").each(function (index, element) {
        Module.list.push(new Module(element));
    });
    $(".accordion").each(function (index, element) {
        var estimatedList = $(element).next();
        if ($(estimatedList).hasClass("list")) {
            $(element).on("click", function () {
                $(estimatedList).slideToggle(300);
        })
        }
    });
    Module.active_chain("main");
});

function Module(element) {
    var _this = this;
    this.active = $(element).hasClass("active");
    this.group = $(element).attr("group");
    this.object = element;
    this.name = $(element).attr("module");
    this.triggers = $(".module_t").toArray().filter(function (element) {
        return $(element).attr("module") === _this.name;
    });
    this.triggers.forEach(function (element) {
        $(element).on("click", function () {
            _this.set_active();
        })
    });
    if (this.active) {
        _this.set_active();
    }
}
Module.prototype = {
    constructor: Module,
    set_active: function () {
        if (this.group !== undefined)
            Module.active_chain(this.group);
        else
            Module.set_active(this);
    },
    show: function () {
        $(this.object).show(0);
    },
    hide: function () {
        $(this.object).hide(0);
    }
};
Module.active = undefined;
Module.list = [];
Module.set_active = function (module){
    if (Module.active !== undefined) {
       Module.active.forEach(function (element) {
           element.hide();
       });
    }
    Module.active = [module];
    Module.active[0].show();
};
Module.active_chain = function (group) {
    if (Module.active !== undefined) {
        Module.active.forEach(function (element) {
            element.hide();
        });
    }
    var modules = Module.list.filter(function (element) {
        return element.group !== undefined && element.group === group;
    });
    Module.active = modules;
    modules.forEach(function (module) {
        module.show();
    });
};
function FieldsEdit(fields, trigger, button) {
    this.blocks = {};
    this.fields = {};
    this.trigger = trigger;
    this.state = false;
    this.button_classes = $(button).attr("class");
    for (var field in fields) {
        if (fields.hasOwnProperty(field)) {
            this.blocks[field] = $('.description#' +  field);
            var input = $(this.blocks[field]).find("input").removeClass("hidden");
            this.fields[field] = new Field(input, fields[field]);
            input.addClass("hidden");
        }
    }
    this.fallbackIcon = $(button).find("i");
    this.fallbackText = $(button).text();
    this.button = new AjaxButton(button, this.fields, {
        "url" : "action.php",
        "data_from_func" : function (elem) {
            var data = {};
            Object.keys(elem.fields).forEach(function (element) {
                data[element] = elem.fields[element].get_value();
            });
            data['action'] = "update";
            return data;
        }
    });
    this.button.disable();
    this.button.on("disable", function () {
        $(button).text("").append($(_this.fallbackIcon), _this.fallbackText);
    });
    this.button.on("activate", function () {
        $(button).text("").append($("<i class=\"ui icon save\"></i>"), "Сохранить");
    });
    this.button.on("success", function (result) {
        if (result['response'] !== undefined) {
            $(trigger).trigger("click");
        }
    });
    $(button).on("click", function () {
        if (_this.button.disabled) {
            $(trigger).trigger("click");
        }
    });
    var _this = this;
    $(trigger).on("click", function () {
        if (_this.state) {
            _this.restore();
            _this.button.disable();
            $(this).removeClass("remove").addClass("write");
        } else {
            _this.edit();
            _this.button.activate();
            $(this).removeClass("write").addClass("remove");
        }
    });
}
FieldsEdit.prototype = {
    constructor: FieldsEdit,
    edit: function () {
        this.state = true;
        var _this = this;
        $(this.button.button).removeClass("disabled");
        Object.keys(this.blocks).forEach(function (block) {
            var swap = $(_this.blocks[block]).find(".swap");

            $(_this.blocks[block]).find("span").hide(0);
            $(_this.blocks[block]).find("input").removeClass("hidden").val($(swap).text());
            _this.fields[block].on_change();
            if ($(_this.blocks[block]).hasClass("hidden")) $(_this.blocks[block]).removeClass("hidden").addClass("hide");
        });
    },
    restore: function () {
        this.state = false;
        var _this = this;
        $(this.button.button).attr("class", this.button_classes);
        Object.keys(this.blocks).forEach(function (block) {
            $(_this.blocks[block]).find("span").show(0);
            $(_this.blocks[block]).find("input").addClass("hidden");
            if ($(_this.blocks[block]).hasClass("hide")) $(_this.blocks[block]).removeClass("hide").addClass("hidden");
        });
    }
};
