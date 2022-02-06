export default [
  {
    name: 'login',
    path: '/login', aliases: ['/lgn', '/signin', '/sign-in'],
    meta: {
      notAllowWithToken: true,
      title: 'ورود به حساب'
    },
    component: () => import('@/pages/auth/Login')
  },
  {
    name: 'register',
    path: '/register',
    meta: {
      notAllowWithToken: true,
      title: 'ثبت نام'
    },
    component: () => import('@/pages/auth/Register.vue')
  },
  {
    name: 'organ-register',
    path: 'organ/register',
    meta: {
      title: 'ثبت نام سازمان'
    },
    component: () => import('@/pages/auth/Organ')
  },
  {
    name: 'seller-register',
    path: 'seller/register',
    meta: {
      title: 'ثبت نام فروشگاه'
    },
    component: () => import('@/pages/auth/Seller')
  },
  {
    name: 'reset',
    path: 'auth/reset-password',
    meta: {
      notAllowWithToken: true,
      title: 'بازیابی رمز عبور'
    },
    component: () => import('@/pages/auth/Reset')
  },
  {
    name: 'landing-auth',
    path: 'lnd/auth',
    component: () => import('@/pages/auth/Landings')
  }
];
