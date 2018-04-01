<div class="module" module="<?=$this->module_name;?>" group="<?=$this->chain;?>">
    <div class="box add">
        <div class="ui top attached green big label">Кэш расписания</div>
        <div class="content floating">
            <div class="header">
                <i class="ui icon refresh"></i>
                Обновлен:
            </div>
            <div class="text" id="cached_last">
                <?=$this->cached_last;?>
            </div>
        </div>
        <div class="content floating">
            <div class="header">
                <i class="ui icon calendar checked"></i>
                Действует до:
            </div>
            <div class="text" id="cached_until">
                <?=$this->cached_until;?>
            </div>
        </div>
        <div class="ui labeled button<?=($this->user->group_editor()) ? "" : " disabled";?>" id="group_cache">
            <div class="ui green mini button">
                <i class="icon refresh"></i>
                Обновить
            </div>
            <a class="ui basic left pointing green label">
                <?=$this->group['recache_count'];?>
            </a>
        </div>
        <script type="text/javascript">
            var button = new AjaxButton($("#group_cache"), {}, {
                "url" : "action.php",
                "data_from_func" : function (elem) {
                    return {
                        "action" : "cache",
                        "subject" : "group"
                    }
                }
            });
            button.on("sent", function () {
                $(this.button).addClass("loading");
            });
            button.on("success", function (result) {
                $(this.button).removeClass("loading");
                if (result["response"] !== undefined && result["response"] === true) {
                    $(this.button).find("a").text(result["info"]["left"]);
                    $("#cached_last").text(result["info"]["cached_last"]);
                    $("#cached_until").text(result["info"]["cached_until"])
                }
            });
        </script>
    </div>
</div>


