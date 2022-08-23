'use strict';
const jQuery = require('jquery');
(function (window, $) {
    window.ContactApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'submit',
            '.form_contact_action',
            this.handleFormContactSubmit.bind(this)
        );
    };

    $.extend(window.ContactApp.prototype, {

        //listener pour form contact
        handleFormContactSubmit: function (e) {
            e.preventDefault();
            var $form = $(e.currentTarget);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (data) {
                    $('#success-alert-modal').modal("show");
                },
                error: function (jqXHR) {
                    $('#danger-alert-modal').modal("show");
                }
            });
        },
    });


})(window, jQuery);
