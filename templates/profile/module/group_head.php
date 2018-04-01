<div class="module" module="<?=$this->module_name;?>" group="<?=$this->chain;?>">
    <div class="box add">
        <div class="ui top attached green big label">Редакторы</div>
        <? if ($this->head_exists): ?>
        <div class="content floating hover">
            <div class="header">
                <img src="data/image/32/<?=$this->head['id'];?>.png">
            </div>
            <div class="text left medium" style="margin-top: 6px;">
                <?=$this->head['login'];?>
                <div class="ui red horizontal label">Староста</div>
            </div>
        </div>
        <div id="editors">
            <? foreach ($this->editors as $editor): ?>
                <div class="content floating hover">
                    <div class="header">
                        <img src="data/image/32/<?=$editor['id'];?>.png">
                    </div>
                    <div class="text left medium" style="margin-top: 6px;">
                        <?=$editor['login'];?>
                        <input class="hidden" value="<?=$editor['login'];?>" hidden>
                        <div class="ui teal horizontal label">Редактор</div>
                    </div>
                    <div class="editor_remove">Удалить</div>
                </div>
            <? endforeach; ?>
        </div>
        <? else: ?>
            <div class="content floating">
                <div class="header">
                    <i class="ui circular inverted red icon warning" style="font-size: 16px"></i>
                </div>
                <div class="text medium">
                    В вашей группе староста пока не подтвежден
                </div>
            </div>
            <div class="ui green small button <?=($this->user->verified) ? "" : "disabled";?>" id="head_request" >
                Я староста :3
            </div>
        <? endif; ?>
        <? if($this->user->is_head): ?>
            <div class="content">
                <div class="search_container">
                    <input type="text" name="query" class="search" id="user_input" placeholder="Добавить редактора">
                    <button class="search_submit" type="submit" id="user_find">
                        <i class="ui icon user add"></i>
                        Добавить
                    </button>
                </div>
            </div>
            <script type="text/javascript">
                var field = new Field($("#user_input"), {"regex_check" : false, "show_errors" : false});
                var button = new AjaxButton($("#user_find"), {"login" : field}, {
                    "url" : "action.php",
                    "data_from_func" : function (elem) {
                        return {
                            "action" : "editor",
                            "event" : "add",
                            "login" : elem.fields["login"].get_value()
                        }
                    }
                });
                button.on("success", function (result) {
                    console.log(result);
                    if (result["response"] !== undefined && result['response'] === true) {
                        var div = $("<div></div>").addClass("content floating hover");
                        $(div).append($("<div></div>").addClass("header").append('<img src="data/image/32/' + result['info']['id'] + '.png">'))
                        var text = $("<div></div>").addClass("text left medium").css("margin-top", "6px");
                        var input = $("<input hidden>").addClass("hidden").attr("value", result['info']['login']);
                        var label = $("<div></div>").addClass("ui teal horizontal label").text("Редактор");
                        var button = $("<div></div>").addClass("editor_remove").text("Удалить");
                        $(text).append(result["info"]["login"], input, label);
                        $(div).append(text, button);
                        $("#editors").append(div);
                        addRemoveButton(button);

                    }
                });
                $(".editor_remove").each(function () {
                    addRemoveButton(this);
                });
                function addRemoveButton(elem) {
                    console.log(elem);
                    var button = new AjaxButton(elem,
                        {
                            "login" : new Field($(elem).parent().find(".text").find("input"), {"regex_check" : false, "show_errors" : false})
                        },
                        {
                            "url": "action.php",
                            "data_from_func": function (elem) {
                                return {
                                    "action": "editor",
                                    "event": "remove",
                                    "login": elem.fields["login"].get_value()
                                }
                            }
                        }
                    );
                    button.on("success", function (result) {
                        if (result["response"] !== undefined && result['response'] === true) {
                            console.log(this.button);
                            $(this.button).parent().remove();
                        }
                    })
                }
            </script>
        <? endif; ?>
    </div>
</div>