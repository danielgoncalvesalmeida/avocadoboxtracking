
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

