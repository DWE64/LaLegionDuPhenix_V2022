<?php


namespace App\Service;


class StatusService
{
    /*
     * Status member association
     */
    const MEMBER_ADMIN = "MEMBER_ADMIN";
    const MEMBER_OFFICE = "MEMBER_OFFICE";
    const MEMBER_REPRESENT_OTHER_MEMBER = "MEMBER_REPRESENT_OTHER_MEMBER";
    const MEMBER_GAMEMASTER = "MEMBER_GAMEMASTER";
    const MEMBER_PLAYER = "MEMBER_PLAYER";
    const MEMBER_REGISTER = "MEMBER_REGISTER";
    const MEMBER_NOT_REGISTER = "MEMBER_NOT_REGISTER";
    const MEMBER_INACTIVE = "MEMBER_INACTIVE";
    const MEMBER_ACTIVE = "MEMBER_ACTIVE";
    const MEMBER_NEW = "MEMBER_NEW";
    const MEMBER_OLD = "MEMBER_OLD";

    /*
     * Status game
     */
    const NEW_GAME = "NEW_GAME";
    const ACTIVE_GAME = "ACTIVE_GAME";
    const FINISH_GAME = "FINISH_GAME";

    /*
     * Selection creneaux
     */
    const SLOT_WEEK_PAIR="CRENEAU_1";
    const SLOT_WEEK_ODD="CRENEAU_2";

    const SLOT_AFTERNOON="APREM";
    const SLOT_EVENING="SOIR";
}