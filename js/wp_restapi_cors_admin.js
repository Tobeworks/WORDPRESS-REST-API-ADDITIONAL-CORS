$ = jQuery;

$(document).ready(function() {

    checkFormset();

    $('#wprac_enabled').on('click',function(){
        checkFormset();
    });
});

function checkFormset(){
    const is_enabled = $('#wprac_enabled').prop('checked');
    if(is_enabled === true){
        $('#WPRAC_formset').css('display','block');
    }else{
        $('#WPRAC_formset').css('display','none');
    }
}