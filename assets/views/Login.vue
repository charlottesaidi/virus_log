<template>
    <div class="form_page">
      <div class="title">
        <h2 class="title-label large"> Log In </h2>
      </div>
      <div class="card w-40">
        <form class="card-form" @submit.prevent="submit(user)">
          <p v-if="error" class="card-error">
            {{ error }}
          </p>
          <div class="input">
            <input type="password" class="input-field" v-model="user.password" required/>
            <label class="input-label" for="password">Key</label>
          </div>
            <div class="input">
                <input type="text" class="input-field" v-model="user.username" required/>
                <label class="input-label" for="username">Ip</label>
            </div>
          <div class="action flex_form_group">
            <button class="flash flash--info action-button inline">LOGIN</button>
          </div>
        </form>
      </div>
    </div>
</template>

<script lang="ts">
import HttpRequest from '../core/services/http/HttpRequest';
import { defineComponent } from 'vue';
import {isLogged} from '../core/services/utils/auth';
import {AxiosError} from "axios";

export default defineComponent({
    name: 'Login',
    data() {
        return {
            token: isLogged(),
            user: {
                username: null,
                password: null
            },
            error: null as string | null
        }
    },
    methods: {
        submit(user: any): void {
            HttpRequest.post('/api/login_check', user)
                .then((res: any) => {
                    if(res instanceof AxiosError) {
                        this.error = res.response?.data.message;
                    } else {
                        sessionStorage.setItem('token', res.data.token)
                        this.$router.push('/')
                    }
                })
                .catch((error : any) => {
                    this.error = error.response.data.message ?? error.response.data.detail;
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
