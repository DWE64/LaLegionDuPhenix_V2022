$(document).ready(function() {


    //======================================
    //Fonctionnalité pour add un game
    //======================================
    let $wrapper=$('.js-profil-user');
    let modalProfilApp= new ModalProfilApp($wrapper);

    let $wrapperGame=$('.js-profil-game');
    let profilGameApp= new ProfilGameApp($wrapperGame);

    /*let $wrapperDelete=$('.js-manage-game-delete');
    let repLogDeleteApp= new RepLogApp($wrapperDelete);*/


});