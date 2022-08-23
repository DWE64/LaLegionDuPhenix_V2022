'use strict';
const jQuery = require('jquery');
(function (window, $) {
    window.RepLogApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'click',
            '.button-filter',
            this.handleFilterStatusGame.bind(this)
        )

    };

    $.extend(window.RepLogApp.prototype, {

        //listener pour filtrer sur les status
        handleFilterStatusGame: function(e) {
            e.preventDefault();
            let $form= $(e.currentTarget);
            let $listDiv = $('.list-game').find('.js-filter');
            $listDiv.each(index=>{
                let gameId=$listDiv[index].dataset.value

                if($form.data('filter') === 'all'){
                    $('#game-'+gameId).show();
                }else{
                    if($('.game-status-'+gameId).data('value') !== $form.data('filter')){
                        $('#game-'+gameId).hide();
                    }else{
                        $('#game-'+gameId).show();
                    }
                }
            })
        },

    });


})(window, jQuery);
