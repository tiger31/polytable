function group() {
    AjaxModule.apply(this, [group.config])
}

group.config = {
    name : "group",
    load_node : "#modules",
    templates : {
        main : "group",
        templates : [
            {
                name : "group",
                path : "templates/profile/group.handlebars",
                type : "template"
            }
        ]
    },
    events : {

    }
};

group.prototype = Object.create(AjaxModule.prototype);
group.prototype.constructor = group;
group.prototype.template_data = function () {
    return Object.assign(this.template_object(), {});
};