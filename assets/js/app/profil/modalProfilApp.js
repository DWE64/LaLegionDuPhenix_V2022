        'use strict';
        const jQuery = require('jquery');
        (function (window, $) {
            window.ModalProfilApp = function ($wrapper) {
                this.$wrapper = $wrapper;

                this.$wrapper.on(
                    'submit',
                    '.edit_user_form',
                    this.handleEditFormSubmit.bind(this)
                );

            };

            $.extend(window.ModalProfilApp.prototype, {

                //listener pour edit un game
                handleEditFormSubmit: function (e) {
                    e.preventDefault();
                    var $form = $(e.currentTarget);

                    $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: $form.serialize(),
                        success: function (data) {
                            //$tbody.append(data);

                            if (data.name !== undefined && data.firstname !== undefined) {
                                $('.user-name-firstname-' + data.id).text(data.firstname+' '+data.name);
                            }else if(data.name !== undefined && data.firstname == undefined){
                                $('.user-name-firstname-' + data.id).text(data.name);
                            }else if(data.name == undefined && data.firstname !== undefined){
                                $('.user-name-firstname-' + data.id).text(data.firstname);
                            }else{

                            }
                            if(data.birthday !== undefined){
                                $('.user-birthday-'+data.id).text(data.birthday);
                            }
                            if(data.username !== undefined){
                                $('.user-username-'+data.id).text(data.username);
                            }
                            if(data.address !== undefined){
                                $('.user-address-'+data.id).text(data.address);
                            }
                            if(data.city !== undefined){
                                $('.user-city-'+data.id).text(data.city);
                            }
                            if(data.zip !== undefined){
                                $('.user-postal-code-'+data.id).text(data.zip);
                            }

                            $(".user-profil-modal-"+ data.id).modal('hide');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        },
                        error: function (jqXHR) {
                            alert('edit failed : ' + jqXHR);
                            $form.closest('.edit_user_form').html(jqXHR.responseText);
                        }
                    });
                    if($form.find("#picture").get(0).files) {
                        let formData = new FormData();
                        formData.append('picture', $form.find("#picture").prop('files')[0]);

                        $.ajax({
                            url: '/profil/edit/'+$form.data('id')+'/edit_picture',
                            method: 'POST',
                            data: formData,
                            dataType: 'text',
                            contentType: false,
                            processData: false,
                            success: function (data) {
                                //on fait un JSON.parse(data) car pour envoyé l'image, on a forcé l'annulation du type d'envoi json
                                // de ce fait le systeme ne s'attend pas a recevoir du json
                                data = JSON.parse(data);
                                if (data.picture !== undefined) {
                                     $('.user-picture-' + data.id).replaceWith(data.picture);
                                }
                            },
                            error: function (jqXHR) {
                                alert('edit failed : ' + jqXHR);
                                $form.closest('.edit_user_form').html(jqXHR.responseText);
                            }
                        });
                    }
                },

            });


        })(window, jQuery);
