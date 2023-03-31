import {RolesEnum} from '../enums/rolesEnum';

export interface User {
    id: number;
    email: string;
    roles: RolesEnum[];
}