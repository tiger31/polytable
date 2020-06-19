$(document).ready(function () {
    $("#header").find("td").append($("<div id='group'></div>").text(Calendar.group_id));
    calendar.on("constructed", function () {
        calendar.node = $("#calendar-block");
        calendar.template();
    });
    calendar.load();
});
