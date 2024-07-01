import { APICore } from './apiCore';
import config from './config';

class UserApi {
    constructor() {
        this.apiCore = new APICore();
    }

    getUsers(role) {
        const url = `${config.API_URL}/admin/manage/users`;
        return this.apiCore.get(url, { role });
    }

    deleteUser(idUser) {
        const url = `${config.API_URL}/admin/manage/user/${idUser}/delete`;
        return this.apiCore.delete(url);
    }

    updateUser(url, data) {
        return this.apiCore.update(url, data);
    }
}

export { UserApi };
