<template>
    <div class="form_page">
      <div class="title">
        <h2 class="title-label large"> Log In </h2>
      </div>
      <div class="card w-40">
        <form class="card-form" @submit.prevent="submit(user)">
          <p v-if="error" class="my-3">
              <FlashMessage type="error" :message="error" />
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
import FlashMessage from "../components/FlashMessage.vue";

export default defineComponent({
    name: 'Login',
    components: {FlashMessage},
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
            HttpRequest.login('/api/login_check', user)
                .then((res) => {
                    if(res.code) {
                        this.error = res.message;
                    } else {
                        sessionStorage.setItem('token', res.token)
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
