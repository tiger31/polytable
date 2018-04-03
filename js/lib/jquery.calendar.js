moment.locale("ru");
function Calendar(group, element) {
    var _this = this;
    this.element = element;
    this.emitter = new Emitter("loaded constructed templated");
    this.group_id = group;
    this.today = moment().startOf('day');
    this.templated = false;
    this.visible = false;
    this.month = this.today.month();
    this.data_loaded = false;
    this.months = {};
    this.raw_data = {};
    this.matrix = [];
    this.controls = {
        next : undefined,
        prev : undefined
    };
    this.templates = {
        "day" : {
            "path" : "templates/calendar/day.handlebars",
            "type" : "template"
        },
        "lesson" : {
            "path" : "templates/calendar/lesson.handlebars",
            "name" : "lesson",
            "type" : "partial"
        },
        "lessonList" : {
            "path" : "templates/calendar/lessonList.handlebars",
            "type" : "template"
        },
        "homework" : {
            "path" : "templates/calendar/homework.handlebars",
            "name" : "homework",
            "type" : "partial"
        },
        "homework_editor" : {
            "path" : "templates/controls/homework_editor.handlebars",
            "type" : "template"
        }
    };
    for(var template in this.templates) {
        (function() {
            const t = template;
            $.get(_this.templates[t]["path"], function (data) {
                switch (_this.templates[t]["type"]) {
                    case "template":
                        Calendar.templates[t] = Handlebars.compile(data);
                        break;
                    case "partial":
                        Calendar.templates[t] = Handlebars.registerPartial(_this.templates[t]["name"], data);
                        break;
                }
            }, 'text');
        })()
    }
}
Calendar.prototype = {
    constructor: Calendar,
    load: function () {
        var _this = this;
        $.getJSON({
            url: "action.php?action=calendar",
            data: {
                group: this.group_id
            },
            success: function (data) {
                if (data["response"] === true) {
                    _this.data_loaded = true;
                    _this.raw_data = data['data'];
                    _this.emitter.emit("loaded");
                    _this.construct(data["data"]);
                }
            }
        });
    },
    load_controls: function (obj) {
        var _this = this;
        this.controls.next = obj.next;
        $(this.controls.next).on("click", function () {
            var keys = Object.keys(_this.months);
            var index = keys.indexOf(_this.month.toString());
            if (index !== -1 && index !== keys.length - 1)
                _this.show(keys[index + 1]);
        });
        this.controls.prev = obj.prev;
        $(this.controls.prev).on("click", function () {
            var keys = Object.keys(_this.months);
            var index = keys.indexOf(_this.month.toString());
            if (index !== -1 && index !== 0)
                _this.show(keys[index - 1]);
        });
    },
    refresh_controls: function () {
        var keys = Object.keys(this.months).map(Number);
        var month = parseInt(this.month);
        var min = Math.min(...keys);
        var max = Math.max(...keys);
        $(this.controls.next).css("display", ((month === max) ? "none" : "block"));
        $(this.controls.prev).css("display", ((month === min) ? "none" : "block"));
    },
    /***
     * date_from - день, с которого начинается кэш
     * date_to - день, на котором кэш заканчивается
     * date_start - день, с которого начинается формирование календаря.
     * date_end - день, на котором заканчивается формирование календаря.
     * Внимание! Месяцы в датах date_start и date_end могут отличаться
     */
    construct: function (data) {
        var date_start, date_end;
        if (data !== null && data !== undefined) {
            var cache_dates = Object.keys(data.days);
            var date_from = new Date(cache_dates[0]);
            var date_to = new Date(cache_dates[cache_dates.length - 1]);
            date_start = moment(date_from).startOf("month");
            date_end = moment(date_to).endOf("month");
        } else {
            date_start = moment().startOf("month");
            date_end = moment.endOf("month");
        }
        //clearing current data stored
        this.months = {};
        for (var date = moment(date_start); date <= date_end; date = moment(date).add(1, 'days')) {
            var month = date.month();
            if (this.months[month] === undefined || this.months === null)
                this.months[month] = new Month(date);
            this.months[month].days.push(new Day(date, data.days[date.format("YYYY-MM-DD")]));
        }
        this.emitter.emit("constructed");
    },
    template: function (element, element2) {
        var _this = this;
        //Создание матрицы элементов для плавной анимации
        var max = Math.max(...Object.values(this.months).map(m => m.days.length + m.empty));
        var rows = Math.ceil(max / 7);
        for (var r = 0; r < rows; r++) {
            this.matrix[r] = [];
            for (var c = 0; c < 7; c++) {
                this.matrix[r].push($('<div class="matrix"></div>'));
                $(element).append(this.matrix[r][c]);

            }
        }
        //Шаблонизация дней
        var keys = Object.keys(this.months);
        for (var m = 0; m < keys.length; m++) {
            var month = this.months[keys[m]];
            var shift = month.empty;
            for (var i = 0; i < shift; i++) {
                var empty = $('<div class="day empty"></div>');
                month.elements.push(empty);
                $(this.matrix[Math.floor(i / 7)][i % 7]).append(empty);
            }
            for (i; i < month.days.length + shift; i++) {
                var day = month.days[i - shift];
                var day_template = day.template();
                month.elements.push(day_template);
                $(this.matrix[Math.floor(i / 7)][i % 7]).append(day_template);
                //lessons
                if (month.days[i - shift].has_cache) {
                    var lessons_template = day.template_lessons();
                    $(element2).append(lessons_template);
                    var j = 0;
                    $(lessons_template).find(".lesson").each(function () {
                        var lesson = day.lessons[j];
                        lesson.element = this;
                        lesson.emitter.emit("templated");
                        j++;
                    })
                }
            }
        }
        $("#cached_last")
            .text((_this.raw_data['cache_last'] ? "Последнее обновление информации " + moment(_this.raw_data['cache_last']).format("DD.MM.YYYY HH:mm") :
                "Данных о кэше нет"));
        this.templated = true;
        this.emitter.emit("templated");
    },
    show: function (month) {
        var timeout = 50;
        if (this.visible) {
            //hide part
            $(this.months[this.month].elements).each(function () {
                var day = this;
                setTimeout(function () {
                    $(day).fadeOut(300);
                }, timeout);
                timeout += 10;
            });
            //this.months[this.month].elements.reverse()
        }
        //show part
        this.month = month;
        $(this.months[month].elements).each(function () {
            var day = this;
            setTimeout(function () {
                $(day).fadeIn(300);
            }, timeout);
            timeout += 10;
        });
        this.refresh_controls();
        this.visible = true;
    },
    on: function (event, func) {
        this.emitter.on(event, func);
    }

};
Calendar.dayMS = 86400000;
Calendar.templates = {
    //TODO partial
    "log" : Handlebars.registerHelper("log", function (data) {
        console.log(data);
    })
};
Calendar.activeDay = undefined;

function Month(month) {
    this.month = month;
    this.days = [];
    this.empty = month.startOf('month').weekday();
    this.elements = [];
    return this;
}

function Day(date, data) {
    this.emitter = new Emitter("templated");
    this.date = date;
    this.date_key = date.format("YYYY-MM-DD");
    this.day = date.date();
    this.weekday = date.weekday();
    this.has_cache = false;
    this.lessons = [];
    this.element = undefined;
    this.element_list = undefined;
    if (data !== null && data !== undefined) {
        var lesson_keys = Object.keys(data);
        this.has_cache = true;
        this.time_start = data[lesson_keys[0]]["time_start"];
        this.time_end = data[lesson_keys[lesson_keys.length - 1]]["time_end"];
        for (var i = 0; i < data.length; i++)
            this.lessons.push(new Lesson(data[i]));
    }
    var _this = this;
    this.on("templated", function () {
        $(_this.element).on("click", function () {
            if (_this.has_cache)
                _this.show();
        });
    })
}
Day.short = ["ПН", "ВТ", "СР", "ЧТ", "ПТ", "СБ", "ВС"];
Day.prototype = {
    constructor: Day,
    get_class_list: function () {
        var class_list = [];
        var today = moment().startOf('day');
        if (today.format("YYYY-MM-DD") === this.date_key) {
            class_list.push("current");
            if (this.weekday !== 6)
                class_list.push("active");
        }
        if (this.date < today)
            class_list.push("past");
        if (this.weekday === 6)
            class_list.push("weekend");
        if (!this.has_cache)
            class_list.push("uncached");
        return class_list.join(" ");
    },
    get_prefix: function () {

    },
    get_day_message: function () {
        var prefix = this.get_prefix();
        if (!this.has_cache)
            return "На " + prefix + " данных нет, удачи :)";
        if (this.lessons.length === 0)
            return prefix + " пар нет";
        return prefix + " пар: " + this.lessons.length;
    },
    template: function() {
        var args = {
            classList : this.get_class_list(),
            number : this.date.format("DD"),
            weekDay : Day.short[this.weekday],
            cached : this.has_cache,
            time : this.time_start + "-" + this.time_end
        };
        this.element = $(Calendar.templates['day'](args));
        this.emitter.emit("templated");
        return this.element;
    },
    template_lessons: function () {
        var class_list = [];
        if (moment().format("YYYY-MM-DD") === this.date_key && this.has_cache) {
            Calendar.activeDay = this;
        }
        var args = {
            class : class_list.join(" "),
            title : this.date_key,
            lessons : this.lessons.map(l => l.template_data())
        }
        this.element_list = $(Calendar.templates["lessonList"](args));
        return this.element_list;
    },
    show: function () {
        if (Calendar.activeDay === this) return;
        if (Calendar.activeDay !== undefined)
            Calendar.activeDay.toggle_lessons(false);
        Calendar.activeDay = this;
        this.toggle_lessons(true);
    },
    hide: function () {
        if (Calendar.activeDay !== this) return;
        this.toggle_lessons(false);
    },
    toggle_lessons: function (show) {
        if (show) {
            var timeout = 0;
            $(this.element_list).addClass("active");
            $(this.element).addClass("active");
            for (var i = 0; i < this.lessons.length; i++) {
                this.lessons[i].show(450 + timeout);
                timeout += 250;
            }
        } else {
            $(this.element_list).removeClass("active");
            $(this.element).removeClass("active");
            for (var i = 0; i < this.lessons.length; i++) {
                this.lessons[i].hide(0);
            }
        }
    },
    on: function (event, func) {
        this.emitter.on(event, func);
    }
};

function Lesson(data) {
    this.emitter = new Emitter("templated");
    this.date_key = data['day'];
    this.db_key = data['lesson'];
    this.subject = data['subject'];
    this.type = data['type'];
    this.time_start = data['time_start'];
    this.time_end = data['time_end'];
    this.teachers = data['teachers'];
    this.places = data['places'];
    this.element = undefined;
    this.homework = new Homework(data['text'], data['files']);
    this.editor = undefined;
    var _this = this;
    //Editor
    if (Calendar.editor) {
        this.editor = new Editor(this, {
            template: Calendar.templates["homework_editor"],
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
                    var length = value.length;
                    var counter = $(editor.element).find(".editor_counter").text(length + "/140");
                    console.log(length);
                    if (length > 140)
                        $(counter).addClass("invalid");
                    else
                        $(counter).removeClass("invalid");
                });
                editor.ajax_button = new AjaxButton($(editor.element).find(".editor_submit"), {'text' : editor.ajax_field},
                    {
                        'url' : 'action.php',
                        'data_from_func' : function (elem) {
                            return {
                                'action' : 'send',
                                'date' : elem.controller.object.date_key, // AjaxButton.Editor.Lesson.date_key
                                'lesson' : elem.controller.object.db_key,
                                'text' : JSON.stringify({'text' : elem.fields['text'].get_value(), 'files' : get_values(editor.file_list)})
                            }
                        }
                    }
                    , editor);
                editor.ajax_button.on("sent", function () {
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
                editor.ajax_loader = $(editor.element).find("#imageLoader").dropzone(imageAjaxConfig).get(0)['dropzone'];
                editor.file_list = {};
                //Setting up custom events for fields
                editor.ajax_loader.on("success", function (file, result) {
                    var json = result;
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
                        var thumb = (element['showable']) ? "../uploads/thumbnails/" + element['name'] :
                            "../../assets/images/file.png";
                        var file = {
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
                    editor.ajax_loader.options.maxFiles = 5 - editor.object.homework.files.length;
                }
                //Client-server dialog
                editor.on("accepted", function (result) {
                    var icon = $('<i class="ui icon check" style="font-size: 19px; margin: 0; display: none"></i>');
                    $(editor.ajax_button.button).html($(icon).fadeIn(500, function() {
                        editor.window.hide();
                        $(editor.ajax_button.button).html("Сохранить");
                    }));
                    var replace = _this.homework.exists;
                    editor.object.homework = new Homework(result['text'], result['files']);
                    console.log(editor.object.homework);
                    var homework = $(Handlebars.partials['homework'](editor.object.homework.template_data()));
                    if (!replace) {
                        $(editor.object.element)
                            .find(".lesson_controls")
                            .prepend(homework)
                            .append('<div class="lesson_homework_show">Показать ДЗ</div>');
                        Lesson.listeners.homework_show.func(editor.object, Lesson.listeners.homework_show);
                    } else {
                        $(editor.object.element)
                            .find(".lesson_homework_show")
                            .trigger("click");
                        $(editor.object.element)
                            .find(".lesson_homework")
                            .replaceWith(homework);
                    }
                    //reloading listeners for images
                    Lesson.listeners.image_view.func(editor.object, Lesson.listeners.image_view);
                });
                editor.on("rejected", function(response) {

                });
            },
            get_data: function () {
                return {
                    subject: _this.subject
                }
            }
        });
    }
    //Setting up listeners
    this.on("templated", function () {
        for (var listener in Lesson.listeners) {
            Lesson.listeners[listener].func(_this, Lesson.listeners[listener]);
        }
    });

}
Lesson.listeners = {
    "homework_show" : {
        element : ".lesson_homework_show",
        event : "click",
        func : function (lesson, config) {
            if (lesson.homework.exists) {
                $(lesson.element).find(config.element).on(config.event, function () {
                    $(lesson.element).find(".lesson_homework").toggleClass("active").slideToggle(300);
                    $(this).text($(this).text() === "Показать ДЗ" ? "Скрыть ДЗ" : "Показать ДЗ");
                })
            }
        }
    },
    //TODO images по-другому
    "image_view" : {
        element : "img.view",
        event : "click",
        func : function (lesson, config) {
            $(lesson.element).find(config.element).on(config.event, function () {
                var imageArr = $(this).parent().find(config.element);
                var imageUrls = [];
                $(imageArr).each(function () {
                    imageUrls.push($(this).attr("src").replace("uploads/thumbnails/", "uploads/images/"));
                });
                var index = $(imageArr).index(this);
                var viewer = new ImageViewer(imageUrls, index);
                viewer.show(index);
            });
        }
    },
    "editor_setup" : {
        element : ".lesson_edit",
        event : "click",
        func : function (lesson, config) {
            if (Calendar.editor) {
                $(lesson.element).find(config.element).on(config.event, function () {
                    var stored = ModalWindow.findStored(Editor.find, lesson.editor);
                    if (stored !== undefined) {
                        ModalWindow.setActive(stored);
                        ModalWindow.activeWindow.show();
                    } else {
                        lesson.editor.template();
                        lesson.editor.show();
                    }
                });
            }
        }
    }
};
Lesson.prototype = {
    constructor: Lesson,
    get_color: function () {
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
    },
    template_data: function () {
        return {
            subject : this.subject,
            color : this.get_color(),
            type : this.type,
            time : this.time_start + "-" + this.time_end,
            teachers : {
                exists : this.teachers.length > 0,
                text: (this.teachers.length > 1) ? "Ведут: " : "Ведет: ",
                teacher : this.teachers
            },
            places : {
                exists : this.places.length > 0,
                place : this.places
            },
            homework : this.homework.template_data(),
            editor: Calendar.editor
        }
    },
    show: function (time) {
        $(this.element).fadeIn(time);
    },
    hide: function (time) {
        $(this.element).fadeOut(time);
    },
    on: function (event, func) {
        this.emitter.on(event, func);
    }
};

function Homework(text, files) {
    this.exists = false;
    if (text !== null || files !== null) {
        this.exists = true;
        this.text = text || "";
        this.files = files || [];
        for (var i = 0; i < this.files.length; i++)
            this.files['showable'] = this.files['showable'] === 1;
    }
}
Homework.prototype = {
    constructor: Homework,
    template_data: function () {
        return {
            exists: this.exists,
            text: this.text,
            files: this.files
        }
    }

};