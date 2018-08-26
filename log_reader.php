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
        function view(lines) {
            $.getJSON({
                url: "action.php",
                data: {
                    action: "log_reader",
                    lines: lines
                },
                success: function (response) {
                    if (response['response']) {
                        console.log(response);
                        let logger = new log(response['data']);
                        logger.template();
                        $("#all").show();
                    }
                }
            });
        }

        function log(data) {
            AjaxModule.apply(this, [log.config]);
            this.data = data;
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
            }, events: {}
        };

        log.prototype = Object.create(AjaxModule.prototype);
        log.prototype.constructor = log;
        log.prototype.template_data = function () {
            return Object.assign(this.template_object(), {data: this.data})
        };

        $(document).ready(view(10));
    </script>
</head>
<body>
<div id="logged"></div>
<button id="more" hidden>Show 10 more</button>
<button id="all" hidden>Show all</button>
<script>
    $("#all").click(function () {
        $.getJSON({
            url: "action.php",
            data: {
                action: "log_reader",
                lines: Number.MAX_SAFE_INTEGER
            },
            success: function (response) {
                if (response['response']) {
                    console.log(response);
                    let logger = new log(response['data']);
                    logger.template();
                    $("#all").hide();
                }
            }
        });
    })

</script>
</body>
</html>