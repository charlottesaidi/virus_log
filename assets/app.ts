import './styles/app.scss';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router/router';
import moment from 'moment'

const app = createApp(App)

moment.locale('fr')
app.config.globalProperties.$moment = moment;

app.use(router)
app.mount('#app');
