/*
 * Administration - Employees
 * 
 */

// Begin of document ready
$(document).ready(function(){
    
    // For flash notices
    $('#flash_success').delay(7000).fadeOut(600);
    
    // Confirm deletion
    $(".btdelete_employee").click(function(event){
        if(confirm('Do you really want to delete the employee?')){
           return true;  
        } else {
           event.preventDefault(); 
        }
    });
    
    // Check social id
    $("#iSocialid").change(function(){
        var errorCount = 0;
        $('#cgSocialid').removeClass('has-error');
        $('#cgSocialid .help-block').hide();
        
        /*
        if($('#iSocialid').val() == ''){
            $('#cgSocialid').addClass('has-error');
            $('#cgSocialid .msg-required').show();
            errorCount++;
        } else {
            $('#cgSocialid').removeClass('has-error');
            $('#cgSocialid .msg-required').hide();
        }
        */
        
        var id = $("#id").val();
        if(!id > 0) id = 0;
        
        $.ajax({
                type: 'POST',
                url: base_url+'admin/employees/ajax',
                async: true,
                cache: false,
                dataType: 'json', 
                data : csrf_token_name+'='+csrf_token_hash+'&type=1&value='+$(this).val()+'&exclude='+id,
                success: function(data)
                { 
                    if(data === true){
                        $('#cgSocialid').addClass('has-error');
                        $('#cgSocialid .msg-exists').show();
                    } else {
                        $('#cgSocialid').removeClass('has-error');
                        $('#cgSocialid .msg-exists').hide();
                    }

                }
        });
    });
    
    
    // Check birth date
    $("#iBirthdate").change(function(){
        var errorCount = 0;
        $('#cgBirthdate').removeClass('error');
        $('#cgBirthdate .help-block').hide();
        
        if($('#iBirthdate').val() == ''){
            $('#cgBirthdate').addClass('has-error');
            $('#cgBirthdate .msg-required').show();
            errorCount++;
        } else {
            $('#cgBirthdate').removeClass('has-error');
            $('#cgBirthdate .msg-required').hide();
        }
        
        $.ajax({
                type: 'POST',
                url: base_url+'admin/employees/ajax',
                async: true,
                cache: false,
                dataType: 'json', 
                data : csrf_token_name+'='+csrf_token_hash+'&type=2&value='+$(this).val(),
                success: function(data)
                { 
                    if(data === false){
                        $('#cgBirthdate').addClass('has-error');
                        $('#cgBirthdate .msg-invalid').show();
                    } else {
                        $('#cgBirthdate').removeClass('has-error');
                        $('#cgBirthdate .msg-invalid').hide();
                    }
                }
        });
    });
    
    
    // Check employee number
    $("#iEmployeenumber").change(function(){
        var errorCount = 0;
        $('#cgEmployeenumber').removeClass('has-error');
        $('#cgEmployeenumber .help-block').hide();
        
        var id = $("#id").val();
        if(!id > 0) id = 0;
        
        $.ajax({
                type: 'POST',
                url: base_url+'admin/employees/ajax',
                async: true,
                cache: false,
                dataType: 'json', 
                data : csrf_token_name+'='+csrf_token_hash+'&type=3&value='+$(this).val()+'&exclude='+id,
                success: function(data)
                { 
                    if(data === true){
                        $('#cgEmployeenumber').addClass('has-error');
                        $('#cgEmployeenumber .msg-exists').show();
                    } else {
                        $('#cgEmployeenumber').removeClass('has-error');
                        $('#cgEmployeenumber .msg-exists').hide();
                    }

                }
        });
    });
    
    // Check email
    $("#iEmail").change(function(){
        var errorCount = 0;
        $('#cgEmail').removeClass('has-error');
        $('#cgEmail .help-block').hide();
        
        var id = $("#id").val();
        if(!id > 0) id = 0;
        
        $.ajax({
                type: 'POST',
                url: base_url+'admin/employees/ajax',
                async: true,
                cache: false,
                dataType: 'json', 
                data : csrf_token_name+'='+csrf_token_hash+'&type=4&value='+$(this).val()+'&exclude='+id,
                success: function(data)
                { 
                    if(data === true){
                        $('#cgEmail').addClass('has-error');
                        $('#cgEmail .msg-exists').show();
                    } else {
                        $('#cgEmail').removeClass('has-error');
                        $('#cgEmail .msg-exists').hide();
                    }

                }
        });
    });
    
    // Check username
    $("#iUsername").change(function(){
        var errorCount = 0;
        $('#cgUsername').removeClass('has-error');
        $('#cgUsername .help-block').hide();
        
        var id = $("#id").val();
        if(!id > 0) id = 0;
        
        $.ajax({
                type: 'POST',
                url: base_url+'admin/employees/ajax',
                async: true,
                cache: false,
                dataType: 'json', 
                data : csrf_token_name+'='+csrf_token_hash+'&type=5&value='+$(this).val()+'&exclude='+id,
                success: function(data)
                { 
                    if(data === true){
                        $('#cgUsername').addClass('has-error');
                        $('#cgUsername .msg-exists').show();
                    } else {
                        $('#cgUsername').removeClass('has-error');
                        $('#cgUsername .msg-exists').hide();
                    }

                }
        });
    });
    
    
    $("#submitAdd").click(function(event){
        var errorCount = 0;
        
        if($('#iFirstname').val() == ''){
            $('#cgFirstname').addClass('has-error');
            $('#cgFirstname .msg-required').show();
            errorCount++;
        } else {
            $('#cgFirstname').removeClass('has-error');
            $('#cgFirstname .msg-required').hide();
        }
        
        if($('#iLastname').val() == ''){
            $('#cgLastname').addClass('has-error');
            $('#cgLastname .msg-required').show();
            errorCount++;
        } else {
            $('#cgLastname').removeClass('has-error');
            $('#cgLastname .msg-required').hide();
        }
        
        if(errorCount > 0){
            event.preventDefault();
        }
        
    });
    
    $("#iPassword1").change(function(event){
        var content = $(this).val();
        
    });
    
    
}); // End of document ready

