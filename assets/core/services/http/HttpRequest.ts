import {config} from './http-common';
import {IndexResponse} from "@/core/models/indexResponse";
import {CommonResponse} from "@/core/models/commonResponse";
import {User} from "@/core/models/user";
import {LoginResponse} from "@/core/models/loginResponse";

class HttpRequest {

    get(url: string, token: string | null): Promise<CommonResponse> {
        return fetch(config.url+url, {
            method: 'GET',
            headers: {
                "Authorization": "Bearer " + token,
                "Content-type": "application/json"
            }
        }).then(response => response.json());
    }

    post(url: string, data: any): Promise<CommonResponse> {
        return fetch(config.url+url, {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                "Content-type": "application/json"
            }
        }).then(response => response.json());
    }

    fetchAllLogs(url: string, token: string | null): Promise<IndexResponse> {
        return fetch(config.url+url, {
            method: 'GET',
            headers: {
                "Authorization": "Bearer " + token,
                "Content-type": "application/json"
            }
        }).then(response => response.json());
    }

    login(url: string, data: User): Promise<LoginResponse> {
        return fetch(config.url+url, {
            method: 'POST',
            headers: {
                "Content-type": "application/json"
            },
            body: JSON.stringify(data)
        }).then(response => response.json());
    }
}

export default new HttpRequest();
