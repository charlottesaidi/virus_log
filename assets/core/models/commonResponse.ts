import {Transaction} from "@/core/models/transaction";
import {User} from "@/core/models/user";
import {Log} from "@/core/models/log";

export interface CommonResponse {
    message: string;
    success: boolean;
    error: boolean;
    code: number;
    transaction: Transaction;
    user: User;
    log: Log;
}