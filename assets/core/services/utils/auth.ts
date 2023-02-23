import {parseJwt} from "../../../core/services/utils/string";

export function isTokenExpired(token: string | null) {
    const parsedToken = parseJwt(token);
    if (parsedToken.exp * 1000 < Date.now()) {
        sessionStorage.removeItem('token');
        token = null;
    }
}