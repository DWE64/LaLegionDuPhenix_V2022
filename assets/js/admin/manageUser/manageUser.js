$(document).ready(function() {
    $('.switch_is_member_association').click((e)=>{
        let $button = $(e.currentTarget);


        $.ajax({
            url: $button.data('url'),
            method: 'POST',
            success: function(data){
                $('.user-associationRegistrationDate-'+data.id).text(data.associationRegistrationDate);
                $('.user-updatedAt-'+data.id).text(data.updatedAt);
                $('.user-memberStatus-'+data.id).text(data.memberStatus);
                alert('Changement effectue : '+data.status);
            },
            error: function(jqXHR){
                alert('Changement effectué : '+jqXHR);
            }
        });
    });

    //======================================
    //Fonctionnalité pour charger en dynamique les status
    //Abandonné car pour une raison inconnu l'event se déclenche en double
    //piste =>Problème de librairie ???
    //======================================
    /*$('.modal').on('shown.bs.modal',(e)=>{
        console.log('vu n°1');
        let $select = $(e.currentTarget).find('.check-oldest');
        console.log('vu n°2');
        $.ajax({
            url: $select.data('url'),
            method: 'GET',
            success: function (data) {
                console.log(data);
                for (let i = 0; i < data.seniority.length; i++) {
                    console.log('boucle n°:' + i);
                    let $option = "<option value='" + data.seniority[i] + "'>" + data.seniority[i] + "</option>"
                    //$('.load-seniority-'+data.id).append($option);
                    $select.append($option);
                    console.log('append fait');
                }
            },
            error: function (jqXHR) {
                alert('Une erreur est survenue : ' + jqXHR);
            }
        });

    });*/

    //======================================
    //Fonctionnalité pour edit user
    //======================================
    let $wrapper=$('.js-manage-user');
    let repLogApp= new RepLogApp($wrapper);

    let $wrapperRole=$('.js-manage-role-user');
    let repLogRoleApp= new RepLogApp($wrapperRole);



});