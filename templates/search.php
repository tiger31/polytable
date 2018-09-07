<script type="text/javascript" src="../js/lib/jquery3.2.1.min.js"></script>
<script type="text/javascript" src="../js/lib/jquery.ajax.inputs.js?1337"></script>
<div id="search">
    <input type="text" name="query" class="group_search" placeholder="Группа">
    <button class="group_search_submit" type="submit">Найти</button>
</div>
<div id="group_search_result"></div>
<script type="text/javascript">
    $(".group_search").on("keyup", function (event) {
       if (event.keyCode === 13)
           $(".group_search_submit").trigger("click");
    });
    var Field = new Field($(".group_search"), {"regex": /.{1,16}/, "ajax_ignore": true, "show_errors": false});
    var Button = new AjaxButton($(".group_search_submit"), {"group": Field}, {
        'url': 'search.php',
        "data": function (elem) {
            return {
                'query': elem.fields["group"].get_value()
            }
        }
    });
    Button.on("success", function (result) {
        if (result.length === 1)
            window.location.replace("https://<?=$_SERVER['HTTP_HOST'];?>/groups.php?id=" + result[0]['name']);
        print_result(result);
    });

    function print_result(result) {
        var result_container = $('#group_search_result');
        $(result_container).html("");
        if (result.length === 0) {
        $(result_container).append('<div id="search_non">По Вашему запросу групп не найдено</div>')
        }
        for (var i = 0; i < result.length; i++) {
            var link = "https://<?=$_SERVER['HTTP_HOST'];?>/groups.php?id=" + result[i]['name'];
            $(result_container).append($('<a href="' + link + '" class="group">' + result[i]['name'] + '<span>' + result[i]['faculty_abbr'] + '</span></a>'))
        }
    }
</script>
