$(document).ready(function () {
    calendar.on("constructed", function () {
        calendar.template($("#calendar"), $("#day_lessons"));
    });
    calendar.on("templated", function () {
        if (!calendar.visible) {
            calendar.show(moment().month());
            if (Calendar.activeDay !== undefined)
                Calendar.activeDay.toggle_lessons(true);
        }
    });
    calendar.load_controls({
        next: $(".arrow.right").find("i"),
        prev: $(".arrow.left").find("i")
    });
    calendar.load();
});
