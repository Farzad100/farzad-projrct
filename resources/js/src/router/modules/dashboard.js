export default [
  {
    name: 'dashboard',
    path: ':role',
    meta: {
      title: 'داشبورد'
    },
    component: () => import('../../pages/dashboard/Dashboard')
  },
  {
    name: 'dash-orders',
    path: ':role/orders',
    meta: {
      title: 'سفارش‌ها'
    },
    component: () => import('../../pages/dashboard/order/List')
  },

  {
    name: 'dash-order-single',
    path: ':role/orders/:oid',
    component: () => import('../../pages/dashboard/order/Single')
  },
  {
    name: 'dash-new-order',
    path: ':role/new-order',
    meta: {
      title: 'ثبت سفارش جدید'
    },
    component: () => import('../../pages/dashboard/order/Create')
  },

  {
    name: 'dash-profile',
    path: ':role/profile',
    meta: {
      title: 'حساب کاربری'
    },
    component: () => import('../../pages/dashboard/Profile')
  },

  {
    name: 'dash-inbox',
    path: ':role/inbox',
    meta: {
      title: 'اطلاعیه‌ها'
    },
    component: () => import('../../pages/dashboard/inbox/List')
  },

  {
    name: 'dash-inbox-single',
    path: ':role/inbox/:id',
    component: () => import('../../pages/dashboard/inbox/Single')
  },

  {
    name: 'dash-transactions',
    path: ':role/transactions',
    meta: {
      title: 'تراکنش‌ها'
    },
    component: () => import('../../pages/dashboard/Transactions')
  },

  {
    name: 'dash-sms',
    path: ':role/sms',
    meta: {
      title: 'پیامک‌ها'
    },
    component: () => import('../../pages/dashboard/Sms')
  },

  {
    name: 'dash-installments',
    path: ':role/installments',
    meta: {
      title: 'اقساط'
    },
    component: () => import('../../pages/dashboard/Installments')
  },

  {
    name: 'dash-users',
    path: ':role/users',
    meta: {
      title: 'کاربران'
    },
    component: () => import('../../pages/dashboard/Users')
  },

  {
    name: 'dash-shops',
    path: ':role/shops',
    meta: {
      title: 'فروشگاه‌ها'
    },
    component: () => import('../../pages/dashboard/shop/List')
  },

  {
    name: 'dash-shops-single',
    path: 'admin/shops/:id',
    component: () => import('../../pages/dashboard/shop/Single')
  },
  {
    name: 'dash-cardboard-validation',
    path: 'shops-cardboard-validation',
    component: () => import('../../pages/dashboard/shop/CardBoard')
  },

  {
    name: 'dash-organs',
    path: ':role/organs',
    meta: {
      title: 'سازمان‌ها'
    },
    component: () => import('../../pages/dashboard/organ/List')
  },
  {
    name: 'dash-organs-single',
    path: 'admin/organs/:id',
    component: () => import('../../pages/dashboard/organ/Single')
  },
  {
    name: 'dash-cardboard-organ-orders',
    path: 'organs-cardboard-orders',
    component: () => import('../../pages/dashboard/organ/CardBoard')
  },

  {
    name: 'dash-discounts',
    path: ':role/discounts',
    meta: {
      title: 'تخفیف‌ها'
    },
    component: () => import('../../pages/dashboard/Discounts')
  },

  {
    name: 'dash-cards',
    path: ':role/cards',
    meta: {
      title: 'قسطاکارت‌ها'
    },
    component: () => import('../../pages/dashboard/GhestaCards')
  },

  {
    name: 'dash-incomes',
    path: ':role/incomes',
    meta: {
      title: 'درآمد‌ها و کارمزدها'
    },
    component: () => import('../../pages/dashboard/Incomes')
  },

  /* {
    name: 'dash-comments',
    path: ':role/comments',
    meta: {
      title: 'نظرات'
    },
    component: () => import('../../pages/dashboard/Comments')
  }, */

  {
    name: 'dash-links',
    path: ':role/links',
    meta: {
      title: 'لینک‌های کوتاه'
    },
    component: () => import('../../pages/dashboard/Links')
  }, 

  {
    name: 'dash-settings',
    path: ':role/settings',
    meta: {
      title: 'تنظیمات'
    },
    component: () => import('../../pages/dashboard/Settings')
  }, 

  {
    name: 'dash-accounting',
    path: ':role/accounting',
    meta: {
      title: 'کارتابل حسابداری'
    },
    component: () => import('../../pages/dashboard/Accounting')
  },

  /* {
    name: 'dash-user-mobilechange',
    path: 'user/mobile-change',
    meta: {
      title: 'تغییر شماره موبایل'
    },
    component: () => import('../../pages/dashboard/MobileChange')
  }, */ 
];
