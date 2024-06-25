import {APICore} from "../apiCore";

class AuthApi {
    constructor() {
        this.apiCore = new APICore();
    }

    getAccessToken(params){
        const url='/api_keys/users/check_key';
        return this.apiCore.create(`${url}`, params);
    }

    getUser(idUser){
        const url='/users/'+idUser;
        return this.apiCore.get(`${url}`);
    }
}

export {AuthApi}