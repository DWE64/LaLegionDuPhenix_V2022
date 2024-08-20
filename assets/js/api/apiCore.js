import axios from 'axios';
import configApp from './config';

axios.defaults.headers.patch['Content-Type'] = 'application/json';
axios.defaults.headers.put['Content-Type'] = 'application/json';

axios.interceptors.response.use(
    (response) => response,
    (error) => {
        let message;

        if (error.response) {
            switch (error.response.status) {
                case 401:
                    message = 'Invalid credentials';
                    break;
                case 403:
                    window.location.href = '/access-denied';
                    break;
                case 404:
                    message = 'Sorry! the data you are looking for could not be found';
                    break;
                default:
                    message = error.response.data ? error.response.data.message : error.message;
            }
            return Promise.reject(message);
        }
        return Promise.reject(error.message);
    }
);

const setAuthorization = (token) => {
    if (token) {
        axios.defaults.headers['Authorization'] = `Bearer ${token}`;
        axios.defaults.headers['X-Bearer-Token'] = token;
    } else {
        delete axios.defaults.headers['Authorization'];
        delete axios.defaults.headers['X-Bearer-Token'];
    }
};

class APICore {
    get(url, params) {
        const config = {
            headers: {
                ...axios.defaults.headers,
                'Content-Type': 'application/json',
                'Accept': '*/*'
            }
        };
        const queryString = params ? Object.keys(params).map((key) => `${key}=${params[key]}`).join('&') : '';
        return axios.get(`${url}?${queryString}`, config);
    }

    getFile(url, params) {
        const queryString = params ? Object.keys(params).map((key) => `${key}=${params[key]}`).join('&') : '';
        return axios.get(`${url}?${queryString}`, { responseType: 'blob' });
    }

    getMultiple(urls, params) {
        const queryString = params ? Object.keys(params).map((key) => `${key}=${params[key]}`).join('&') : '';
        const requests = urls.map((url) => axios.get(`${url}?${queryString}`));
        return axios.all(requests);
    }

    create(url, data) {
        const config = {
            headers: {
                ...axios.defaults.headers,
                'Content-Type': 'application/ld+json',
                'Accept': '*/*'
            }
        };
        return axios.post(url, data, config);
    }

    updatePatch(url, data) {
        return axios.patch(url, data);
    }

    update(url, data) {
        return axios.put(url, data);
    }

    delete(url) {
        return axios.delete(url);
    }

    createWithFile(url, data) {
        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }
        const config = {
            headers: {
                ...axios.defaults.headers,
                'Content-Type': 'multipart/form-data',
                'Accept': '*/*'
            }
        };
        return axios.post(url, formData, config);
    }

    updateWithFile(url, data) {
        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }
        const config = {
            headers: {
                ...axios.defaults.headers,
                'Content-Type': 'multipart/form-data'
            }
        };
        return axios.patch(url, formData, config);
    }
}

export { APICore, setAuthorization };
