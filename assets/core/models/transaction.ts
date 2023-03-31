import {User} from "./user";

export interface Transaction {
    id: number;
    number: number;
    label: string;
    user: User;
    paymentStatus: string;
    createdAt: Date;
}