'use strict';
const jQuery = require('jquery');
(function (window, $) {
    window.RepLogApp = function ($wrapper) {
        this.$wrapper = $wrapper;

        this.$wrapper.on(
            'submit',
            '.add_game_form',
            this.handleAddGameFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'click',
            '.delete-game',
            this.handleDeleteGame.bind(this)
        )

        this.$wrapper.on(
            'submit',
            '.edit_game_form',
            this.handleEditFormSubmit.bind(this)
        );

        this.$wrapper.on(
            'submit',
            '.add_player_game_form',
            this.handleAddPlayerGame.bind(this)
        )

        this.$wrapper.on(
            'click',
            '.button-modal-delete-player',
            this.handleGetPlayersGame.bind(this)
        )

        this.$wrapper.on(
            'submit',
            '.delete_player_game_form',
            this.handleDeletePlayerGame.bind(this)
        )

        this.$wrapper.on(
            'submit',
            '.change_status_game_form',
            this.handleChangeStatusGame.bind(this)
        )

        this.$wrapper.on(
            'click',
            '.button-filter',
            this.handleFilterStatusGame.bind(this)
        )

        this.$wrapper.on(
            'change',
            '.toggle-switch input',
            this.handleChangePlayersStatus.bind(this)
        )
    };

    $.extend(window.RepLogApp.prototype, {

        //listener pour ajouter un game
        handleAddGameFormSubmit: function (e) {
            e.preventDefault();
            let $form = $(e.currentTarget);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (data) {
                    $(".game-manage-modal-add").modal('toggle');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('.list-game').append(data.view);
                },
                error: function (jqXHR) {
                    alert('edit failed : ' + jqXHR);
                    $form.closest('.add_game_form').html(jqXHR.responseText);
                }
            });
        },

        //listener pour delete un game
        handleDeleteGame: function (e) {
            e.preventDefault();
            var $deleteAction = $(e.currentTarget);

            $.ajax({
                url: $deleteAction.data('url'),
                method: 'DELETE',
                success: function (data) {
                    $('.game-' + data.id).remove();
                },
                error: function (jqXHR) {
                    alert('delete failed : ' + jqXHR);
                    $deleteAction.closest('.js-manage-game-delete').html(jqXHR.responseText);
                }
            });
        },

        handleEditFormSubmit: function (e) {
            e.preventDefault();
            var $form = $(e.currentTarget);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function (data) {
                    //$tbody.append(data);
                    $('.game-updatedAt-' + data.id).text(data.updatedAt);

                    if (data.title !== undefined) {
                        $('.game-title-' + data.id).text(data.title);
                    }
                    if (data.description !== undefined) {
                        $('.game-description-' + data.id).text(data.description);
                    }
                    if (data.gameMaster !== undefined) {
                        $('.game-gameMaster-' + data.id).text(data.gameMaster);
                    }
                    if (data.weekSlot !== undefined) {
                        $('.game-weekSlot-' + data.id).text(data.weekSlot);
                    }
                    if (data.hourSlot !== undefined) {
                        $('.game-hourSlot-' + data.id).text(data.hourSlot);
                    }
                    if (data.minPlaceGame !== undefined) {
                        $('.game-minPlaceGame-' + data.id).text(data.minPlaceGame);
                    }
                    if (data.maxPlaceGame !== undefined) {
                        $('.game-maxPlaceGame-' + data.id).text(data.maxPlaceGame);
                    }

                    $(".game-manage-modal-edit-" + data.id).modal('hide');
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
                    url: '/admin/manage/game/'+$form.data('id')+'/edit_picture',
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
                            $('.game-updatedAt-' + data.id).text(data.updatedAt);
                            $('.game-picture-' + data.id).replaceWith(data.picture);
                        }
                    },
                    error: function (jqXHR) {
                        alert('edit failed : ' + jqXHR);
                        $form.closest('.add_game_form').html(jqXHR.responseText);
                    }
                });
            }
        },

        //listener pour add player
        handleAddPlayerGame: function (e) {
            e.preventDefault();
            let $form = $(e.currentTarget);

            let $inputCheck = $form.find('input:checked');

            $inputCheck.each(index => {
                let data = {
                    'player': $inputCheck[index].value
                }
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: data,
                    success: function (data) {
                        $('.game-updatedAt-' + data.id).text(data.updatedAt);
                        $('.game-assignedGamePlace-' + data.id).text(data.assignedPlace);
                    },
                    error: function (jqXHR) {
                        alert('edit failed : ' + jqXHR);
                        $form.closest('.add_player_game_form').html(jqXHR.responseText);
                    }
                });

            });

            $(".game-manage-modal-add-player-" + $form.data('id-game')).modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        },

        //listener pour récupere les players
        handleGetPlayersGame: function (e) {
            e.preventDefault();
            let $button = $(e.currentTarget);

            $.ajax({
                url: $button.data('url'),
                method: 'GET',
                success: function (data) {

                    $('#delete_player_game_form_'+data.id).append(data.view);
                },
                error: function (jqXHR) {
                    alert('get player failed : ' + jqXHR);
                }
            });
        },

        //listener pour delete player
        handleDeletePlayerGame: function (e) {
            e.preventDefault();
            let $form = $(e.currentTarget);

            let $inputCheck = $form.find('input:checked');

            $inputCheck.each(index => {
                let data = {
                    'player': $inputCheck[index].value
                }
                $.ajax({
                    url: $form.attr('action'),
                    method: 'DELETE',
                    data: data,
                    success: function (data) {
                        $('.game-updatedAt-' + data.id).text(data.updatedAt);
                        $('.game-assignedGamePlace-' + data.id).text(data.assignedPlace);
                    },
                    error: function (jqXHR) {
                        alert('edit failed : ' + jqXHR);
                        $form.closest('.add_player_game_form').html(jqXHR.responseText);
                    }
                });

            });

            $(".game-manage-modal-delete-player-" + $form.data('id-game')).modal('hide');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        },

        //listener pour changer le status du game
        handleChangeStatusGame: function(e) {
            e.preventDefault();
            var $form= $(e.currentTarget);

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: $form.serialize(),
                success: function(data){
                    //$tbody.append(data);
                    $('.game-updatedAt-'+data.id).text(data.updatedAt);

                    if(data.status !== undefined){
                        let html =''
                        if(data.status == 'NEW_GAME'){
                            html = '<div class="badge bg-warning mb-3 game-status-'+data.id+'" data-value="'+data.status+'">'+data.status+'</div>';
                        }
                        if(data.status == 'ACTIVE_GAME'){
                            html = '<div class="badge bg-primary mb-3 game-status-'+data.id+'" data-value="'+data.status+'">'+data.status+'</div>';
                        }
                        if(data.status == 'FINISH_GAME'){
                            html = '<div class="badge bg-success mb-3 game-status-'+data.id+'" data-value="'+data.status+'">'+data.status+'</div>';
                        }
                        $('.game-status-'+data.id).replaceWith(html);
                    }

                    $(".game-manage-modal-change-status-" + data.id).modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                },
                error: function(jqXHR){
                    alert('edit failed : '+jqXHR);
                    $form.closest('.change_status_game_form').html(jqXHR.responseText);
                }
            });
        },

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

        //listener pour changer les status des players
        handleChangePlayersStatus: function (e) {
            let $input = $(e.currentTarget);

            $.ajax({
                url: $input.data('url'),
                method: 'POST',
                data: { status: $input.data('custom-switch') },
                success: function (data) {
                    alert('player change status in game: ' + data.firstname + " " + data.name);
                },
                error: function (jqXHR) {
                    alert('get player failed : ' + jqXHR);
                }
            });
        },
    });


/*handleNewRoleSubmit: function(e) {
    e.preventDefault();
    var $form= $(e.currentTarget);

    $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        data: $form.serialize(),
        success: function(data){
            //$tbody.append(data);
            $('.user-updatedAt-'+data.id).text(data.updatedAt);

            if(data.roles !== undefined){
                $('.user-roles-'+data.id).text(data.roles);
            }

            $(".edit-role-modal").fadeOut('fast');
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
        },
        error: function(jqXHR){
            alert('edit failed : '+jqXHR);
            $form.closest('.edit_user_form').html(jqXHR.responseText);
        }
    });
},*/


})(window, jQuery);
