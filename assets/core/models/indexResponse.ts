import {Transaction} from "@/core/models/transaction";
import {Log} from "@/core/models/log";

export interface IndexResponse {
    transactions: Transaction[];
    logs: Log[];
    infectedTerminals: number;
    totalAmount: number;
}