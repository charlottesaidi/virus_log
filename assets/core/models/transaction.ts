import {User} from "./user";

export interface Transaction {
    id: number;
    amount: number;
    label: string;
    user: User;
    paymentStatus: string;
    createdAt: Date;
    paymentMethod: Object;
}