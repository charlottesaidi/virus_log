import {parseJwt} from "../../../core/services/utils/string";
import router from "../../../router/router";

export function logout() {
    sessionStorage.removeItem('token');
    return router.push('/login')
}

export function isLogged() {
    return sessionStorage.getItem('token') ?? null
}

export function isTokenExpired(token: string | null) {
    const parsedToken = parseJwt(token);
    if (parsedToken.exp * 1000 < Date.now()) {
        sessionStorage.removeItem('token');
        return router.push('/login')
    }
}

export function isAdmin(token: string | null) {
    const roles = parseJwt(token).roles;
    return !!roles.includes('ROLE_ADMIN');
}