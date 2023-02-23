import { AxiosResponse } from 'axios';
import http from './http-common';

class HttpRequest {
    get(url: string, token: string | null): Promise<AxiosResponse<any, any>> {
        return http.get(url, {
            headers: {
                "Authorization": "Bearer " + token
            }
        });
    }

    post(url: string, data: any): Promise<AxiosResponse<any, any>> {
        return http.post(url, data)
    }
}

export default new HttpRequest();
