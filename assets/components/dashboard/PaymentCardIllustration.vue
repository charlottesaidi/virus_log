<template>
    <UnionPay v-if="isCard('union')" />
    <Jcb v-else-if="isCard('jcb')" />
    <Discover v-else-if="isCard('discover')" />
    <MasterCard v-else-if="isCard('master')" />
    <AmericanExpress v-else-if="isCard('american')" />
    <Visa v-else />
</template>

<script>
import Visa from "../icons/Visa.vue";
import MasterCard from "../icons/MasterCard.vue";
import Discover from "../icons/Discover.vue";
import Jcb from "../icons/Jcb.vue";
import UnionPay from "../icons/UnionPay.vue";
import AmericanExpress from "../icons/AmericanExpress.vue";

export default {
    name: "PaymentCardIllustration",
    components: {
        Visa,
        MasterCard,
        Discover,
        Jcb,
        UnionPay,
        AmericanExpress
    },
    props: {
        card: String
    },
    methods: {
        isCard(option) {
            return this.card.toLowerCase().includes(option.toLowerCase());
        }
    }
}
</script>

<style scoped>
</style>