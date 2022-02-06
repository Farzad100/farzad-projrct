import Vue from 'vue';
import Cookies from 'js-cookie';
import VueRouter from 'vue-router';
Vue.use(VueRouter);

// Module Routes
import auth from './modules/auth';
import pages from './modules/pages';
import dashboard from './modules/dashboard';

const routes = [
  {
    path: '/',
    component: () => import('../App.vue'),
    children: [

      /**
       * Default Layout
       * 
       * ---------------------------------------------------
       */
      {
        path: '/',
        component: () => import('../layouts/Default.vue'),
        children: [
          ...pages,
        ]
      },

      /**
       * Dashboard Layout
       * 
       * ---------------------------------------------------
       */
      {
        path: '/dashboard',
        component: () => import('../layouts/Dashboard'),
        meta: { requiresAuth: true },
        children: [
          ...dashboard
        ]
      },

      /**
       * Auth Layout
       * 
       * ---------------------------------------------------
       */
      {
        path: '/',
        component: () => import('../layouts/Auth'),
        children: [
          ...auth
        ]
      },

      /**
       * Outside Layouts
       * 
       * ---------------------------------------------------
       */
      {
        path: '/orders/:id/contract',
        name: 'contract',
        meta: { requiresAuth: true },
        component: () => import('../pages/Contract.vue')
      },
      {
        path: '/orders/:id/card-stick/:address/:password',
        name: 'cardStick',
        meta: { requiresAuth: true },
        component: () => import('../pages/CardStick.vue'),
      },
      {
        path: '/qrTestChk',
        name: 'qr',
        component: () => import('../pages/qrcode.vue')
      },
      {
        path: '*',
        name: 'NotFound',
        component: () => import('../pages/NotFound')
      }
    ]
  }
];

const router = new VueRouter({
  mode: 'history',
  scrollBehavior() {
    return {x: 0, y: 0};
  },
  routes
});


router.beforeEach((to, from, next) => {
  const cookies = Cookies.get('access_token');
  const isLoggedIn = !!cookies;

  if (to.matched.some(r => r.meta.requiresAuth)) {

    if (isLoggedIn) {
      next();
    } else {
      Cookies.set('rd_route_path', to.path);
      next('/login');
    }

  } else {

    if (to.matched.some(r => r.meta.notAllowWithToken) && isLoggedIn) {
      next('/dashboard');
    }
    next();

  }
});

export default router;
