'use strict';
const jQuery = require('jquery');
(function (window, $) {
    window.ProfilGameApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'change',
            '.player-switch-check, .master-switch-check',
            this.handleChangePlayersStatus.bind(this)
        );

        this.$wrapper.on(
            'submit',
            '.form_post_message_game',
            this.handleFormPostGameMasterCommentarySubmit.bind(this)
        );
    };

    $.extend(window.ProfilGameApp.prototype, {

        handleChangePlayersStatus: function (e) {
            let $input = $(e.currentTarget);

            $.ajax({
                url: $input.data('url'),
                method: 'POST',
                data: {
                    status: $input.data('custom-switch'),
                    userid: $input.data('userid')
                },
                success: function(data) {
                    console.log('Status changed for user: ', data.userid, ' to status: ', data.status);
                },
                error: function(jqXHR) {
                    console.error('Error changing player status: ', jqXHR);
                }
            });
        },

        handleFormPostGameMasterCommentarySubmit: function (e) {
            e.preventDefault();
            var $form = $(e.currentTarget);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (data) {
                    if (data.message_master !== undefined) {
                        let html = '<div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">\n' +
                            '    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                            data.message_master +
                            '</div>';

                        $('.js-target-game-' + data.id).prepend(html);
                    }
                },
                error: function (jqXHR) {
                    alert('edit failed : ' + jqXHR);
                    $form.closest('.edit_user_form').html(jqXHR.responseText);
                }
            });
        }
    });

})(window, jQuery);
