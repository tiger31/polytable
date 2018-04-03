function ModalWindow(content, sub) {
    this.element = $('<div class="modal_window"></div>');
    this.subClass = sub;
    this.content = content;
    this.storeInsteadDestroy = false;
    $(this.element).append(content);
    this.set_events();
    this.windows = $(".modal_window_container");
    if (this.windows.length === 0) {
        this.windows = $('<div class="modal_window_container"></div>');
        $(document.body).append(this.windows);
    }
    $(this.windows).append(this.element);
}

ModalWindow.activeWindow = null;
ModalWindow.stored = [];
ModalWindow.queue = [];
ModalWindow.checkForActive = function () {
    return (ModalWindow.activeWindow !== null && ModalWindow.activeWindow !== undefined);
};
ModalWindow.setActive = function (window) {
    if (ModalWindow.checkForActive() && ModalWindow.activeWindow !== window) {
        if (ModalWindow.activeWindow.storeInsteadDestroy) {
            ModalWindow.activeWindow.hide();
        } else {
            ModalWindow.activeWindow.destroy();
        }
    }
    if (window.storeInsteadDestroy && !window.stored) {
        ModalWindow.stored.push(window);
        window.stored = true;
    }
    ModalWindow.activeWindow = window;
};
ModalWindow.setSubActive = function (window) {
    if (ModalWindow.checkForActive()) {
        $(ModalWindow.activeWindow.element).hide(0);
        ModalWindow.activeWindow = window;
        ModalWindow.setActive(window);
    }
    ModalWindow.setActive(window);
};
ModalWindow.findStored = function (func, args) {
    return ModalWindow.stored.find(func, args);
};

ModalWindow.prototype = {
    constructor: ModalWindow,
    show: function () {
        if (ModalWindow.activeWindow !== this) {
            ModalWindow.activeWindow.hide();
            ModalWindow.setActive(this);
        }
        if (ModalWindow.activeWindow === this) {
            $(this.element).show(0);
        }
    },
    hide: function () {
        if (ModalWindow.activeWindow === this) {
            $(this.element).hide(0);
            ModalWindow.activeWindow = null;
        }
        if (ModalWindow.queue.length > 0) {
            ModalWindow.setActive(ModalWindow.queue.pop());
        }
        $(document).off("keydown");
    },
    destroy: function () {
        if (ModalWindow.activeWindow === this) {
            $(this.element).remove();
            ModalWindow.activeWindow = null;
        }
        $(document).off("keydown");
    },
    set_events: function () {
        var _this = this;
        $(this.element).on("click", function () {
            if (_this.storeInsteadDestroy)
                _this.hide();
            else
                _this.destroy();
        }).children(":not(a)").click(function () {
            return false;
        });
    }
};

function Editor(object, config) {
    this.emitter = new Emitter("templated sent accepted rejected", this);
    this.object = object;
    this.config = config;
    this.element = undefined;

    this.model = config.template;
    //New

    this.on("templated", function () {
        this.window = new ModalWindow($(this.element), this);
        this.window.storeInsteadDestroy = true;
        this.config.template_created(this);
    })

}
Editor.prototype = {
    constructor: Editor,
    show: function () {
        ModalWindow.setActive(this.window);
        this.window.show();
    },
    on: function (event, func) {
       this.emitter.on(event, func);
    },
    template: function () {
        this.element = $(this.model(this.config.get_data()));
        this.emitter.emit("templated");
    }
};
Editor.find = function(element) {
        element = element.subClass;
        return (
            element instanceof Editor &&
            element.object === this.object
        );
};

function ImageViewer(imageUrl, index) {
    this.image = [];
    this.active = 0;

    var _this = this;

    if (Array.isArray(imageUrl)) {
        imageUrl.forEach(function (url) {
            var image = new Image();
            image.src = url;
            _this.image.push(image);
        });
        if (index < this.image.length && index > 0)
            this.active = index;
    } else {
        var image = new Image();
        image.src = url;
        this.image.push(image);
    }

    this.element = $("<div></div>").addClass("imageViewer");
    this.content = $("<div class='imageViewerContent'></div>");
    this.controlsElement = $("<div class='controls'></div>");

    this.controls = {
        "close" : $("<div class='imageViewerClose'></div>"),
        "next" : $("<div class='imageViewerNext'></div>"),
        "previous" : $("<div class='imageViewerPrevious'></div>")
    };

    this.controlsElement.append(this.controls['close'], this.controls['next'], this.controls['previous']);
    $(this.element).append(this.controlsElement, this.content);
    this.window = new ModalWindow(this.element, this);


    $(this.controls['close']).on("click", function () {
        _this.window.destroy();
    });
    $(this.controls['next']).on("click", function () {
        _this.next();
    });
    $(this.controls['previous']).on("click", function () {
        _this.previous();
    });
    $(document).on("keydown", function (event) {
        switch (event.keyCode) {
            case 37:
                _this.previous();
                break;
            case 39:
                _this.next();
                break;
            case 27:
                _this.window.destroy();
                break;
        }
    })
}
ImageViewer.prototype = {
    constructor: ImageViewer,
    show: function (index) {
        if (ModalWindow.activeWindow !== this.window) {
            ModalWindow.setActive(this.window);
            this.window.show();
        }
        if (index === undefined) {
            this.show(0);
        } else if (index < this.image.length && index >= 0) {
            var _this = this;
            if (this.image[index].complete === true) {
                this.set_image(this.image[index], index);
            } else {
                this.image[index].onload = function () {
                    _this.set_image(this, index);
                }
            }


        }
    },
    next: function () {
        this.show(this.active + 1);
    },
    previous: function () {
        this.show(this.active - 1);
    },
    calc_size: function (width, height) {
        var maxSize = 0.8;
        var minWidth = 400;
        var minHeight = 300;

        var windowWidth = document.body.clientWidth;
        var windowHeight = document.body.clientHeight;

        var maxWidth = windowWidth * maxSize;
        var maxHeight = windowHeight * maxSize;

        var ratio = width / height;

        var resultWidth;
        var resultHeight;

        if (width > height) {
            resultWidth = (width < minWidth) ? minWidth : (width > maxWidth) ? maxWidth : width;
            resultHeight = resultWidth / ratio;
            if (resultHeight > maxHeight) {
                resultHeight = maxHeight;
                resultWidth = resultHeight * ratio;
            }
        } else {
            resultHeight = (height < minHeight) ? minHeight : (height > maxHeight) ? maxHeight : height;
            resultWidth = resultHeight * ratio;
            if (resultWidth > maxWidth) {
                resultWidth = maxWidth;
                resultHeight = resultWidth / ratio;
            }
        }
        return([resultWidth, resultHeight]);
    },
    refresh_controls: function (imageSize) {
        $(this.controls['previous']).show(0);
        $(this.controls['next']).show(0);

        var top = imageSize[1] / 2 - 17.5;
        $(this.controls['next']).css("top", top);
        $(this.controls['previous']).css("top", top);

        if (this.active === 0) $(this.controls['previous']).hide(0);
        if (this.active === this.image.length - 1) $(this.controls['next']).hide(0);
    },
    set_image: function (image, index) {
        $(this.content).html("");
        var container = $("<div class=\"imageContainer\"></div>");
        var controls = $("<div class=\"imageControls\"></div>");
        var size = this.calc_size(image.width, image.height);
        var original = $("<a>Открыть оригинал</a>").attr("href", image.src).addClass("imageOriginal").attr("target", "_blank");
        original.on("click", function () {
            window.open($(this).attr('href'), "_blank");
        });

        $(controls).append($("<div class=\"imageCount\"></div>").text((index + 1) + " из " + this.image.length));
        $(controls).append(original);
        $(container).append(image);
        $(this.content).append(container, controls);

        $(this.controlsElement).css("height", size[1]);
        $(container).css({"width" : size[0], "height" : size[1]});
        $(this.element).css({"width" : size[0], "height" : size[1] + 50});
        if (image.width < size[0] && image.height < size[1])
            $(image).css({"width" : image.width, "height" : image.height});
        else
            $(image).css({"width" : size[0], "height" : size[1]});
        this.active = index;
        this.refresh_controls(size);
    }
};

function ShowContent(homework, header) {
    var _this = this;
    this.element = $('<div class="homework_full"></div>');
    this.close = $('<div class="homework_title_close"><img src="../../assets/images/x.png"></div>');
    this.title = $('<div class="homework_title"><div class="editor_title_text">' + $(header).find(".lesson_title").text() + '</div></div>')
        .append(this.close);
    this.subject = $('<div class="homework_subject"></div>').append($(header).find(".lesson_type").clone());

    $(this.element).append(this.title, this.subject);
    this.window = new ModalWindow(this.element, this);
    
    $(this.close).on("click", function () {
        _this.window.destroy();
    })
}
ShowContent.prototype = {
    constructor: ShowContent,
    show: function () {
        if (ModalWindow.activeWindow !== this.window) {
            ModalWindow.setActive(this.window);
            this.window.show();
        }
    }
};

function get_values(obj) {
    var values = [];
    for (var key in obj) {
        if (obj.hasOwnProperty(key))
            values.push(obj[key]);
    }
    return values;
}

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
    dictMaxFilesExceeded: "Нельзя прикреплять больше пяти фалов"
};


