function group(data) {
    AjaxModule.apply(this, [group.config])
    this.data = data;
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
    let data = [];
    for (let obj of this.data)
        data.push(Object.assign(this.template_object(), obj));
    return {data: data};
};