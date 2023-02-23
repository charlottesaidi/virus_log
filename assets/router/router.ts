import { createRouter, createWebHistory, Router, RouteRecordNormalized, RouteRecordRaw } from 'vue-router';
import Home from '../views/Home.vue';
import Payment from "../views/Payment.vue";
import Login from "../views/Login.vue";
import NotFound from "../views/NotFound.vue";

const routes : Array<RouteRecordRaw> = [
  {
    path: '/',
    name: 'home',
    component: Home
  },
  {
    path: '/payment',
    name: 'payment',
    component: Payment
  },
  {
    path: '/login',
    name: 'login',
    component: Login
  },
  {
    path: "/:catchAll(.*)",
    component: NotFound
  }
]

const router : Router = createRouter({
  history: createWebHistory('/'),
  routes
})

router.beforeEach((to, from, next) => {
  const nearestWithTitle : RouteRecordNormalized | undefined = to.matched.slice().reverse().find(r => r.meta && r.meta.title);
  const nearestWithMeta : RouteRecordNormalized | undefined = to.matched.slice().reverse().find(r => r.meta && r.meta.metaTags);
  const previousNearestWithMeta : RouteRecordNormalized | undefined = from.matched.slice().reverse().find(r => r.meta && r.meta.metaTags);

  if(nearestWithTitle !== undefined) {
    document.title = typeof nearestWithTitle.meta.title === 'string' ? nearestWithTitle.meta.title : '';
  } else if(previousNearestWithMeta) {
    document.title = typeof previousNearestWithMeta.meta.title === 'string' ? previousNearestWithMeta.meta.title : '';
  }

  Array.from(document.querySelectorAll('[data-vue-router-controlled]')).map(el => el.parentNode!.removeChild(el));

  if(!nearestWithMeta) return next();

  if (Array.isArray(nearestWithMeta.meta.metaTags)) {
    nearestWithMeta.meta.metaTags.map((tagDef: { [x: string]: string; }) => {
      const tag : HTMLMetaElement = <HTMLMetaElement>document.createElement('meta');
  
      Object.keys(tagDef).forEach(key => {
        tag.setAttribute(key, tagDef[key]);
      });
  
      tag.setAttribute('data-vue-router-controlled', '');
  
      return tag;
    })
    .forEach((tag: HTMLMetaElement) => document.head.appendChild(tag));
  }

  next();
});

export default router
