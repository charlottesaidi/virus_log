<template>

    <div class="layout_horizontal">
        <Header/>
        <div class="home">
            <h1>Logs</h1>

            <p v-if="error">{{ error }}</p>

            <div v-else>
                {{ response }}
            </div>

        </div>
    </div>

</template>

<script lang="ts">
import {defineComponent} from 'vue';
import Header from '../components/commons/Header.vue';
import Login from "./Login.vue";
import HttpRequest from '../core/services/http/HttpRequest';
import { isTokenExpired } from '../core/services/utils/auth';

export default defineComponent({
    name: 'Home',
    data() {
        return {
            error: null as string | null,
            token: '' as string | null,
            response: {} as any
        }
    },
    components: {
        Header,
        Login
    },
    methods: {
        call(token: string | null): void {
            HttpRequest.get('/logs', token)
                .then((response: any) => {
                    this.response = response.data
                    if([400, 404].includes(response.data.status_code)) {
                        this.$router.push('/*')
                    }
                })
                .catch((error: any) => {
                    this.error = error;
                });
        }
    },
    created() {
        this.token = sessionStorage.getItem('token') ?? null;

        if(this.token) {
            isTokenExpired(this.token);
        }

        if (this.token === null) {
            this.$router.push('/login')
        }
        this.call(this.token);
    }
});
</script>

<style lang="scss" scoped>
// Your scoped scss
</style>
