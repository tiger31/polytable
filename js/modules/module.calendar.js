moment.locale("ru");
function Calendar(group_id) {
    AjaxModule.apply(this, [Calendar.config]);
    Calendar.group_id = group_id;

    this.group_id = group_id;

    Calendar.today = moment().startOf("day");

    this.shown_index = (this.get_type() === "month") ? moment(Calendar.today).startOf("month").format("YYYY-MM-DD") :
        moment(Calendar.today).startOf("week").format("YYYY-MM-DD");
    this.shown_data = [];

    this.months = {};
    this.weeks = {};
    this.raw_data = {};
    this.waiting = {};

    this.on("linked", function () {
        this.setter.set("next prev type");
        $(this.html['calendar_type']).prop('checked', (this.get_type() === "week"));
        (this.get_type() === "week") ? $(this.html['calendar']).addClass("weekly") : $(this.html['calendar']).removeClass("weekly");
    });

    const _this = this;

    function appear() {
        _this.show(_this.shown_index);
        Calendar.activeDay.toggle_lessons(true);
        _this.emitter.off("ready", appear);
    }

    this.on("ready", appear);
}
Calendar.config = {
    name : "calendar",
    customEvents: "ajax_sent ajax_loaded constructed",
    templates: {
        main : "calendar",
        templates: [
            {
                name : "calendar",
                path : "templates/calendar/calendar.handlebars",
                type : "template"
            },
            {
                name : "month",
                path : "templates/calendar/month.handlebars",
                type : "partial"
            },
            {
                "name" : "day",
                "path" : "templates/calendar/day.handlebars",
                "type" : "partial"
            },
            {
                "name" : "lesson",
                "path" : "templates/calendar/lesson.handlebars",
                "type" : "partial"
            },
            {
                "name" : "lesson_list",
                "path" : "templates/calendar/lessonList.handlebars",
                "type" : "partial"
            },
            {
                "name" : "homework",
                "path" : "templates/calendar/homework.handlebars",
                "type" : "partial"
            },
            {
                "name" : "homework_editor",
                "path" : "templates/controls/homework_editor.handlebars",
                "type" : "template"
            }
        ]
    },
    events : {
        next : {
            type : "html",
            object : "arrow_right",
            event : "click",
            handler : function () {
                this.show(moment(this.shown_index).add(1, (this.get_type() === "month") ? "months" : "weeks").format("YYYY-MM-DD"), true);
            }
        },
        prev : {
            type: "html",
            object : "arrow_left",
            event : "click",
            handler : function () {
                this.show(moment(this.shown_index).subtract(1, (this.get_type() === "month") ? "months" : "weeks").format("YYYY-MM-DD"), false);
            }
        },
        type : {
            type: "html",
            object: "calendar_type",
            event: "change",
            handler : function (object) {
                const type = (object[0].checked) ? "week" : "month";
                localStorage.setItem("calendar_type", type);
                this.shown_index = (this.get_type() === "month") ? moment(Calendar.today).startOf("month").format("YYYY-MM-DD") :
                    moment(Calendar.today).startOf("week").format("YYYY-MM-DD");
                console.log(this.shown_index);
                (type === "week") ? $(this.html['calendar']).addClass("weekly") : $(this.html['calendar']).removeClass("weekly");
                this.refresh_controls();
                this.show(this.shown_index);
            }
        }
    }
};
Calendar.prototype = Object.create(AjaxModule.prototype);
Calendar.prototype.constructor = Calendar;
Calendar.prototype.load = function () {
    this.emitter.emit("ajax_sent");
    let _this = this;
    $.getJSON({
        url: "action.php?action=calendar",
        data: {
            group: this.group_id
        },
        success: function (data) {
            if (data["response"] === true) {
                _this.data_loaded = true;
                _this.raw_data = data['data'];
                _this.emitter.emit("ajax_loaded");
                _this.construct(data["data"]);
            }
        }
    });
};
Calendar.prototype.template_waiting = function () {
    let list = [];
    for (let month of Object.values(this.waiting))
        for (let day of month.days)
            list.push(day.template_lessons_data());
    const obj = Object.assign(this.template_object(), {
        list : list,
        months : Object.values(this.waiting).map(month => month.template_data()),
    });
    this.waiting = {};
    return obj;
};

Calendar.prototype.construct = function(data) {
    if (data !== null && data !== undefined) {
        Calendar.static_start = moment(data['static_start']);
        Calendar.static_end = moment(data['static_end']);
        Calendar.timetable_start = moment(data['timetable_start']);
        Calendar.timetable_end = moment(data['timetable_end']);

        Calendar.static = data['static'];
        Calendar.dynamic = data['dynamic'];
        Calendar.homework = data['homework'];

        let today = moment();
        if (today >= Calendar.timetable_start && today <= Calendar.timetable_end) {
            const index = today.startOf("month").format("YYYY-MM-DD");
            this.months[index] = new Month(this, moment(today).startOf("month"));
            this.waiting[index] = this.months[index];
        }
    }
    this.emitter.emit("constructed");
};

Calendar.prototype.template_data = function () {
    return Object.assign(this.template_object(), this.template_waiting() ,{
        cache : "Последнее обновление информации: " + moment(this.raw_data['cache_last']).calendar(null, {
            lastDay : "[вчера в] LT",
            nextDay : "[завтра(как?) в] LT",
            sameDay : "[сегодня в] LT",
            lastWeek : function () {
                const weekday = this.weekday();
                const this_week = this.week() === Calendar.today.week();
                const sub = (this_week) ? "" : Day.prefix[0] + Day.suffix[weekday];
                if (this.diff(Calendar.today, 'days') === -2)
                    return "[позавчера в] LT";
                return `[в ${sub} ${Day.format[weekday]} в] LT`;
            },
            nextWeek : function () {
                const weekday = this.weekday();
                const this_week = this.week() === Calendar.today.week();
                const sub = (this_week) ? "" : Day.prefix[1] + Day.suffix[weekday];
                if (this.diff(Calendar.today, 'days') === 2)
                    return "[послезавтра(как?) в] LT";
                return `[в ${sub} ${Day.format[weekday]} в] LT`;
            },
            sameElse : function () {
                const weekday = this.weekday();
                const sub = (weekday === 1) ? "во" : "в";
                return `[${sub}] dddd D MMMM [в] LT`;
            }

        })
    });
};
Calendar.prototype.show = function (index, status) {
    const type = this.get_type();
    let ready = false;
    function late_show() {
        this.show(index, status);
        this.emitter.off("ready", late_show);
    }

    if (type === "month") {
        if (this.months[index] === undefined) {
            let time = moment(index).startOf("day");
            let bound = (status) ? time.startOf("month") : time.endOf("month");
            if (Calendar.timetable_start <= bound && Calendar.timetable_end >= bound) {
                this.months[index] = new Month(this, moment(bound));
                this.waiting[index] = this.months[index];
                this.on("ready", late_show);
                const data = this.template_waiting();
                this.expand(
                    { node : "lessons", template : "lesson_list", data : data},
                    { node : "calendar", template : "month", data : data}
                );
            }
        } else ready = true;
    } else if (type === "week") {
        if (this.weeks[index] === undefined || this.weeks[index].length < 7) {
            let time = moment(index).startOf("day");
            let bound = (status) ? time.endOf("week") : time.startOf("week");
            if (Calendar.timetable_start <= bound && Calendar.timetable_end >= bound) {
                this.months[index] = new Month(this, moment(bound));
                this.waiting[index] = this.months[index];
                this.on("ready", late_show);
                const data = this.template_waiting();
                this.expand(
                    { node : "lessons", template : "lesson_list", data : data},
                    { node : "calendar", template : "month", data : data}
                );
            }
        } else ready = true;
    }
    if (ready) {
        if (this.visible) {
            $(this.shown_data).each(function () {
                $(this).hide(0);
            });
        }
        //show part
        this.shown_index = index;
        let elements = [];



        const days = (type === "month") ? this.months[this.shown_index].days : this.weeks[this.shown_index];
        $(this.html['current_month']).text(Month.names[days[days.length - 1].date.month()]);
        if (type === "month")
            elements.push(...this.months[this.shown_index].html["empty"] || []);
        else if (index === Object.keys(this.weeks)[0]) {
            const month = this.weeks[index][0].date.format("YYYY-MM-DD");
            elements.push(...this.months[month].html["empty"] || []);
        }
        for (let day of days)
            elements.push(day.html['day']);
        $(elements).each(function () {
            const day = this;
            $(day).show(0);
        });
        this.shown_data = elements;
        this.refresh_controls();
        this.visible = true;
    }
};
Calendar.prototype.refresh_controls = function () {
    const left_bound = moment(this.shown_index).startOf((this.get_type() === "month") ? "month" : "week").startOf("day");
    const right_bound = moment(this.shown_index).endOf((this.get_type() === "month") ? "month" : "week").startOf("day");
    const min = Calendar.timetable_start;
    const max = Calendar.timetable_end;
    $(this.html["arrow_right"]).css("display", ((right_bound >= max) ? "none" : "block"));
    $(this.html["arrow_left"]).css("display", ((left_bound <= min) ? "none" : "block"));
};
Calendar.prototype.get_type = function () {
    let calendar_type = localStorage.getItem("calendar_type") || "month";
    if (calendar_type !== "month" && calendar_type !== "week")
        calendar_type = "month";
    return calendar_type;
};

//---------- Submodule Month ----------//

function Month(parent, date_start) {
    AjaxModule.apply(this, [Month.config, parent]);
    this.start = moment(date_start);
    this.end = moment(date_start).endOf("month");
    this.shift = date_start.weekday();
    this.days = [];
    for (let date = moment(this.start); date <= this.end; date = moment(date).add(1, 'days')) {
        const day = new Day(this, moment(date));
        this.days.push(day);
        const week = moment(day.date).startOf("week").format("YYYY-MM-DD");
        if (!this.parent.weeks[week])
            this.parent.weeks[week] = [];
        this.parent.weeks[week].push(day);
    }
}
Month.config = {
    name : "month",
    submodule : true,
    templates : {
        main : "month",
        templates : [
            {
                "name" : "day",
                "path" : "templates/calendar/day.handlebars",
                "type" : "partial"
            },
        ]
    }
};

Month.names = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"];
Month.prototype = Object.create(AjaxModule.prototype);
Month.prototype.constructor = Month;
Month.prototype.template_data = function () {
    return Object.assign(this.template_object(), {
        empty : this.shift,
        days : this.days.map(day => day.template_data())
    });
};

//---------- Submodule Day ----------//

function Day(parent, date) {
    AjaxModule.apply(this, [Day.config, parent]);
    this.date = date;
    this.date_key = date.format("YYYY-MM-DD");
    this.day = date.date();
    this.is_odd = ((date.week() - Calendar.static_start.week()) % 2 === 0) ? 1 : 0;
    this.weekday = date.isoWeekday();
    this.has_cache = false;
    this.lessons = [];
    if ((Calendar.static[this.is_odd] !== undefined && Calendar.static[this.is_odd][this.weekday] !== undefined) ||
            Calendar.dynamic[this.date_key] !== undefined) {
        let lessons = {};
        if (this.date >= Calendar.static_start && this.date <= Calendar.static_end)
            lessons = Object.assign({}, Calendar.static[this.is_odd][this.weekday]) || {};
        if (Calendar.dynamic[this.date_key] !== undefined)
            for (let key of Object.keys(Calendar.dynamic[this.date_key])) {
                if (Calendar.dynamic[this.date_key][key]['action'] === "ERASE")
                    delete lessons[key];
                else
                    lessons[key] = Calendar.dynamic[this.date_key][key];
            }
        const lesson_keys = Object.keys(lessons);
        if (Calendar.homework[this.date_key] !== undefined)
            for (let key of lesson_keys)
                if (Calendar.homework[this.date_key][key] !== undefined) {
                    lessons[key]['text'] = Calendar.homework[this.date_key][key]['text'];
                    lessons[key]['files'] = Calendar.homework[this.date_key][key]['files'];
                }
        if (lesson_keys.length > 0) {
            this.has_cache = true;
            this.time_start = lessons[lesson_keys[0]]["time_start"];
            this.time_end = lessons[lesson_keys[lesson_keys.length - 1]]["time_end"];
        }
        for (let key of lesson_keys)
            this.lessons.push(new Lesson(lessons[key], this));

    }
    if (this.date_key === moment().format("YYYY-MM-DD"))
        Calendar.activeDay = this;
    this.on("ready", function () {
        this.setter.set("show_lessons scroll_lessons");
    });
}
Day.config = {
    name : "day",
    submodule : true,
    templates : {
        main : "day",
        templates : [
            {
                "name" : "day",
                "path" : "templates/calendar/day.handlebars",
                "type" : "partial"
            },
            {
                "name" : "lesson",
                "path" : "templates/calendar/lesson.handlebars",
                "type" : "partial"
            },
            {
                "name" : "lesson_list",
                "path" : "templates/calendar/lessonList.handlebars",
                "type" : "partial"
            },
            {
                "name" : "homework",
                "path" : "templates/calendar/homework.handlebars",
                "type" : "partial"
            },
            {
                "name" : "homework_editor",
                "path" : "templates/controls/homework_editor.handlebars",
                "type" : "template"
            }
        ]
    },
    events : {
        show_lessons : {
            type : "html",
            object : "day",
            event : "click",
            handler : function () {
                this.show();
            }
        },
        scroll_lessons : {
            type : "html",
            object : "day",
            event : "dblclick",
            handler : function () {
                $('html, body').animate({
                    scrollTop: ($(this.html['lcontainer']).offset().top - 50)
                },300);
            }
        }
    }
};
Day.short = ["ПН", "ВТ", "СР", "ЧТ", "ПТ", "СБ", "ВС"];
Day.format = ["понедельник", "вторник", "среду", "четверг", "пятницу", "субботу", "воскресенье"];
Day.prefix = ["предыду", "следую"];
Day.suffix = ["щий", "щий", "щую", "щий", "щую", "щую", "щее"];

Day.prototype = Object.create(AjaxModule.prototype);
Day.prototype.constructor = Day;
Day.prototype.get_class_list = function () {
    let class_list = [];
    let today = moment().startOf('day');
    if (today.format("YYYY-MM-DD") === this.date_key)
        class_list.push("current");
    if (this.date < today)
        class_list.push("past");
    if (this.weekday === 7)
        class_list.push("weekend");
    if (!this.has_cache)
        class_list.push("uncached");
    return class_list.join(" ");
};
Day.prototype.template_data = function () {
    return Object.assign(this.template_object(), {
        classList : this.get_class_list(),
        number : this.date.format("DD"),
        weekDay : Day.short[this.weekday - 1],
        cached : this.has_cache,
        time : this.time_start + "-" + this.time_end
    });
};
Day.prototype.template_lessons_data = function () {
    return Object.assign(this.template_object(), {
        class : "",
        title : this.date.calendar(null, {
            lastDay : "[Вчера пар: ]",
            nextDay : "[Завтра пар: ]",
            sameDay : "[Сегодня пар: ]",
            lastWeek : function () {
                const weekday = this.weekday();
                const this_week = this.week() === Calendar.today.week();
                const sub = (this_week) ? "" : Day.prefix[0] + Day.suffix[weekday];
                if (this.diff(Calendar.today, 'days') === -2)
                    return "[Позавчера пар: ]";
                return `[В ${sub} ${Day.format[weekday]} пар: ]`;
            },
            nextWeek : function () {
                const weekday = this.weekday();
                const this_week = this.week() === Calendar.today.week();
                const sub = (this_week) ? "" : Day.prefix[1] + Day.suffix[weekday];
                if (this.diff(Calendar.today, 'days') === 2)
                    return "[Послезавтра пар: ]";
                return `[В ${sub} ${Day.format[weekday]} пар: ]`;
            },
            sameElse : function () {
                const weekday = this.weekday();
                const sub = (weekday === 1) ? "Во" : "В";
                return `[${sub}] dddd D MMMM [пар: ]`;
            }

        }) + this.lessons.length,
        cache : this.has_cache,
        lessons : this.lessons.map(l => l.template_data())
    });
};
Day.prototype.show = function () {
    if (Calendar.activeDay === this) return;
    if (Calendar.activeDay !== undefined)
        Calendar.activeDay.toggle_lessons(false);
    Calendar.activeDay = this;
    this.toggle_lessons(true);
};
Day.prototype.hide = function () {
    if (Calendar.activeDay !== this) return;
    this.toggle_lessons(false);
};
Day.prototype.toggle_lessons = function (show) {
    if (show) {
        let timeout = 0;
        $(this.html["lcontainer"]).addClass("active");
        $(this.html["day"]).addClass("active");
        for (let i of this.lessons) {
            i.show(450 + timeout);
            timeout += 250;
        }
    } else {
        $(this.html["lcontainer"]).removeClass("active");
        $(this.html["day"]).removeClass("active");
        for (let i of this.lessons) {
            i.hide(0);
        }
    }
};

//---------- Submodule Lesson ----------//

function Lesson(data, parent) {
    AjaxModule.apply(this, [Lesson.config, parent]);
    this.date_key = data['day'] || parent.date_key;
    this.db_key = data['lesson'];
    this.subject = data['subject'];
    this.type = data['type'];
    this.time_start = data['time_start'];
    this.time_end = data['time_end'];
    this.teachers = data['teachers'];
    this.places = data['places'];
    this.homework = new Homework(data['text'], data['files'], this);
    this.editor = undefined;

    let _this = this;

    this.on("ready", function () {
        this.setter.set("homework_edit");
        if (this.homework.exists)
            this.setter.set("homework_show");
    });
    //Editor
    if (Calendar.editor) {
        this.editor = new Editor(this, {
            template: TemplatesLoader.templates['homework_editor'].compilable,
            template_created: function (editor) {
                //Setting up close event on close button
                $(editor.element).find(".editor_title_close").on("click", function () {
                    editor.window.hide();
                });
                //Setting up new variables
                editor.ajax_field = new Field($(editor.element).find("textarea"),
                    {
                        'regex_check' : false,
                        'ajax_ignore' : true,
                        'show_errors' : false,
                        'empty_valid' : true
                    });
                editor.ajax_field.on("changed", function (value) {
                    let length = value.length;
                    let counter = $(editor.element).find(".editor_counter").text(length + "/140");
                    if (length > 140)
                        $(counter).addClass("invalid");
                    else
                        $(counter).removeClass("invalid");
                });
                editor.ajax_button = new AjaxButton($(editor.element).find(".editor_submit"), {'text' : editor.ajax_field},
                    {
                        'url' : 'action.php',
                        'method' : "POST",
                        "data" : function (elem) {
                            return {
                                'action' : 'homework',
                                'group' : Calendar.group_id,
                                'date' : elem.controller.object.date_key, // AjaxButton.Editor.Lesson.date_key
                                'lesson' : elem.controller.object.db_key,
                                'text' : JSON.stringify({'text' : elem.fields['text'].get_value(), 'files' : get_values(editor.file_list)})
                            }
                        }
                    }
                    , editor);
                editor.ajax_button.on("sent", function () {
                    this.disable();
                    $(this.button).html('<i class="ui icon star loading" style="font-size: 19px; margin: 0"></i>');
                });
                editor.ajax_button.on("success", function (result) {
                    if (result['response'] !== undefined && result['response'] !== null) {
                        if (result['response'] === true)
                            editor.emitter.emit("accepted", result);
                        else
                            editor.emitter.emit("rejected", result);
                    } else {
                        editor.emitter.emit("rejected", result);
                    }
                });
                editor.ajax_button.on("error", function (result) {
                    editor.emitter.emit("rejected", result);
                });
                editor.ajax_loader = $(editor.element).find("#imageLoader").dropzone(imageAjaxConfig).get(0)['dropzone'];
                editor.file_list = {};
                //Setting up custom events for fields
                editor.ajax_loader.on("success", function (file, result) {
                    let json = result;
                    if (json['error'] === undefined) {
                        editor.file_list[file.name] = json['filename'];
                    } else {
                        editor.ajax_loader.removeFile(file);
                    }
                });
                editor.ajax_loader.on("removedfile", function (file) {
                    delete editor.file_list[file.name];
                });
                editor.ajax_loader.on("maxfilesexceeded", function (file) {
                    editor.ajax_loader.removeFile(file);
                });
                editor.ajax_loader.on("addedfile", function (file) {
                    if (!file.type.match(/image.*/))
                        this.emit("thumbnail", file, "../../assets/images/file.png");
                });
                editor.ajax_loader.on("sending", function (file, xhr, formData) {
                    formData.append("action", "upload");
                });
                //Preload data if exists
                if (editor.object.homework.exists) {
                    $(editor.element).find("textarea").text(editor.object.homework.text);
                    editor.object.homework.files.forEach(function (element) {
                        let thumb = (element['showable']) ? "../uploads/thumbnails/" + element['name'] :
                            "../../assets/images/file.png";
                        let file = {
                            name: element['original'],
                            type: (element['showable']) ? "image/jpeg" : "text/plain",
                            size: element['size']
                        };
                        editor.ajax_loader.emit("addedfile", file);
                        editor.ajax_loader.emit("thumbnail", file, thumb);
                        editor.ajax_loader.emit("complete", file);
                        editor.ajax_loader.emit("success", file, { response : true, filename: element['name']});
                        editor.ajax_loader.files.push(file);

                    });
                }
                //Client-server dialog
                editor.on("accepted", function (result) {
                    let icon = $('<i class="ui icon check" style="font-size: 19px; margin: 0; display: none"></i>');
                    $(editor.ajax_button.button).html($(icon).fadeIn(500, function() {
                        editor.window.hide();
                        editor.ajax_button.activate();
                        $(editor.ajax_button.button).html("Сохранить");
                    }));
                    editor.object.tracker = [];
                    editor.object.homework = new Homework(result['text'], result['files'], editor.object);
                    function refresh() {
                        this.show(0);
                        $(this.homework.html["homework"]).toggleClass("active").slideToggle(0);
                        $(this.html["homework_button"]).text("Скрыть ДЗ");
                        this.emitter.off("ready", refresh)
                    }
                    editor.object.on("ready", refresh);
                    editor.object.retemplate("lesson");
                });
                editor.on("rejected", function() {
                    let target = $(editor.ajax_button.button);
                    let icon = $('<i class="ui icon close" style="font-size: 19px; margin: 0; display: none; color: white"></i>');
                    $(target).html($(icon).fadeIn(500)).addClass("error");
                    setTimeout(function () {
                        $(target).html("Сохранить").removeClass("error");
                        editor.ajax_button.activate();
                    }, 1000)
                });
            },
            get_data: function () {
                return {
                    subject: _this.subject
                }
            }
        });
    }

}
Lesson.config = {
    name : "lesson",
    submodule : true,
    templates : {
        main : "lesson",
        templates : [
            {
                "name" : "lesson",
                "path" : "templates/calendar/lesson.handlebars",
                "type" : "partial"
            },
            {
                "name" : "homework",
                "path" : "templates/calendar/homework.handlebars",
                "type" : "partial"
            },
            {
                "name" : "homework_editor",
                "path" : "templates/controls/homework_editor.handlebars",
                "type" : "template"
            }
        ]
    },
    events : {
        homework_show : {
            type : "html",
            object : "homework_button",
            event : "click",
            handler : function () {
                const button = $(this.html["homework_button"]);
                $(this.homework.html["homework"]).toggleClass("active").slideToggle(300);
                $(button).text($(button).text() === "Показать ДЗ" ? "Скрыть ДЗ" : "Показать ДЗ");
            }
        },
        homework_edit : {
            type : "html",
            object : "edit_button",
            event : "click",
            handler : function () {
                let stored = ModalWindow.findStored(Editor.find, this.editor);
                if (stored !== undefined) {
                    ModalWindow.setActive(stored);
                    ModalWindow.activeWindow.show();
                } else {
                    this.editor.template();
                    this.editor.show();
                }
            }
        }
    }
};

Lesson.prototype = Object.create(AjaxModule.prototype);
Lesson.prototype.constructor = Lesson;
Lesson.prototype.get_color = function () {
    switch (this.type) {
        case "Практика":
            return "blue";
        case "Лабораторные":
            return "dark_blue";
        case "Курсовой проект":
            return "purple";
        case "Консультация":
        case "Консультации":
            return "orange";
        case "Экзамен":
        case "Доп. экзамен":
            return "red";
        case "Лекции":
        default:
            return "green";
    }
};
Lesson.prototype.template_data = function () {
    return Object.assign(this.template_object(), {
        subject : this.subject,
        color : this.get_color(),
        type : this.type,
        time : this.time_start + "-" + this.time_end,
        teachers : {
            exists : this.teachers.length > 0,
            text: (this.teachers.length > 1) ? "Ведут: " : "Ведет: ",
            teacher : this.teachers.map(teacher => teacher.name)
        },
        places : {
            exists : this.places.length > 0,
            place : this.places.map(place => place.name)
        },
        homework : this.homework.template_data(),
        editor: Calendar.editor
    });
};
Lesson.prototype.show = function (time) {
    $(this.html["lesson"]).fadeIn(time);
};
Lesson.prototype.hide = function (time) {
    $(this.html["lesson"]).fadeOut(time);
};

//---------- Submodule Homework ----------//

function Homework(text, files, parent) {
    AjaxModule.apply(this, [Homework.config, parent]);
    this.exists = false;
    if (text !== undefined || files !== undefined) {
        this.exists = true;
        this.text = text || "";
        this.files = files || [];
        for (let i = 0; i < this.files.length; i++) {
            this.files[i]['showable'] = this.files[i]['showable'] === "1";
            this.files[i]['_l'] = this.linker;
        }
    }
    this.on("ready", function () {
        if (this.exists)
            this.setter.set("image_view");
    })
}
Homework.config = {
    name : "homework",
    submodule : true,
    templates : {
        main: "homework",
        templates: [
            {
                "name": "homework",
                "path": "templates/calendar/homework.handlebars",
                "type": "partial"
            },
            {
                "name" : "homework_editor",
                "path" : "templates/controls/homework_editor.handlebars",
                "type" : "template"
            }
        ]
    },
    events : {
        image_view : {
            type : "html",
            object : "image",
            event : "click",
            handler : function (image) {
                console.log(image);
                let imageArr = $(image).parent().find(".view");
                let imageUrls = [];
                $(imageArr).each(function () {
                    imageUrls.push($(this).attr("src").replace("uploads/thumbnails/", "uploads/images/"));
                });
                let index = $(imageArr).index(image);
                let viewer = new ImageViewer(imageUrls, index);
                viewer.show(index);
            }
        }
    }
};

Homework.prototype = Object.create(AjaxModule.prototype);
Homework.prototype.constructor = Homework;
Homework.prototype.template_data = function () {
    return Object.assign(this.template_object(), {
        exists: this.exists,
        text: this.text,
        files: this.files
    });
};
imageAjaxConfig =  {
    url: "action.php",
    paramName: "image",
    maxFilesize: 100,
    thumbnailHeight: 50,
    thumbnailWidth: 50,
    maxFiles: 5,
    addRemoveLinks: true,
    renameFile: Date.now(),
    dictDefaultMessage: "Нажмите или перетащите файлы сюда",
    dictFileTooBig: "Размер файла не должен превышать 100Мб",
    dictCancelUpload: "",
    dictCancelUploadConfirmation: "Вы уверены, что хотите отменить загрузку?",
    dictRemoveFile: "",
    dictMaxFilesExceeded: "Нельзя прикреплять больше пяти фалов",
    headers: {
        'x-csrf-token': function () {
            const cookie = document.cookie.match(/X-CSRF-TOKEN=([\w]+)/);
            return (cookie) ? cookie[1] : null;
        }
    }
};