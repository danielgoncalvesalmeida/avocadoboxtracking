
$(function () {
    $('#datetimepicker1').datetimepicker({
        locale: 'fr',
        format: 'L',
        calendarWeeks: true,
        showTodayButton: true
    });
    $('#datetimepicker2').datetimepicker({
        locale: 'fr',
        format: 'L',
        calendarWeeks: true,
        showTodayButton: true
    });
    
    $("#datetimepicker1").on("dp.change", function (e) {
        $('#datetimepicker2').data("DateTimePicker").minDate(e.date);
    });
    $("#datetimepicker2").on("dp.change", function (e) {
        $('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
    });
});

