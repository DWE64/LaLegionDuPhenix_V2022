import { APICore } from './apiCore';

class UserApi {
    constructor() {
        this.apiCore = new APICore();
    }
    getUsers() {
        const url = `/api/admin/manage/users`;
        return this.apiCore.get(url);
    }

    deleteUser(idUser){
        const url = `/api/admin/manage/user/${idUser}/delete`;
        return this.apiCore.delete(url);
    }

    updateUser(url, data){
        return this.apiCore.update(url, data);
    }
}

export { UserApi };
