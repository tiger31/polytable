<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/modules/Config.php"; ?>
<html>
<head>
    <meta charset="utf-8">
    <title>Log Reader</title>
    <link rel="icon" type="image/png" href="assets/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="assets/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="assets/favicon-96x96.png" sizes="96x96">
    <link rel="stylesheet" type="text/css" href="css/logs.css"/>
    <script type="text/javascript" src="js/lib/jquery3.2.1.min.js"></script>
    <script type="text/javascript" src="js/lib/handlebars-latest.js"></script>
    <script type="text/javascript" src="js/lib/jquery.ajax.inputs.js?1337"></script>
    <script type="text/javascript">


        function log(data) {
            AjaxModule.apply(this, [log.config]);
            this.data = data;
            this.on("templated", function () {
                this.setter.set("clicked");
            });
        }

        log.config = {
            name: 'log',
            node: '#logged',
            templates: {
                main: 'log',
                templates: [
                    {
                        name: 'log',
                        path: 'templates/controls/log.handlebars',
                        type: 'template'
                    }
                ]
            },
            events: {
                clicked: {
                    type: "html",
                    object: "div",
                    event: "click",
                    handler: function (object) {
                        $(object).find('.full_info').slideToggle(0);
                    }
                }
            }
        };

        log.prototype = Object.create(AjaxModule.prototype);
        log.prototype.constructor = log;
        log.prototype.template_data = function () {
            let data = [];
            for (let obj of this.data)
                data.push(Object.assign(this.template_object(), obj));
            return {data: data};
        };
        logger = new log([]);

        function view(lines) {
            $.getJSON({
                url: "action.php",
                data: {
                    action: "log_reader",
                    lines: lines
                },
                success: function (response) {
                    if (response['response']) {
                        logger.data = response['data'];
                        logger.template();
                    }
                }
            });
        }

        $(document).ready(
            function () {
                let num = 1;
                view(num * 10);
                $('#all').on("click", function () {
                    view(Number.MAX_SAFE_INTEGER);
                    $(this).hide(0);
                });
                $('#more').on("click", function () {
                    view((num + 1) * 10);
                    num++;
                });
            });
    </script>
</head>
<body>
<div id="logged"></div>
<button id="more">Show 10 more</button>
<button id="all">Show all</button>
</body>
</html>