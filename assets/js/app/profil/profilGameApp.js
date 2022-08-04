'use strict';
const jQuery = require('jquery');
(function (window, $) {
    window.ProfilGameApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.player-switch-check',
            this.handleChangePlayersStatus.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.master-switch-check',
            this.handleChangeMasterStatus.bind(this)
        );
            this.$wrapper.on(
            'submit',
            '.form_post_message_game',
            this.handleFormPostGameMasterCommentarySubmit.bind(this)
        );
    };

    $.extend(window.ProfilGameApp.prototype, {

        //listener pour changer les status des players
        handleChangePlayersStatus: function (e) {
            let $button = $(e.currentTarget);

            $.ajax({
                url: $button.data('url'),
                method: 'POST',
                success: function (data) {
                    alert(data.message);
                },
                error: function (data) {
                    alert(data.message);
                }
            });
        },
        handleChangeMasterStatus: function (e) {
            let $button = $(e.currentTarget);

            $.ajax({
                url: $button.data('url'),
                method: 'POST',
                success: function (data) {
                    alert(data.message);
                },
                error: function (data) {
                    alert(data.message);
                }
            });
        },
        //listener pour edit un game
        handleFormPostGameMasterCommentarySubmit: function (e) {
            e.preventDefault();
            var $form = $(e.currentTarget);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (data) {
                    //$tbody.append(data);

                    if(data.message_master !== undefined){
                        let html = '<div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">\n' +
                            '    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                            data.message_master +
                            '</div>'

                        $('.js-target-game-'+data.id).prepend(html);
                    }

                },
                error: function (jqXHR) {
                    alert('edit failed : ' + jqXHR);
                    $form.closest('.edit_user_form').html(jqXHR.responseText);
                }
            });
        },
    });


})(window, jQuery);
