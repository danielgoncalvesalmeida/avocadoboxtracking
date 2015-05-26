
// Begin of document ready
$(document).ready(function(){
    
    // Switch between tabs
    $(".nav-tabs li").click(function(event){
        var divtarget = $(this).data('divtarget');
        
        $(".nav-tabs li").removeClass('active');
        $(this).addClass('active');
        
        $(".tab-container").hide();
        $("#"+divtarget).show();
    });
    
    // Open close help panels
    $(".help-header").click(function(event){
        var divtarget = $(this).data('divtarget');
        
        $("#"+divtarget).toggle();
        event.preventDefault();
    });
    
    
    
}); // End of document ready

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

