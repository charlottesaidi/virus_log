<template>
    <div class="form_page">
      <div class="title">
        <h2> Log In </h2>
      </div>
      <div class="card w-40">
        <form class="card-form" @submit.prevent="submit(user)">
          <p v-if="error" class="card-error">
            {{ error }}
          </p>
          <div class="input">
            <input type="text" class="input-field" v-model="user.username" required/>
            <label class="input-label" for="username">Email</label>
          </div>
          <div class="input">
            <input type="password" class="input-field" v-model="user.password" required/>
            <label class="input-label" for="password">Key</label>
          </div>
          <div class="action">
            <button class="action-button">LOGIN</button>
          </div>
        </form>
      </div>
    </div>
</template>

<script lang="ts">
import HttpRequest from '../core/services/http/HttpRequest';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'Login',
  data() {
    return {
      user: {
        username: null,
        password: null
      },
      error: null as string | null
    }
  },
  methods: {
    submit(user: any): void {
        HttpRequest.post('/login_check', user)
            .then((response: any) => {
                sessionStorage.setItem('token', response.data.token)
                this.$router.push('/')
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
