<template>
    <p v-if="message.error" class="card w-60">
        <FlashMessage type="error" :message="message.error" />
    </p>
    <div v-else class="form_page">
        <div class="title">
            <h1 class="title-label large text-center">
                <p v-if="message.success" class="card-success text-dark bordered-text success">
                    {{ message.success }}
                </p>
                <p v-else class="text-dark bordered-text">
                    Donne les thunes fréro <br/>
                    <small v-if="transaction">Il nous faut {{ transaction.amount }} €</small>
                </p>
            </h1>
        </div>
        <div class="card w-60">
            <Loader v-if="isLoading || isSubmitted"/>

            <form id="payment-form" class="card-form" @submit.prevent="handleSubmit">
                <div id="payment-element">
                    <!-- Stripe injecte l'élément de formulaire ici -->
                </div>
                <div class="flex_form_group">
                    <button class="flash flash--info action-button inline" type="submit">Pay</button>
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
import FlashMessage from "../components/FlashMessage.vue";

export default defineComponent({
    name: 'Home',
    data() {
        return {
            token: sessionStorage.getItem('decryptId'),
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
        FlashMessage,
        Loader
    },
    created() {
        if (this.token === null) {
            this.$router.push('/signin')
        }

        HttpRequest.get('/stripe_create?decryptId='+this.token, null).then(response => {
            if(response.error) {
                this.message.error = response.message;
            } else {
                this.stripe = Stripe(env.STRIPE_PUBLIC_KEY);

                const options = {
                    clientSecret: response.clientSecret,
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
                this.transaction = response.transaction
            }
        }).catch(error => {
            this.message.error = error.response.data.message ?? error.response.data.detail
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
                                country: window.navigator.language,
                                postal_code: 'never'
                            }
                        }
                    },
                }
            });

            if (error === undefined) {
                HttpRequest.get('/payment_success?pm=' + paymentIntent.payment_method + '&decryptId='+this.token, null).then((res) => {
                    this.message.success = res.message
                }).catch((err) => {
                    this.message.error = err.response.data.message ?? err.response.data.detail
                })
            }
            this.isSubmitted = false;
        }
    }
});
</script>