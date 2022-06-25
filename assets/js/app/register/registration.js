$(document).ready(function() {

    $('#registration_form').on('submit',(e)=>{
        e.preventDefault();

        var $form= $(e.currentTarget);
        const $array =$form.find("input");
        var inputForm= {
            'email': $array[0].value,
            'plainPassword': $array[1].value
        };
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: inputForm,
            success: function(data){
                //$tbody.append(data);
                console.log(data);
                //window.location.replace(data);
            },
            error: function(jqXHR){
                alert('registration failed');
                console.log(jqXHR);
                console.log('data: '+JSON.parse(jqXHR));
                //$form.closest('.js-new-rep-log-form-wrapper').html(jqXHR.responseText);
            }
        });
    });
});