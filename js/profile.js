$(document).ready(function () {
    $.getJSON({
        url : "action.php",
        data : {
            action : "profile",
        },
        success : function (response) {
            if (response['response']) {
                for (let module of Object.keys(response['data'])) {
                    $.getScript(`js/modules/module.${module}.js`, function () {
                        const m = eval(`new ${module}(response['data'][module])`);
                        m.node = $(eval(`${module}.config.load_node`));
                        m.template(true);
                    })
                }
            }
        }
    });
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

