<template>
    <div class="form_page">
        <div class="title">
            <h1 class="title-label large text-center">
                <p v-if="message.success" class="card-success">
                    {{ message.success }}
                </p>
                <p v-else>
                    Donne les thunes fréro <br/>
                    <small v-if="transaction">Il nous faut {{ transaction.amount }} €</small>
                </p>
            </h1>
        </div>
        <div class="card w-60">
            <p v-if="message.error" class="card-error">
                {{ message.error }}
            </p>

            <Loader v-if="isLoading || isSubmitted"/>

            <form id="payment-form" class="card-form" @submit.prevent="handleSubmit">
                <div id="payment-element">
                    <!-- Stripe injecte l'élément de formulaire ici -->
                </div>
                <div class="payment_form_group">
                    <button class="action-button payment" type="submit">Pay</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import {ref, defineComponent} from "vue"
import {env} from "../env";
import Loader from "../components/Loader.vue";
import {localeToCountryCode} from "../core/services/utils/string";
import HttpRequest from "../core/services/http/HttpRequest";
import {isLogged, isTokenExpired} from "../core/services/utils/auth";

export default defineComponent({
    name: 'Home',
    data() {
        return {
            token: isLogged(),
            isLoading: true,
            isSubmitted: false,
            transaction: null,
            stripe: ref(null),
            elements: ref(null),
            appearance: {
                theme: 'night',
                labels: 'floating'
            },
            message: {
                success: null,
                error: null
            }
        }
    },
    components: {
        Loader
    },
    created() {
        if(this.token) {
            isTokenExpired(this.token);
        }

        if (this.token === null) {
            this.$router.push('/login')
        }

        HttpRequest.get('stripe_create', this.token).then(response => {
            this.stripe = Stripe(env.STRIPE_PUBLIC_KEY);

            const options = {
                clientSecret: response.data.clientSecret,
                appearance: this.appearance
            }

            this.elements = this.stripe.elements(options);
            const paymentElement = this.elements.create('payment', {
                fields: {
                    billingDetails: {
                        address: {
                            country: 'never',
                            postalCode: 'never',
                        }
                    }
                }
            });
            this.isLoading = false;
            paymentElement.mount('#payment-element');
            this.transaction = response.data.transaction
        }).catch(error => {
            this.message.error = error.response.data.message
        })
    },
    methods: {
        async handleSubmit(e) {
            e.preventDefault();
            this.isSubmitted = true;

            const {error, paymentIntent} = await this.stripe.confirmPayment({
                elements: this.elements,
                redirect: "if_required",
                confirmParams: {
                    payment_method_data: {
                        billing_details: {
                            address: {
                                country: localeToCountryCode(window.navigator.language),
                                postal_code: 'never'
                            }
                        }
                    },
                }
            });

            if (error === undefined) {
                HttpRequest.get('payment_success?pm=' + paymentIntent.payment_method, this.token).then((res) => {
                    this.message.success = res.data.message
                }).catch((err) => {
                    this.message.error = err.response.data.message
                })
                this.isSubmitted = false;
            } else {
                this.message.error = 'Une erreur est survenue'
            }
        }
    }
});
</script>