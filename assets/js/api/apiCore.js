import jwtDecode from 'jwt-decode';
import axios from 'axios';
import configApp from './config';

// content type
//axios.defaults.headers.post['Content-Type'] = 'application/json';
axios.defaults.headers.patch['Content-Type'] = 'application/json';
axios.defaults.headers.put['Content-Type'] = 'application/json';

axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        // Any status codes that falls outside the range of 2xx cause this function to trigger
        let message;

        if (error && error.response && error.response.status === 404) {
            // window.location.href = '/not-found';
        } else if (error && error.response && error.response.status === 403) {
            window.location.href = '/access-denied';
        } else {
            switch (error.response.status) {
                case 401:
                    message = 'Invalid credentials';
                    break;
                case 403:
                    message = 'Access Forbidden';
                    break;
                case 404:
                    message = 'Sorry! the data you are looking for could not be found';
                    break;
                default: {
                    message =
                        error.response && error.response.data ? error.response.data['message'] : error.message || error;
                }
            }
            return Promise.reject(message);
        }
    }
);

const setAuthorization = (token) => {
    if (token) axios.defaults.headers['Authorization'] = 'Bearer ' + token;
    else delete axios.defaults.headers['Authorization'];
    if (token) axios.defaults.headers['X-Bearer-Token'] = token;
    else delete axios.defaults.headers['X-Bearer-Token'];
};

class APICore {
    /**
     * Fetches data from given url
     */
    get(url, params) {
        const config = {
            headers: {
                ...axios.defaults.headers,
                'content-type': 'application/json',
                'Accept': '*/*'
            },
        };
        let response;
        if (params) {
            var queryString = params
                ? Object.keys(params)
                    .map((key) => key + '=' + params[key])
                    .join('&')
                : '';
            response = axios.get(`${configApp.API_URL+url}?${queryString}`, params, config);
        } else {
            response = axios.get(`${configApp.API_URL+url}`, params, config);
        }
        return response;
    };

    getFile(url, params) {
        let response;
        if (params) {
            var queryString = params
                ? Object.keys(params)
                    .map((key) => key + '=' + params[key])
                    .join('&')
                : '';
            response = axios.get(`${configApp.API_URL+url}?${queryString}`, { responseType: 'blob' });
        } else {
            response = axios.get(`${configApp.API_URL+url}`, { responseType: 'blob' });
        }
        return response;
    };

    getMultiple (urls, params) {
        const reqs = [];
        let queryString = '';
        if (params) {
            queryString = params
                ? Object.keys(params)
                    .map((key) => key + '=' + params[key])
                    .join('&')
                : '';
        }

        for (const url of urls) {
            reqs.push(axios.get(`${configApp.API_URL+url}?${queryString}`));
        }
        return axios.all(reqs);
    };

    /**
     * post given data to url
     */
    create(url, data) {
        const config = {
            headers: {
                ...axios.defaults.headers,
                'content-type': 'application/ld+json',
                'Accept': '*/*'
            },
        };
        return axios.post(configApp.API_URL+url, data, config);
    };

    /**
     * Updates patch data
     */
    updatePatch(url, data){
        return axios.patch(configApp.API_URL+url, data);
    };

    /**
     * Updates data
     */
    update(url, data){
        return axios.put(configApp.API_URL+url, data);
    };

    /**
     * Deletes data
     */
    delete (url){
        return axios.delete(configApp.API_URL+url);
    };

    /**
     * post given data to url with file
     */
    createWithFile (url, data){
        const formData = new FormData();
        for (const k in data) {
            formData.append(k, data[k]);
        }

        const config = {
            headers: {
                ...axios.defaults.headers,
                'content-type': 'multipart/form-data',
                'Accept': '*/*'
            },
        };
        return axios.post(configApp.API_URL+url, formData, config);
    };

    /**
     * post given data to url with file
     */
    updateWithFile(url, data) {
        const formData = new FormData();
        for (const k in data) {
            formData.append(k, data[k]);
        }

        const config = {
            headers: {
                ...axios.defaults.headers,
                'content-type': 'multipart/form-data',
            },
        };
        return axios.patch(configApp.API_URL+url, formData, config);
    };

    }


export { APICore, setAuthorization};
