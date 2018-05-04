function PasswordField(field, config) {
    Field.apply(this, [field, config]);
    this.ajax_check = false;
    this.empty_valid = false;
    this.valid_strength = config['valid_strength'] || 1;
    this.current_strength = 0;

    const _this = this;

    $(this.field).on("change insert keyup", function () {
        _this.on_change();
    });
}

PasswordField.regexes = [
    /^.*$/,
    /^(?![.\n])(([A-Za-z0-9]{0,7})|([a-z]+)|([0-9]+)|[A-Z]+)$/,
    /(?![.\n])(?=^.{8,}$)((?=.*[a-z])((?=.*[0-9])|(?=.*[A-Z]))|(?=.*[A-Z])((?=.*[0-9])))[A-Za-z0-9]*$/,
    /(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z])[A-Za-z0-9]*$/
];

PasswordField.prototype = Object.create(Field.prototype);
PasswordField.prototype.constructor = PasswordField;
PasswordField.prototype.on_change = function () {
    this.emitter.emit("changed");
    if (this.value_not_empty()) {
        for (let i = 0; i < PasswordField.regexes.length; i++) {
            const regex = PasswordField.regexes[i];
            if (regex.test(this.get_value())) {
                this.current_strength = i;
            }
        }
        if (this.current_strength < this.valid_strength)
            this.invalidate();
        else
            this.validate();
    } else {
        this.clear();
    }
    if (this.error !== undefined && this.log_level > 2) console.log(this.error);
    this.emitter.emit("validate");
};
PasswordField.prototype.set_confirm = function (field) {
    if (!(field instanceof Field))
        throw new TypeError("field is not instance of Field");
    field.regex_check = false;
    field.ajax_check = false;
    field.empty_valid = false;
    const _this = this;
    this.on("validate", function () {
        field.emitter.emit("validate");
    });
    field.on("validate", function () {
        if (this.get_value() === _this.get_value())
            this.validate();
        else {
            this.invalidate();
        }
        this.controller.on_valid();
    });
};


