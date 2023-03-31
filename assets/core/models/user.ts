import {RolesEnum} from '../enums/rolesEnum';

export interface User {
    id: number;
    email: string;
    roles: RolesEnum[];
    macAddress: string;
    ip: string;
}